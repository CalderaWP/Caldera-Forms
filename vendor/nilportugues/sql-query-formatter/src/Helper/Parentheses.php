<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/22/14
 * Time: 11:37 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Helper;

use NilPortugues\Sql\QueryFormatter\Formatter;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Parentheses.
 */
class Parentheses
{
    /**
     * @var bool
     */
    protected $inlineParentheses = false;
    /**
     * @var \NilPortugues\Sql\QueryFormatter\Formatter
     */
    protected $formatter;

    /**
     * @var Indent
     */
    protected $indentation;

    /**
     * @param Formatter $formatter
     * @param Indent    $indentation
     */
    public function __construct(Formatter $formatter, Indent $indentation)
    {
        $this->formatter = $formatter;
        $this->indentation = $indentation;
    }

    /**
     * @return bool
     */
    public function getInlineParentheses()
    {
        return $this->inlineParentheses;
    }

    /**
     * @param bool $inlineParentheses
     *
     * @return $this
     */
    public function setInlineParentheses($inlineParentheses)
    {
        $this->inlineParentheses = $inlineParentheses;

        return $this;
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function stringIsOpeningParentheses($token)
    {
        return $token[Tokenizer::TOKEN_VALUE] === '(';
    }

    /**
     *
     */
    public function writeNewInlineParentheses()
    {
        $this->inlineParentheses = true;
        $this->formatter->setInlineCount(0);
        $this->indentation->setInlineIndented(false);
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function invalidParenthesesTokenValue($token)
    {
        return $token[Tokenizer::TOKEN_VALUE] === ';'
        || $token[Tokenizer::TOKEN_VALUE] === '(';
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function invalidParenthesesTokenType($token)
    {
        return $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_RESERVED_TOP_LEVEL
        || $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_RESERVED_NEWLINE
        || $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_COMMENT
        || $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_BLOCK_COMMENT;
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function stringIsClosingParentheses($token)
    {
        return $token[Tokenizer::TOKEN_VALUE] === ')';
    }

    /**
     * @param string $tab
     * @param        $queryValue
     */
    public function writeInlineParenthesesBlock($tab, $queryValue)
    {
        $this->formatter->setFormattedSql(\rtrim($this->formatter->getFormattedSql(), ' '));

        if ($this->indentation->getInlineIndented()) {
            $indentTypes = $this->indentation->getIndentTypes();
            \array_shift($indentTypes);
            $this->indentation->setIndentTypes($indentTypes);
            $this->indentation->setIndentLvl($this->indentation->getIndentLvl() - 1);

            $this->formatter->appendToFormattedSql("\n".str_repeat($tab, $this->indentation->getIndentLvl()));
        }

        $this->inlineParentheses = false;
        $this->formatter->appendToFormattedSql($queryValue.' ');
    }
}
