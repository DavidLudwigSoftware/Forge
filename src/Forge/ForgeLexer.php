<?php

namespace Forge;

use Forge\ForgeToken as Token;

use Forge\Exception\UnknownStatementError;
use Forge\Exception\InvalidSyntaxError;

class ForgeLexer
{
    /**
     * The content to parse
     * @var string
     */
    private $_content;

    /**
     * The length of the content
     * @var integer
     */
    private $_len;

    /**
     * The list of tokens
     * @var array
     */
    private $_tokens;

    /**
     * The previous token type
     * @var string
     */
    private $_prevType;

    /**
     * The data for the current token
     * @var string
     */
    private $_data;

    /**
     * The current character index
     * @var integer
     */
    private $_index;

    /**
     * The current line
     * @var integer
     */
    private $_line;

    /**
     * Where to separate Html from Forge code
     */
    private $_htmlSeparators = '@#({';


    /**
     * Create a Fore code lexer
     */
    public function __construct()
    {

    }

    /**
     * Analyze the content and create a list of tokens
     * @param  string $content The content to analyze
     * @return array           The list of tokens
     */
    public function analyze(string $content)
    {
        $this->_content = $content;
        $this->_len = strlen($content);

        $this->_tokens = array();
        $this->_type = Token::HTML;

        $this->_data = '';
        $this->_line = 1;

        for ($this->_index = 0; $this->_index < $this->_len; $this->next())
        {
            if ($this->comment())

                continue;

            elseif ($this->printStatement())

                $this->_prevType = Token::PRINT_STATEMENT;

            elseif ($this->property())

                $this->_prevType = Token::PROPERTY;

            elseif ($this->parameters())

                $this->_prevType = Token::PARAMETERS;

            elseif ($this->html())

                $this->_prevType = Token::HTML;
        }

        $this->_tokens[] = new Token(ForgeToken::EOF);

        return $this->_tokens;
    }

    /**
     * Analyze a print statement
     * @return bool
     */
    public function comment()
    {
        $c = $this->char();

        if ($c == '#')

            do
            {
                $this->next();
                $c = $this->char();

                if ($c == "\n")

                    return True;

            } while ($c !== Null);

        return False;
    }

    /**
     * Analyze a print statement
     * @return bool
     */
    public function printStatement()
    {
        $c = $this->char();

        if ($c == '{')
        {
            $string = Null;

            if (ctype_space($this->char(1)))

                return False;

            $symbol = $this->char(1);

            $this->next(2);
            $c = $this->char();

            while ($c !== Null)
            {
                if ($string === Null &&
                    !ctype_space($c) &&
                    $this->char(1) == '}')
                {
                    $this->_tokens[] = new Token(
                        Token::PRINT_STATEMENT,
                        new ForgePrintStatement($symbol . $c, $this->_data)
                    );

                    $this->_data = '';

                    $this->next();

                    return True;
                }
                elseif ($string === Null && ($c == "'" || $c == '"'))
                {
                    $string = $c;
                }
                elseif ($string !== Null && $c == '\\')
                {
                    $this->_data .= $c;

                    $this->next();
                    $c = $this->char();
                }
                elseif ($string !== Null && $c == $string)
                {
                    $string = Null;
                }

                if ($c !== Null)

                    $this->_data .= $c;

                $this->next();
                $c = $this->char();
            }

            $this->unexpectedEof();
        }

        return False;
    }

    /**
     * Analyze a property
     * @return bool
     */
    public function property()
    {
        $c = $this->char();


        if ($c == '@')
        {
            $this->next();
            $c = $this->char();
            $this->checkProperty();

            while ($c !== Null)
            {
                $nc = $this->char(1);

                if (!(ctype_alpha($nc) || ctype_digit($nc) || $nc == '_'))
                {
                    $this->_data .= $c;

                    break;
                }
                $this->_data .= $c;
                $this->next();
                $c = $this->char();
            }

            $this->_tokens[] = new Token(Token::PROPERTY, $this->_data);

            $this->_data = '';

            return True;
        }

        return False;
    }

    /**
     * Analyze some parameters
     * @return bool
     */
    public function parameters()
    {
        $c = $this->char();

        if ($c == '(')
        {
            $level = 0;
            $string = Null;

            $this->next();
            $c = $this->char();

            while ($c !== Null)
            {
                if ($level == 0 && $string == Null && $c == ')')
                {
                    $this->_tokens[] = new Token(
                        Token::PARAMETERS,
                        "$this->_data"
                    );

                    $this->_data = '';

                    return True;
                }
                elseif ($string === Null && ($c == '(' || $c == ')'))
                {
                    $level += $c == '(' ? 1 : -1;
                }
                elseif ($string === Null && ($c == "'" || $c == '"'))
                {
                    $string = $c;
                }
                elseif ($string !== Null && $c == '\\')
                {
                    $this->_data .= $c;

                    $this->next();
                    $c = $this->char();
                }
                elseif ($string !== Null && $c == $string)
                {
                    $string = Null;
                }

                if ($c !== Null)

                    $this->_data .= $c;

                $this->next();
                $c = $this->char();
            }

            $this->unexpectedEof();
        }

        return False;
    }

    /**
     * Analyze some Html
     * @return bool
     */
    public function html()
    {
        $c = $this->char();

        while ($c !== Null)
        {
            $this->_data .= $c;

            if (strpos($this->_htmlSeparators, $this->char(1)) !== False)
            {
                if (!($this->_prevType == Token::PROPERTY &&
                    ctype_space($this->_data) &&
                    $this->char(1) == '('))

                    $this->_tokens[] = new Token(Token::HTML, $this->_data);

                $this->_data = '';

                return True;
            }

            $this->next();
            $c = $this->char();
        }

        $this->_tokens[] = new Token(Token::HTML, $this->_data);

        return False;
    }

    /**
     * Get the character from the given offset
     * @param  integer $offset The amount to offset the current index
     * @return string          The character at the given offset
     */
    public function char($offset = 0)
    {
        if ($this->_index + $offset >= $this->_len)

            return Null;

        return $this->_content[$this->_index + $offset];
    }

    /**
     * Increase the character offset
     * @param  integer $offset The amount to increase the character index
     * @return void
     */
    public function next($offset = 1)
    {
        for ($i = 0; $i < $offset; $i++)
        {
            $this->_index++;

            if ($this->char() === "\n")

                $this->_line++;
        }
    }

    /**
     * Decrease the character offset
     * @param  integer $offset The amount to decrease the character index
     * @return void
     */
    public function prev($offset = 1)
    {
        for ($i = 0; $i < $offset; $i++)
        {
            $this->_index--;

            if ($this->char() === "\n")

                $this->_line--;
        }
    }

    /**
     * Check the current property
     * @return void
     */
    public function checkProperty()
    {
        if ($this->char() == Null)

            $this->unexpectedEof();

        elseif ($this->char() == '@' || ctype_space($this->char()))

            $this->unexpectedPropertyDef();

        elseif (!ctype_alpha($this->char()))

            $this->invalidProperty();
    }

    /**
     * Invoke an unexpected EOF error
     * @return void
     */
    public function unexpectedEof()
    {
        throw new InvalidSyntaxError(
            "Unexpected 'end-of-file' on line $this->_line"
        );
    }

    /**
     * Invoke an unexpected property definition error
     * @return void
     */
    public function unexpectedPropertyDef()
    {
        throw new InvalidSyntaxError(
            "Unexpected operator '@' on line $this->_line"
        );
    }

    /**
     * Invoke an invalid property error
     * @return void
     */
    public function invalidProperty()
    {
        throw new InvalidSyntaxError(
            "Invalid proprty on line $this->_line"
        );
    }
}
