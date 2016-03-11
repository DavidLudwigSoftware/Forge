<?php

namespace Forge;

use Forge\ForgeToken as Token;

use Forge\Exception\InvalidPropertyError;
use Forge\Exception\InvalidSyntaxError;
use Forge\Exception\UnknownPropertyError;
use Forge\Exception\UnknownStatementError;

class ForgeParser
{
    private $_forge;

    private $_parts;
    private $_bufferIndex = 0;

    private $_prints;
    private $_props;
    private $_storage;

    private $_tokens;
    private $_tokenCount;
    private $_index;

    public function __construct(ForgeEngine $forge)
    {
        $this->_forge = $forge;
        $this->loadPrintStatements();

        $this->loadProperties();

        $this->_parts = array([]);
        $this->_storage = new ForgeParserStorage();
    }

    protected function loadPrintStatements()
    {
        $this->_prints = require __DIR__ . '/PrintStatement/printstatements.php';
    }

    protected function loadProperties()
    {
        $this->_props = Null;

        $props = require __DIR__ . '/Property/properties.php';

        foreach ($props as $key => $prop)

            $this->resolveProperty($key, $prop);
    }

    protected function resolveProperty(string $key, string $prop)
    {
        $parts = explode('@', $prop);

        if (count($parts) != 2)

            $this->invalidProperty($prop);

        $reflection = new \ReflectionMethod($parts[0], $parts[1]);

        $this->_props[$key] = $reflection;
    }

    public function parse(string $content)
    {
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

    public function htmlToken($token)
    {
        $this->addPart($token->content());
    }

    public function printToken($token)
    {
        $symbols = $token->content()->symbols();
        $content = $token->content()->content();

        $statement = new $this->_prints[$symbols]($symbols, $content);

        $this->addPart('<?php echo ' . $statement->display() . ' ?>');
    }

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

    public function parametersToken($token)
    {
        throw new InvalidSyntaxError(
            "Unexpected Parameters '" . $token->content() . "'"
        );
    }

    public function addPart($part)
    {
        $this->_parts[$this->_bufferIndex][] = $part;
    }

    public function addParts($parts)
    {
        foreach ($parts as $part)

            $this->addPart($part);
    }

    public function parts()
    {
        return $this->_parts[$this->_bufferIndex];
    }

    public function newBuffer()
    {
        $this->_parts[++$this->_bufferIndex] = array();
    }

    public function closeBuffer()
    {
        $parts = $this->parts();

        $this->_parts[$this->_bufferIndex--] = Null;

        return $parts;
    }

    public function mark($key, $name)
    {
        $this->addPart(new ForgeParserMarker($key, $name));
    }

    public function fillMark($key, $name, $content)
    {
        foreach ($this->parts() as $part)
        {
            if (is_a($part, 'Forge\ForgeParserMarker'))

                if ($part->key() == $key && $part->name() == $name)

                    $part->fill($content);

        }
    }

    public function token($offset = 0)
    {
        if ($this->_index < $this->_tokenCount && $this->_index >= 0)

            return $this->_tokens[$this->_index + $offset];

        return $this->_tokens[$this->_tokenCount - 1];
    }

    public function next($offset = 1)
    {
        $this->_index += $offset;
    }

    public function prev($offset = 1)
    {
        $this->_index -= $offset;
    }

    public function forge()
    {
        return $this->_forge;
    }

    public function storage()
    {
        return $this->_storage;
    }

    public function invalidProperty($property)
    {
        throw new InvalidPropertyError("Invalid property assigned '$property'");
    }

    public function unknownProperty($property)
    {
        throw new UnknownStatementError("Unknown property '$property'");
    }

}
