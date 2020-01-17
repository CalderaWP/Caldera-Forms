<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:18 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Reserved.
 */
final class Reserved
{
    /**
     * @var array
     */
    protected static $regex = [
        Tokenizer::TOKEN_TYPE_RESERVED_TOP_LEVEL => 'getRegexReservedTopLevel',
        Tokenizer::TOKEN_TYPE_RESERVED_NEWLINE => 'getRegexReservedNewLine',
        Tokenizer::TOKEN_TYPE_RESERVED => 'getRegexReserved',
    ];

    /**
     * @param Tokenizer  $tokenizer
     * @param string     $string
     * @param array|null $previous
     *
     * @return array
     */
    public static function isReserved(Tokenizer $tokenizer, $string, $previous)
    {
        $tokenData = [];

        if (!$tokenizer->getNextToken() && self::isReservedPrecededByDotCharacter($previous)) {
            $upperCase = \strtoupper($string);

            self::getReservedString($tokenData, Tokenizer::TOKEN_TYPE_RESERVED_TOP_LEVEL, $string, $tokenizer);
            self::getReservedString($tokenData, Tokenizer::TOKEN_TYPE_RESERVED_NEWLINE, $upperCase, $tokenizer);
            self::getReservedString($tokenData, Tokenizer::TOKEN_TYPE_RESERVED, $string, $tokenizer);

            $tokenizer->setNextToken($tokenData);
        }
    }

    /**
     * A reserved word cannot be preceded by a "." in order to differentiate "mytable.from" from the token "from".
     *
     * @param $previous
     *
     * @return bool
     */
    protected static function isReservedPrecededByDotCharacter($previous)
    {
        return !$previous || !isset($previous[Tokenizer::TOKEN_VALUE]) || $previous[Tokenizer::TOKEN_VALUE] !== '.';
    }

    /**
     * @param array     $tokenData
     * @param           $type
     * @param string    $string
     * @param Tokenizer $tokenizer
     */
    protected static function getReservedString(array &$tokenData, $type, $string, Tokenizer $tokenizer)
    {
        $matches = [];
        $method = self::$regex[$type];

        if (empty($tokenData) && self::isReservedString(
                $string,
                $matches,
                $tokenizer->$method(),
                $tokenizer->getRegexBoundaries()
            )
        ) {
            $tokenData = self::getStringTypeArray($type, $string, $matches);
        }
    }

    /**
     * @param string $upper
     * @param array  $matches
     * @param string $regexReserved
     * @param string $regexBoundaries
     *
     * @return bool
     */
    protected static function isReservedString($upper, array &$matches, $regexReserved, $regexBoundaries)
    {
        return 1 == \preg_match(
            '/^('.$regexReserved.')($|\s|'.$regexBoundaries.')/',
            \strtoupper($upper),
            $matches
        );
    }

    /**
     * @param string $type
     * @param string $string
     * @param array  $matches
     *
     * @return array
     */
    protected static function getStringTypeArray($type, $string, array &$matches)
    {
        return [
            Tokenizer::TOKEN_TYPE => $type,
            Tokenizer::TOKEN_VALUE => \substr($string, 0, \strlen($matches[1])),
        ];
    }
}
