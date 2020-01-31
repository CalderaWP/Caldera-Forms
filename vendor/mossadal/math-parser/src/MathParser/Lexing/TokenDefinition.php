<?php
/*
 * @package     Lexical analysis
 * @subpackage  Token handling
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Lexing;

use MathParser\Lexing\TokenAssociativity;

/**
 * Token definitions using regular expressions to match input
 *
 * To get the Lexer to recognize tokens, they need to be defined. This is
 * the task of the TokenDefinition class. Each TokenDefinition consists of
 * a regular expression used to match the input string, a corresponding
 * token type and an optional token value (making it possible to standarize
 * the token value for synonyms, e.g. both ln and log can be tokenized into the
 * same token with value 'log'.)
 *
 * ### Example usage (excerpt from StdMathLexer):
 *
 * ~~~{.php}
 * $lexer = new Lexer();
 * $lexer->add(new TokenDefinition('/\d+\.\d+/', TokenType::RealNumber));
 * $lexer->add(new TokenDefinition('/\d+/', TokenType::PosInt));
 * $lexer->add(new TokenDefinition('/sin/', TokenType::FunctionName));
 * $lexer->add(new TokenDefinition('/arcsin|asin/', TokenType::FunctionName, 'arcsin'));
 * $lexer->add(new TokenDefinition('/\+/', TokenType::AdditionOperator));
 * $lexer->add(new TokenDefinition('/\-/', TokenType::SubtractionOperator));
 * ~~~
 */
class TokenDefinition
{
    /** string $pattern Regular expression defining the rule for matching a token. */
    private $pattern;
    /** string $value Standarized value of token */
    private $value;
    /** inst $tokenType Type of token, as defined in TokenType class. */
    private $tokenType;

    /** Constructor. Create a TokenDefinition with given pattern and type. */
    public function __construct($pattern, $tokenType, $value=null)
    {
        $this->pattern = $pattern;
        $this->value = $value;
        $this->tokenType = $tokenType;
    }

    /**
     * Try to match the given input to the current TokenDefinition.
     *
     * @param string $input Input string
     * @retval Token|null Token matching the regular expression defining the TokenDefinition
     */
    public function match($input)
    {
        // Match the input with the regex pattern, storing any match found into the $matches variable,
        // along with the index of the string at which it was matched.
        $result = preg_match($this->pattern, $input, $matches, PREG_OFFSET_CAPTURE);

        // preg_match returns false if an error occured
        if ($result === false)
            throw new \Exception(preg_last_error());

        // 0 means no match was found
        if ($result === 0)
            return null;

        return $this->getTokenFromMatch($matches[0]);
    }

    /**
     * Convert matching string to an actual Token.
     *
     * @param string $match Matching text.
     * @retval Token Corresponding token.
     */
    private function getTokenFromMatch($match)
    {
        $value = $match[0];
        $offset = $match[1];

        // If we don't match at the beginning of the string, it fails.
        if ($offset !== 0)
            return null;

        if ($this->value) $value = $this->value;

        return new Token($value, $this->tokenType, $match[0]);
    }

}
