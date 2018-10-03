<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:36 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class LiteralString.
 */
final class LiteralString
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     * @param array     $matches
     */
    public static function isFunction(Tokenizer $tokenizer, $string, array &$matches)
    {
        if (!$tokenizer->getNextToken() && self::isFunctionString($string, $matches, $tokenizer->getRegexFunction())) {
            $tokenizer->setNextToken(self::getFunctionString($string, $matches));
        }
    }

    /**
     * A function must be succeeded by '('.
     * This makes it so that a function such as "COUNT(" is considered a function, but "COUNT" alone is not function.
     *
     * @param string $string
     * @param array  $matches
     * @param string $regexFunction
     *
     * @return bool
     */
    protected static function isFunctionString($string, array &$matches, $regexFunction)
    {
        return (1 == \preg_match('/^('.$regexFunction.'[(]|\s|[)])/', \strtoupper($string), $matches));
    }

    /**
     * @param string $string
     * @param array  $matches
     *
     * @return array
     */
    protected static function getFunctionString($string, array &$matches)
    {
        return [
            Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_RESERVED,
            Tokenizer::TOKEN_VALUE => \substr($string, 0, \strlen($matches[1]) - 1),
        ];
    }

    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     * @param array     $matches
     */
    public static function getNonReservedString(Tokenizer $tokenizer, $string, array &$matches)
    {
        if (!$tokenizer->getNextToken()) {
            $data = [];

            if (1 == \preg_match('/^(.*?)($|\s|["\'`]|'.$tokenizer->getRegexBoundaries().')/', $string, $matches)) {
                $data = [
                    Tokenizer::TOKEN_VALUE => $matches[1],
                    Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_WORD,
                ];
            }

            $tokenizer->setNextToken($data);
        }
    }
}
