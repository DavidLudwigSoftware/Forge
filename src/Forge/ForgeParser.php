<?php

namespace Forge;

use Forge\ForgeToken as Token;

use Forge\Exception\InvalidPropertyError;
use Forge\Exception\InvalidSyntaxError;
use Forge\Exception\UnknownStatementError;

class ForgeParser
{
    /**
     * Store the ForgeEngine instance
     * @var Forge\ForgeEngine
     */
    private $_forge;

    /**
     * The parts of the view
     * @var array
     */
    private $_parts;

    /**
     * The part buffer index
     * @var integer
     */
    private $_bufferIndex;

    /**
     * A list of the print statemnets
     * @var array
     */
    private $_prints;

    /**
     * A list of the properties
     * @var array
     */
    private $_props;

    /**
     * The storage for the parser
     * @var Forge\ForgeStorage
     */
    private $_storage;

    /**
     * The list of tokens received from the lexer
     * @var array
     */
    private $_tokens;

    /**
     * The token count
     * @var integer
     */
    private $_tokenCount;

    /**
     * The current token index
     * @var integer
     */
    private $_index;


    /**
     * Create a ForgeParser
     * @param Forge\ForgeEngine $forge An instance of the ForgeEngine
     */
    public function __construct(ForgeEngine $forge)
    {
        $this->_forge = $forge;
        $this->loadPrintStatements();

        $this->loadProperties();

        $this->_parts = array([]);
        $this->_storage = new ForgeParserStorage();
    }

    /**
     * Load the print statements
     * @return void
     */
    protected function loadPrintStatements()
    {
        $this->_prints = require __DIR__ . '/Config/printstatements.php';
    }

    /**
     * Load the properties
     * @return void
     */
    protected function loadProperties()
    {
        $this->_props = Null;

        $props = require __DIR__ . '/Config/properties.php';

        foreach ($props as $key => $prop)

            $this->resolveProperty($key, $prop);
    }

    /**
     * Resolve a loaded property
     * @param  string $key  The forge property name
     * @param  string $prop The class string
     * @return void
     */
    protected function resolveProperty(string $key, string $prop)
    {
        $parts = explode('@', $prop);

        if (count($parts) != 2)

            $this->invalidProperty($prop);

        $reflection = new \ReflectionMethod($parts[0], $parts[1]);

        $this->_props[$key] = $reflection;
    }

    /**
     * Parse the given content
     * @param  string $content Html containing Forge code
     * @return array           The view's parts
     */
    public function parse(string $content)
    {
        $this->_bufferIndex = 0;

        $lexer = new ForgeLexer();

        $this->_tokens = $lexer->analyze($content);
        $this->_tokenCount = count($this->_tokens);

        for ($this->_index = 0; $this->_index < $this->_tokenCount; $this->next())
        {
            $token = $this->token();

            if ($token->type() == Token::HTML)

                $this->htmlToken($token);

            elseif ($token->type() == Token::PRINT_STATEMENT)

                $this->printToken($token);

            elseif ($token->type() == Token::PROPERTY)

                $this->propertyToken($token);

            elseif ($token->type() == Token::PARAMETERS)

                $this->parametersToken($token);

        }

        if ($this->_bufferIndex != 0)

            throw new ParserBufferError(
                "There are buffers that have not been closed during parsing!"
            );

        return $this->parts();
    }

    /**
     * Parse an Html token
     * @param  Forge\ForgeToken $token The current token
     * @return void
     */
    public function htmlToken($token)
    {
        $this->addPart($token->content());
    }

    /**
     * Parse a Print Statement token
     * @param  Forge\ForgeToken $token The current token
     * @return void
     */
    public function printToken($token)
    {
        $symbols = $token->content()->symbols();
        $content = $token->content()->content();

        $statement = new $this->_prints[$symbols]($symbols, $content);

        $this->addPart('<?php echo ' . $statement->display() . ' ?>');
    }

    /**
     * Parse a Property token
     * @param  Forge\ForgeToken $token The current token
     * @return void
     */
    public function propertyToken($token)
    {
        $name = $token->content();

        if (!isset($this->_props[$name]))

            $this->unknownProperty($name);

        $method = $this->_props[$name];

        $class = $method->getDeclaringClass()->newInstanceArgs([$this]);

        $result = Null;

        if ($method->getNumberOfParameters() > 0)
        {
            $this->next();
            $params = $this->token(0);

            if ($params->type() == Token::PARAMETERS)
            {
                $result = $method->invokeArgs($class, [$params->content()]);
            }
            else
            {
            }

        }
        elseif ($method->getNumberOfParameters() == 0)
        {
            $result = $method->invoke($class);
        }

        if ($result)

            $this->addPart($result);
    }

    /**
     * Parse a Parameters token
     * @param  Forge\ForgeToken $token The current token
     * @return void
     */
    public function parametersToken($token)
    {
        throw new InvalidSyntaxError(
            "Unexpected Parameters '" . $token->content() . "'"
        );
    }

    /**
     * Add a part to the current part buffer
     * @param mixed $part The part to add
     */
    public function addPart($part)
    {
        $this->_parts[$this->_bufferIndex][] = $part;
    }

    /**
     * Add multiple parts to the current part buffer
     * @param array $parts The list of parts to add
     */
    public function addParts(array $parts)
    {
        foreach ($parts as $part)

            $this->addPart($part);
    }

    /**
     * Get the parts from the current part buffer
     * @return array
     */
    public function parts()
    {
        return $this->_parts[$this->_bufferIndex];
    }

    /**
     * Open a new part buffer
     * @return void
     */
    public function newBuffer()
    {
        $this->_parts[++$this->_bufferIndex] = array();
    }

    /**
     * Close the current part buffer
     * @return array The parts from the closed buffer
     */
    public function closeBuffer()
    {
        $parts = $this->parts();

        $this->_parts[$this->_bufferIndex--] = Null;

        return $parts;
    }

    /**
     * Add a marker to the parts
     * @param  string $key  The key of the marker
     * @param  string $name The name of the marker
     * @return void
     */
    public function mark(string $key, string $name)
    {
        $this->addPart(new ForgeParserMarker($key, $name));
    }

    /**
     * Fill the markers with content
     * @param  string $key     The key of the markers to fill
     * @param  string $name    The name of the markers to fill
     * @param  [type] $content The content to fill the marker with
     * @return void
     */
    public function fillMarkers(string $key, string $name, $content)
    {
        foreach ($this->parts() as $part)

            if (is_a($part, 'Forge\ForgeParserMarker'))

                if ($part->key() == $key && $part->name() == $name)

                    $part->fill($content);
    }

    /**
     * Get the token offsetted by the index
     * @param  integer $offset  The offset of the index (0 = current token)
     * @return Forge\ForgeToken The token at the given offset
     */
    public function token($offset = 0)
    {
        if ($this->_index < $this->_tokenCount && $this->_index >= 0)

            return $this->_tokens[$this->_index + $offset];

        return $this->_tokens[$this->_tokenCount - 1];
    }

    /**
     * Increase the token index by the given offset
     * @param  integer $offset The amount to increase
     * @return void
     */
    public function next($offset = 1)
    {
        $this->_index += $offset;
    }

    /**
     * Decrease the token index by the given offset
     * @param  integer $offset The amount to decrease
     * @return void
     */
    public function prev($offset = 1)
    {
        $this->_index -= $offset;
    }

    /**
     * Get the current Forge Engine instance
     * @return Forge\ForgeEngine
     */
    public function forge()
    {
        return $this->_forge;
    }

    /**
     * Get the current storage
     * @return Forge\ForgeParserStorage
     */
    public function storage()
    {
        return $this->_storage;
    }

    /**
     * Invoke an invalid property error
     * @param  string $property The name of the property
     * @return void
     */
    public function invalidProperty(string $property)
    {
        throw new InvalidPropertyError("Invalid property assigned '$property'");
    }

    /**
     * Invoke an unknown statement error
     * @param  string $property The name of the property
     * @return void
     */
    public function unknownProperty(string $property)
    {
        throw new UnknownStatementError("Unknown property '$property'");
    }

}
