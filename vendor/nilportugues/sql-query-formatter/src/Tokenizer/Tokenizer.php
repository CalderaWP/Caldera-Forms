<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/26/14
 * Time: 12:10 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Tokenizer;

use NilPortugues\Sql\QueryFormatter\Helper\Token;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\Boundary;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\Comment;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\Numeral;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\Quoted;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\Reserved;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\LiteralString;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\UserDefined;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Parser\WhiteSpace;

/**
 * Class Tokenizer.
 */
class Tokenizer
{
    const TOKEN_TYPE_WHITESPACE = 0;
    const TOKEN_TYPE_WORD = 1;
    const TOKEN_TYPE_QUOTE = 2;
    const TOKEN_TYPE_BACK_TICK_QUOTE = 3;
    const TOKEN_TYPE_RESERVED = 4;
    const TOKEN_TYPE_RESERVED_TOP_LEVEL = 5;
    const TOKEN_TYPE_RESERVED_NEWLINE = 6;
    const TOKEN_TYPE_BOUNDARY = 7;
    const TOKEN_TYPE_COMMENT = 8;
    const TOKEN_TYPE_BLOCK_COMMENT = 9;
    const TOKEN_TYPE_NUMBER = 10;
    const TOKEN_TYPE_ERROR = 11;
    const TOKEN_TYPE_VARIABLE = 12;
    const TOKEN_TYPE = 0;
    const TOKEN_VALUE = 1;

    /**
     * @var string
     */
    protected $regexBoundaries;

    /**
     * @var string
     */
    protected $regexReserved;

    /**
     * @var string
     */
    protected $regexReservedNewLine;

    /**
     * @var string
     */
    protected $regexReservedTopLevel;

    /**
     * @var string
     */
    protected $regexFunction;

    /**
     * @var int
     */
    protected $maxCacheKeySize = 15;

    /**
     * @var array
     */
    protected $tokenCache = [];

    /**
     * @var array
     */
    protected $nextToken = [];

    /**
     * @var int
     */
    protected $currentStringLength = 0;

    /**
     * @var int
     */
    protected $oldStringLength = 0;

    /**
     * @var string
     */
    protected $previousToken = '';

    /**
     * @var int
     */
    protected $tokenLength = 0;

    /**
     * @var array
     */
    protected $tokens = [];

    /**
     * Builds all the regular expressions needed to Tokenize the input.
     */
    public function __construct()
    {
        $reservedMap = \array_combine(Token::$reserved, \array_map('strlen', Token::$reserved));
        \arsort($reservedMap);
        Token::$reserved = \array_keys($reservedMap);

        $this->regexFunction = $this->initRegex(Token::$functions);
        $this->regexBoundaries = $this->initRegex(Token::$boundaries);
        $this->regexReserved = $this->initRegex(Token::$reserved);
        $this->regexReservedTopLevel = \str_replace(' ', '\\s+', $this->initRegex(Token::$reservedTopLevel));
        $this->regexReservedNewLine = \str_replace(' ', '\\s+', $this->initRegex(Token::$reservedNewLine));
    }

    /**
     * @param $variable
     *
     * @return string
     */
    protected function initRegex($variable)
    {
        return '('.implode('|', \array_map(array($this, 'quoteRegex'), $variable)).')';
    }

    /**
     * Takes a SQL string and breaks it into tokens.
     * Each token is an associative array with type and value.
     *
     * @param string $string
     *
     * @return array
     */
    public function tokenize($string)
    {
        return (\strlen($string) > 0) ? $this->processTokens($string) : [];
    }

    /**
     * @param string $string
     *
     * @return array
     */
    protected function processTokens($string)
    {
        $this->tokens = [];
        $this->previousToken = '';
        $this->currentStringLength = \strlen($string);
        $this->oldStringLength = \strlen($string) + 1;

        while ($this->currentStringLength >= 0) {
            if ($this->oldStringLength <= $this->currentStringLength) {
                break;
            }
            $string = $this->processOneToken($string);
        }

        return $this->tokens;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function processOneToken($string)
    {
        $token = $this->getToken($string, $this->currentStringLength, $this->previousToken);
        $this->tokens[] = $token;
        $this->tokenLength = \strlen($token[self::TOKEN_VALUE]);
        $this->previousToken = $token;

        $this->oldStringLength = $this->currentStringLength;
        $this->currentStringLength -= $this->tokenLength;

        return \substr($string, $this->tokenLength);
    }

    /**
     * @param string $string
     * @param int    $currentStringLength
     * @param string string
     *
     * @return array|mixed
     */
    protected function getToken($string, $currentStringLength, $previousToken)
    {
        $cacheKey = $this->useTokenCache($string, $currentStringLength);
        if (!empty($cacheKey) && isset($this->tokenCache[$cacheKey])) {
            return $this->getNextTokenFromCache($cacheKey);
        }

        return $this->getNextTokenFromString($string, $previousToken, $cacheKey);
    }

    /**
     * @param string $string
     * @param int    $currentStringLength
     *
     * @return string
     */
    protected function useTokenCache($string, $currentStringLength)
    {
        $cacheKey = '';

        if ($currentStringLength >= $this->maxCacheKeySize) {
            $cacheKey = \substr($string, 0, $this->maxCacheKeySize);
        }

        return $cacheKey;
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed
     */
    protected function getNextTokenFromCache($cacheKey)
    {
        return $this->tokenCache[$cacheKey];
    }

    /**
     * Get the next token and the token type and store it in cache.
     *
     * @param string $string
     * @param string $token
     * @param string $cacheKey
     *
     * @return array
     */
    protected function getNextTokenFromString($string, $token, $cacheKey)
    {
        $token = $this->parseNextToken($string, $token);

        if ($cacheKey && \strlen($token[self::TOKEN_VALUE]) < $this->maxCacheKeySize) {
            $this->tokenCache[$cacheKey] = $token;
        }

        return $token;
    }

    /**
     * Return the next token and token type in a SQL string.
     * Quoted strings, comments, reserved words, whitespace, and punctuation are all their own tokens.
     *
     * @param string $string   The SQL string
     * @param array  $previous The result of the previous parseNextToken() call
     *
     * @return array An associative array containing the type and value of the token.
     */
    protected function parseNextToken($string, $previous = null)
    {
        $matches = [];
        $this->nextToken = [];

        WhiteSpace::isWhiteSpace($this, $string, $matches);
        Comment::isComment($this, $string);
        Quoted::isQuoted($this, $string);
        UserDefined::isUserDefinedVariable($this, $string);
        Numeral::isNumeral($this, $string, $matches);
        Boundary::isBoundary($this, $string, $matches);
        Reserved::isReserved($this, $string, $previous);
        LiteralString::isFunction($this, $string, $matches);
        LiteralString::getNonReservedString($this, $string, $matches);

        return $this->nextToken;
    }

    /**
     * @return array
     */
    public function getNextToken()
    {
        return $this->nextToken;
    }

    /**
     * @param array $nextToken
     *
     * @return $this
     */
    public function setNextToken($nextToken)
    {
        $this->nextToken = $nextToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegexBoundaries()
    {
        return $this->regexBoundaries;
    }

    /**
     * @return string
     */
    public function getRegexFunction()
    {
        return $this->regexFunction;
    }

    /**
     * @return string
     */
    public function getRegexReserved()
    {
        return $this->regexReserved;
    }

    /**
     * @return string
     */
    public function getRegexReservedNewLine()
    {
        return $this->regexReservedNewLine;
    }

    /**
     * @return string
     */
    public function getRegexReservedTopLevel()
    {
        return $this->regexReservedTopLevel;
    }

    /**
     * Helper function for building regular expressions for reserved words and boundary characters.
     *
     * @param string $string
     *
     * @return string
     */
    protected function quoteRegex($string)
    {
        return \preg_quote($string, '/');
    }
}
