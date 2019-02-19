<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/26/14
 * Time: 12:10 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter;

use NilPortugues\Sql\QueryFormatter\Helper\Comment;
use NilPortugues\Sql\QueryFormatter\Helper\Indent;
use NilPortugues\Sql\QueryFormatter\Helper\NewLine;
use NilPortugues\Sql\QueryFormatter\Helper\Parentheses;
use NilPortugues\Sql\QueryFormatter\Helper\Token;
use NilPortugues\Sql\QueryFormatter\Helper\WhiteSpace;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Lightweight Formatter heavily based on https://github.com/jdorn/sql-formatter.
 *
 * Class Formatter
 */
class Formatter
{
    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    /**
     * @var NewLine
     */
    protected $newLine;

    /**
     * @var Parentheses
     */
    protected $parentheses;

    /**
     * @var string
     */
    protected $tab = '    ';
    /**
     * @var int
     */
    protected $inlineCount = 0;

    /**
     * @var bool
     */
    protected $clauseLimit = false;
    /**
     * @var string
     */
    protected $formattedSql = '';
    /**
     * @var Indent
     */
    protected $indentation;

    /**
     * @var Comment
     */
    protected $comment;

    /**
     * Returns a SQL string in a readable human-friendly format.
     *
     * @param string $sql
     *
     * @return string
     */
    public function format($sql)
    {
        $this->reset();
        $tab = "\t";

        $originalTokens = $this->tokenizer->tokenize((string) $sql);
        $tokens = WhiteSpace::removeTokenWhitespace($originalTokens);

        foreach ($tokens as $i => $token) {
            $queryValue = $token[Tokenizer::TOKEN_VALUE];
            $this->indentation->increaseSpecialIndent()->increaseBlockIndent();
            $addedNewline = $this->newLine->addNewLineBreak($tab);

            if ($this->comment->stringHasCommentToken($token)) {
                $this->formattedSql = $this->comment->writeCommentBlock($token, $tab, $queryValue);
                continue;
            }

            if ($this->parentheses->getInlineParentheses()) {
                if ($this->parentheses->stringIsClosingParentheses($token)) {
                    $this->parentheses->writeInlineParenthesesBlock($tab, $queryValue);
                    continue;
                }
                $this->newLine->writeNewLineForLongCommaInlineValues($token);
                $this->inlineCount += \strlen($token[Tokenizer::TOKEN_VALUE]);
            }

            switch ($token) {
                case $this->parentheses->stringIsOpeningParentheses($token):
                    $tokens = $this->formatOpeningParenthesis($token, $i, $tokens, $originalTokens);
                    break;

                case $this->parentheses->stringIsClosingParentheses($token):
                    $this->indentation->decreaseIndentLevelUntilIndentTypeIsSpecial($this);
                    $this->newLine->addNewLineBeforeToken($addedNewline, $tab);
                    break;

                case $this->stringIsEndOfLimitClause($token):
                    $this->clauseLimit = false;
                    break;

                case $token[Tokenizer::TOKEN_VALUE] === ',' && false === $this->parentheses->getInlineParentheses():
                    $this->newLine->writeNewLineBecauseOfComma();
                    break;

                case Token::isTokenTypeReservedTopLevel($token):
                    $queryValue = $this->formatTokenTypeReservedTopLevel($addedNewline, $tab, $token, $queryValue);
                    break;

                case $this->newLine->isTokenTypeReservedNewLine($token):
                    $this->newLine->addNewLineBeforeToken($addedNewline, $tab);

                    if (WhiteSpace::tokenHasExtraWhiteSpaces($token)) {
                        $queryValue = \preg_replace('/\s+/', ' ', $queryValue);
                    }
                    break;
            }

            $this->formatBoundaryCharacterToken($token, $i, $tokens, $originalTokens);
            $this->formatWhiteSpaceToken($token, $queryValue);
            $this->formatDashToken($token, $i, $tokens);
        }

        return \trim(\str_replace(["\t", " \n"], [$this->tab, "\n"], $this->formattedSql))."\n";
    }

    /**
     *
     */
    public function reset()
    {
        $this->tokenizer = new Tokenizer();
        $this->indentation = new Indent();
        $this->parentheses = new Parentheses($this, $this->indentation);
        $this->newLine = new NewLine($this, $this->indentation, $this->parentheses);
        $this->comment = new Comment($this, $this->indentation, $this->newLine);

        $this->formattedSql = '';
    }

    /**
     * @param       $token
     * @param       $i
     * @param array $tokens
     * @param array $originalTokens
     *
     * @return array
     */
    protected function formatOpeningParenthesis($token, $i, array &$tokens, array &$originalTokens)
    {
        $length = 0;
        for ($j = 1; $j <= 250; ++$j) {
            if (isset($tokens[$i + $j])) {
                $next = $tokens[$i + $j];
                if ($this->parentheses->stringIsClosingParentheses($next)) {
                    $this->parentheses->writeNewInlineParentheses();
                    break;
                }

                if ($this->parentheses->invalidParenthesesTokenValue($next)
                    || $this->parentheses->invalidParenthesesTokenType($next)
                ) {
                    break;
                }

                $length += \strlen($next[Tokenizer::TOKEN_VALUE]);
            }
        }
        $this->newLine->writeNewLineForLongInlineValues($length);

        if (WhiteSpace::isPrecedingCurrentTokenOfTokenTypeWhiteSpace($originalTokens, $token)) {
            $this->formattedSql = \rtrim($this->formattedSql, ' ');
        }

        $this->newLine->addNewLineAfterOpeningParentheses();

        return $tokens;
    }

    /**
     * @param $token
     *
     * @return bool
     */
    protected function stringIsEndOfLimitClause($token)
    {
        return $this->clauseLimit
        && $token[Tokenizer::TOKEN_VALUE] !== ','
        && $token[Tokenizer::TOKEN_TYPE] !== Tokenizer::TOKEN_TYPE_NUMBER
        && $token[Tokenizer::TOKEN_TYPE] !== Tokenizer::TOKEN_TYPE_WHITESPACE;
    }

    /**
     * @param bool   $addedNewline
     * @param string $tab
     * @param $token
     * @param $queryValue
     *
     * @return mixed
     */
    protected function formatTokenTypeReservedTopLevel($addedNewline, $tab, $token, $queryValue)
    {
        $this->indentation
            ->setIncreaseSpecialIndent(true)
            ->decreaseSpecialIndentIfCurrentIndentTypeIsSpecial();

        $this->newLine->writeNewLineBecauseOfTopLevelReservedWord($addedNewline, $tab);

        if (WhiteSpace::tokenHasExtraWhiteSpaces($token)) {
            $queryValue = \preg_replace('/\s+/', ' ', $queryValue);
        }
        Token::tokenHasLimitClause($token, $this->parentheses, $this);

        return $queryValue;
    }

    /**
     * @param       $token
     * @param       $i
     * @param array $tokens
     * @param array $originalTokens
     */
    protected function formatBoundaryCharacterToken($token, $i, array &$tokens, array &$originalTokens)
    {
        if (Token::tokenHasMultipleBoundaryCharactersTogether($token, $tokens, $i, $originalTokens)) {
            $this->formattedSql = \rtrim($this->formattedSql, ' ');
        }
    }

    /**
     * @param $token
     * @param $queryValue
     */
    protected function formatWhiteSpaceToken($token, $queryValue)
    {
        if (WhiteSpace::tokenHasExtraWhiteSpaceLeft($token)) {
            $this->formattedSql = \rtrim($this->formattedSql, ' ');
        }

        $this->formattedSql .= $queryValue.' ';

        if (WhiteSpace::tokenHasExtraWhiteSpaceRight($token)) {
            $this->formattedSql = \rtrim($this->formattedSql, ' ');
        }
    }

    /**
     * @param       $token
     * @param       $i
     * @param array $tokens
     */
    protected function formatDashToken($token, $i, array &$tokens)
    {
        if (Token::tokenIsMinusSign($token, $tokens, $i)) {
            $previousTokenType = $tokens[$i - 1][Tokenizer::TOKEN_TYPE];

            if (WhiteSpace::tokenIsNumberAndHasExtraWhiteSpaceRight($previousTokenType)) {
                $this->formattedSql = \rtrim($this->formattedSql, ' ');
            }
        }
    }

    /**
     * @return string
     */
    public function getFormattedSql()
    {
        return $this->formattedSql;
    }

    /**
     * @param string $formattedSql
     *
     * @return $this
     */
    public function setFormattedSql($formattedSql)
    {
        $this->formattedSql = $formattedSql;

        return $this;
    }

    /**
     * @param $string
     *
     * @return $this
     */
    public function appendToFormattedSql($string)
    {
        $this->formattedSql .= $string;

        return $this;
    }

    /**
     * @return int
     */
    public function getInlineCount()
    {
        return $this->inlineCount;
    }

    /**
     * @param int $inlineCount
     *
     * @return $this
     */
    public function setInlineCount($inlineCount)
    {
        $this->inlineCount = $inlineCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClauseLimit()
    {
        return $this->clauseLimit;
    }

    /**
     * @param bool $clauseLimit
     *
     * @return $this
     */
    public function setClauseLimit($clauseLimit)
    {
        $this->clauseLimit = $clauseLimit;

        return $this;
    }
}
