<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:32 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Numeral.
 */
final class Numeral
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     * @param array     $matches
     *
     * @return array
     */
    public static function isNumeral(Tokenizer $tokenizer, $string, array &$matches)
    {
        if (!$tokenizer->getNextToken() && self::isNumeralString($string, $matches, $tokenizer->getRegexBoundaries())) {
            $tokenizer->setNextToken(self::getNumeralString($matches));
        }
    }

    /**
     * @param string $string
     * @param array  $matches
     * @param string $regexBoundaries
     *
     * @return bool
     */
    protected static function isNumeralString($string, array &$matches, $regexBoundaries)
    {
        return (1 == \preg_match(
                '/^([0-9]+(\.[0-9]+)?|0x[0-9a-fA-F]+|0b[01]+)($|\s|"\'`|'.$regexBoundaries.')/',
                $string,
                $matches
            ));
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    protected static function getNumeralString(array &$matches)
    {
        return [Tokenizer::TOKEN_VALUE => $matches[1], Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_NUMBER];
    }
}
