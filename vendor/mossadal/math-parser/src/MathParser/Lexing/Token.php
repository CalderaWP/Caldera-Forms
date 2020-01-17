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

use MathParser\Lexing\TokenType;
use MathParser\Lexing\TokenPrecedence;
use MathParser\Lexing\TokenAssociativity;

/**
 * Token class
 *
 * Class to handle tokens, i.e. discrete pieces of the input string
 * that has specific meaning.
 *
 */
class Token
{

    /** string $value (Standadized) value of token. */
    private $value;
    /** int $type Token type as defined in TokenType class */
    private $type;
    /** string $match The actual string matched in the input. */
    private $match;

    /**
     * Public constructor
     *
     * Create a token with a given value and type, as well
     * as an optional 'match' which is the actual character string
     * matching the token definition. Most of the time, $value
     * and $match are the same, but in order to handle token synonyms,
     * they may be different.
     *
     * As an example illustrating the above, the natural logarithm can
     * be denoted ln() as well as log(). In order to standardize the
     * token list, both inputs might generate a token with value 'log' and
     * type TokenType::FunctionName, but the match parameter will be the
     * actual string matched, i.e. 'log' and 'ln', respectively, so that
     * the token knows its own length so that the rest of the input string
     * will be handled correctly.
     *
     * @param string $value Standardized value of Token
     * @param int $type Token type, as defined by the TokenType class
     * @param string $match Optional actual match in the input string
     */
    public function __construct($value, $type, $match=null)
    {
        $this->value = $value;
        $this->type = $type;
        $this->match = $match ? $match : $value;
    }

    /**
     * @property length
     * Length of the input string matching the token.
     *
     * @retval int length of string matching the token.
     */
    public function length()
    {
        return strlen($this->match);
    }

    /**
     * @property getValue
     * Standarized value/name of the Token, usually the same as
     * what was matched in the the input string.
     *
     * @retval string value of token
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @property getType
     * Returns the type of the token, as defined in the TokenType class.
     *
     * @retval int token type (as defined by TokenType)
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Helper function, converting the Token to a printable string.
     *
     * @retval string
     */
    public function __toString()
    {
        return "Token: [$this->value, $this->type]";
    }

    /**
     * Helper function, determining whether a pair of tokens
     * can form an implicit multiplication.
     *
     * Mathematical shorthand writing often leaves out explicit multiplication
     * symbols, writing "2x" instead of "2*x" or "2 \cdot x". The parser
     * accepts implicit multiplication if the first token is a nullary operator
     * or a a closing parenthesis, and the second token is a nullary operator
     * or an opening parenthesis. (Unless the first token is a a function name,
     * and the second is an opening parentheis.)
     *
     * @retval boolean
     */
    public static function canFactorsInImplicitMultiplication($token1, $token2)
    {
        if ($token1 === null || $token2 === null) return false;

        $check1 = (
            $token1->type == TokenType::PosInt ||
            $token1->type == TokenType::Integer ||
            $token1->type == TokenType::RealNumber ||
            $token1->type == TokenType::Constant ||
            $token1->type == TokenType::Identifier ||
            $token1->type == TokenType::FunctionName ||
            $token1->type == TokenType::CloseParenthesis ||
            $token1->type == TokenType::FactorialOperator ||
            $token1->type == TokenType::SemiFactorialOperator
        );

        if (!$check1) return false;

        $check2 = (
            $token2->type == TokenType::PosInt ||
            $token2->type == TokenType::Integer ||
            $token2->type == TokenType::RealNumber ||
            $token2->type == TokenType::Constant ||
            $token2->type == TokenType::Identifier ||
            $token2->type == TokenType::FunctionName ||
            $token2->type == TokenType::OpenParenthesis
        );

        if (!$check2) return false;

        if ($token1->type == TokenType::FunctionName && $token2->type == TokenType::OpenParenthesis)
            return false;

        return true;
    }
}
