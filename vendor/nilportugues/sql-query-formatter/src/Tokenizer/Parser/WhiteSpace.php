<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:19 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class WhiteSpace.
 */
final class WhiteSpace
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     * @param array     $matches
     */
    public static function isWhiteSpace(Tokenizer $tokenizer, $string, array &$matches)
    {
        if (self::isWhiteSpaceString($string, $matches)) {
            $tokenizer->setNextToken(self::getWhiteSpaceString($matches));
        }
    }

    /**
     * @param string $string
     * @param array  $matches
     *
     * @return bool
     */
    public static function isWhiteSpaceString($string, array &$matches)
    {
        return (1 == \preg_match('/^\s+/', $string, $matches));
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    public static function getWhiteSpaceString(array &$matches)
    {
        return [
            Tokenizer::TOKEN_VALUE => $matches[0],
            Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_WHITESPACE,
        ];
    }
}
