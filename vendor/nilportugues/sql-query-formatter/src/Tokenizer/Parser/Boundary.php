<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:34 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Boundary.
 */
final class Boundary
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     * @param array     $matches
     */
    public static function isBoundary(Tokenizer $tokenizer, $string, array &$matches)
    {
        if (!$tokenizer->getNextToken() &&
            self::isBoundaryCharacter($string, $matches, $tokenizer->getRegexBoundaries())
        ) {
            $tokenizer->setNextToken(self::getBoundaryCharacter($matches));
        }
    }

    /**
     * @param string $string
     * @param array  $matches
     * @param string $regexBoundaries
     *
     * @return bool
     */
    protected static function isBoundaryCharacter($string, array &$matches, $regexBoundaries)
    {
        return (1 == \preg_match('/^('.$regexBoundaries.')/', $string, $matches));
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    protected static function getBoundaryCharacter(array &$matches)
    {
        return [
            Tokenizer::TOKEN_VALUE => $matches[1],
            Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_BOUNDARY,
        ];
    }
}
