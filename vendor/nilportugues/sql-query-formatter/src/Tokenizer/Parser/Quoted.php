<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:23 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Quoted.
 */
final class Quoted
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     */
    public static function isQuoted(Tokenizer $tokenizer, $string)
    {
        if (!$tokenizer->getNextToken() && self::isQuotedString($string)) {
            $tokenizer->setNextToken(self::getQuotedString($string));
        }
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    protected static function isQuotedString($string)
    {
        return !empty($string[0]) && ($string[0] === '"' || $string[0] === '\'' || $string[0] === '`' || $string[0] === '[');
    }

    /**
     * @param string $string
     *
     * @return array
     */
    protected static function getQuotedString($string)
    {
        $tokenType = Tokenizer::TOKEN_TYPE_QUOTE;

        if (!empty($string[0]) && ($string[0] === '`' || $string[0] === '[')) {
            $tokenType = Tokenizer::TOKEN_TYPE_BACK_TICK_QUOTE;
        }

        return [
            Tokenizer::TOKEN_TYPE => $tokenType,
            Tokenizer::TOKEN_VALUE => self::wrapStringWithQuotes($string),
        ];
    }

    /**
     *  This checks for the following patterns:
     *  1. backtick quoted string using `` to escape
     *  2. square bracket quoted string (SQL Server) using ]] to escape
     *  3. double quoted string using "" or \" to escape
     *  4. single quoted string using '' or \' to escape.
     *
     * @param string $string
     *
     * @return null
     */
    public static function wrapStringWithQuotes($string)
    {
        $returnString = null;

        $regex = '/^(((`[^`]*($|`))+)|((\[[^\]]*($|\]))(\][^\]]*($|\]))*)|'.
            '(("[^"\\\\]*(?:\\\\.[^"\\\\]*)*("|$))+)|((\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*(\'|$))+))/s';

        if (1 == \preg_match($regex, $string, $matches)) {
            $returnString = $matches[1];
        }

        return $returnString;
    }
}
