<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/22/14
 * Time: 1:19 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Helper;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class WhiteSpace.
 */
class WhiteSpace
{
    /**
     * @param $token
     *
     * @return bool
     */
    public static function tokenHasExtraWhiteSpaceLeft($token)
    {
        return
            $token[Tokenizer::TOKEN_VALUE] === '.'
            || $token[Tokenizer::TOKEN_VALUE] === ','
            || $token[Tokenizer::TOKEN_VALUE] === ';';
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public static function tokenHasExtraWhiteSpaceRight($token)
    {
        return
            $token[Tokenizer::TOKEN_VALUE] === '('
            || $token[Tokenizer::TOKEN_VALUE] === '.';
    }

    /**
     * @param $tokenType
     *
     * @return bool
     */
    public static function tokenIsNumberAndHasExtraWhiteSpaceRight($tokenType)
    {
        return
            $tokenType !== Tokenizer::TOKEN_TYPE_QUOTE
            && $tokenType !== Tokenizer::TOKEN_TYPE_BACK_TICK_QUOTE
            && $tokenType !== Tokenizer::TOKEN_TYPE_WORD
            && $tokenType !== Tokenizer::TOKEN_TYPE_NUMBER;
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public static function tokenHasExtraWhiteSpaces($token)
    {
        return \strpos($token[Tokenizer::TOKEN_VALUE], ' ') !== false
        || \strpos($token[Tokenizer::TOKEN_VALUE], "\n") !== false
        || \strpos($token[Tokenizer::TOKEN_VALUE], "\t") !== false;
    }

    /**
     * @param $originalTokens
     * @param $token
     *
     * @return bool
     */
    public static function isPrecedingCurrentTokenOfTokenTypeWhiteSpace($originalTokens, $token)
    {
        return isset($originalTokens[$token['i'] - 1])
        && $originalTokens[$token['i'] - 1][Tokenizer::TOKEN_TYPE] !== Tokenizer::TOKEN_TYPE_WHITESPACE;
    }

    /**
     * @param $originalTokens
     *
     * @return array
     */
    public static function removeTokenWhitespace(array &$originalTokens)
    {
        $tokens = [];
        foreach ($originalTokens as $i => &$token) {
            if ($token[Tokenizer::TOKEN_TYPE] !== Tokenizer::TOKEN_TYPE_WHITESPACE) {
                $token['i'] = $i;
                $tokens[] = $token;
            }
        }

        return $tokens;
    }
}
