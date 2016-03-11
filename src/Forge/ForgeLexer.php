<?php

namespace Forge;

use Forge\ForgeToken as Token;

use Forge\Exception\UnknownStatementError;
use Forge\Exception\InvalidSyntaxError;

class ForgeLexer
{
    private $_printStatements;

    private $_content;
    private $_len;

    private $_tokens;
    private $_prevType;
    private $_data;

    private $_index;
    private $_line;

    private $_htmlSeparators = '@{(';


    public function __construct()
    {

    }

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
            if ($this->printStatement())

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

                    $this->next();

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

    public function char($offset = 0)
    {
        if ($this->_index + $offset >= $this->_len)

            return Null;

        return $this->_content[$this->_index + $offset];
    }

    public function next($offset = 1)
    {
        for ($i = 0; $i < $offset; $i++)
        {
            $this->_index++;

            if ($this->char() === "\n")

                $this->_line++;
        }
    }

    public function prev($offset = 1)
    {
        for ($i = $offset; $i >= 0; $i--)
        {
            $this->_index--;

            if ($this->char() === "\n")

                $this->_line--;
        }
    }

    public function checkPrintStatement($symbol)
    {
        if (!isset($this->_printStatements[$symbol]))

            throw new UnknownStatementError(
                "Unknown 'print statement' on line $this->_line"
            );
    }

    public function checkProperty()
    {
        if ($this->char() == Null)

            $this->unexpectedEof();

        elseif ($this->char() == '@' || ctype_space($this->char()))

            $this->unexpectedPropertyDef();

        elseif (!ctype_alpha($this->char()))

            $this->invalidProperty();
    }

    public function unexpectedEof()
    {
        throw new InvalidSyntaxError(
            "Unexpected 'end-of-file' on line $this->_line"
        );
    }

    public function unexpectedPropertyDef()
    {
        throw new InvalidSyntaxError(
            "Unexpected operator '@' on line $this->_line"
        );
    }

    public function invalidProperty()
    {
        throw new InvalidSyntaxError(
            "Invalid proprty on line $this->_line"
        );
    }
}
