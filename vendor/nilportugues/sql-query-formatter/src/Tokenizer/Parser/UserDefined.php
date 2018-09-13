<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/23/14
 * Time: 1:26 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer\Parser;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class UserDefined.
 */
final class UserDefined
{
    /**
     * @param Tokenizer $tokenizer
     * @param string    $string
     *
     * @return array
     */
    public static function isUserDefinedVariable(Tokenizer $tokenizer, $string)
    {
        if (!$tokenizer->getNextToken() && self::isUserDefinedVariableString($string)) {
            $tokenizer->setNextToken(self::getUserDefinedVariableString($string));
        }
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    protected static function isUserDefinedVariableString(&$string)
    {
        return !empty($string[0]) && !empty($string[1]) && ($string[0] === '@' && isset($string[1]));
    }

    /**
     * Gets the user defined variables for in quoted or non-quoted fashion.
     *
     * @param string $string
     *
     * @return array
     */
    protected static function getUserDefinedVariableString(&$string)
    {
        $returnData = [
            Tokenizer::TOKEN_VALUE => null,
            Tokenizer::TOKEN_TYPE => Tokenizer::TOKEN_TYPE_VARIABLE,
        ];

        self::setTokenValueStartingWithAtSymbolAndWrapped($returnData, $string);
        self::setTokenValueStartingWithAtSymbol($returnData, $string);

        return $returnData;
    }

    /**
     * @param array  $returnData
     * @param string $string
     */
    protected static function setTokenValueStartingWithAtSymbolAndWrapped(array &$returnData, $string)
    {
        if (!empty($string[1]) && ($string[1] === '"' || $string[1] === '\'' || $string[1] === '`')) {
            $returnData[Tokenizer::TOKEN_VALUE] = '@'.Quoted::wrapStringWithQuotes(\substr($string, 1));
        }
    }

    /**
     * @param array  $returnData
     * @param string $string
     */
    protected static function setTokenValueStartingWithAtSymbol(array &$returnData, $string)
    {
        if (null === $returnData[Tokenizer::TOKEN_VALUE]) {
            $matches = [];
            \preg_match('/^(@[a-zA-Z0-9\._\$]+)/', $string, $matches);
            if ($matches) {
                $returnData[Tokenizer::TOKEN_VALUE] = $matches[1];
            }
        }
    }
}
