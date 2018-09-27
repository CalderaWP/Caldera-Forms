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
 * Class NewLine.
 */
class NewLine
{
    /**
     * @var bool
     */
    protected $newline = false;

    /**
     * @var \NilPortugues\Sql\QueryFormatter\Formatter
     */
    protected $formatter;

    /**
     * @var Indent
     */
    protected $indentation;

    /**
     * @var Parentheses
     */
    protected $parentheses;

    /**
     * @param Formatter   $formatter
     * @param Indent      $indentation
     * @param Parentheses $parentheses
     */
    public function __construct(Formatter $formatter, Indent $indentation, Parentheses $parentheses)
    {
        $this->formatter = $formatter;
        $this->indentation = $indentation;
        $this->parentheses = $parentheses;
    }

    /**
     * Adds a new line break if needed.
     *
     * @param string $tab
     *
     * @return bool
     */
    public function addNewLineBreak($tab)
    {
        $addedNewline = false;

        if (true === $this->newline) {
            $this->formatter->appendToFormattedSql("\n".str_repeat($tab, $this->indentation->getIndentLvl()));
            $this->newline = false;
            $addedNewline = true;
        }

        return $addedNewline;
    }

    /**
     * @param $token
     */
    public function writeNewLineForLongCommaInlineValues($token)
    {
        if (',' === $token[Tokenizer::TOKEN_VALUE]) {
            if ($this->formatter->getInlineCount() >= 30) {
                $this->formatter->setInlineCount(0);
                $this->newline = true;
            }
        }
    }

    /**
     * @param int $length
     */
    public function writeNewLineForLongInlineValues($length)
    {
        if ($this->parentheses->getInlineParentheses() && $length > 30) {
            $this->indentation->setIncreaseBlockIndent(true);
            $this->indentation->setInlineIndented(true);
            $this->newline = true;
        }
    }

    /**
     * Adds a new line break for an opening parentheses for a non-inline expression.
     */
    public function addNewLineAfterOpeningParentheses()
    {
        if (false === $this->parentheses->getInlineParentheses()) {
            $this->indentation->setIncreaseBlockIndent(true);
            $this->newline = true;
        }
    }

    /**
     * @param bool   $addedNewline
     * @param string $tab
     */
    public function addNewLineBeforeToken($addedNewline, $tab)
    {
        if (false === $addedNewline) {
            $this->formatter->appendToFormattedSql(
                "\n".str_repeat($tab, $this->indentation->getIndentLvl())
            );
        }
    }

    /**
     * Add a newline before the top level reserved word if necessary and indent.
     *
     * @param bool   $addedNewline
     * @param string $tab
     */
    public function writeNewLineBecauseOfTopLevelReservedWord($addedNewline, $tab)
    {
        if (false === $addedNewline) {
            $this->formatter->appendToFormattedSql("\n");
        } else {
            $this->formatter->setFormattedSql(\rtrim($this->formatter->getFormattedSql(), $tab));
        }
        $this->formatter->appendToFormattedSql(\str_repeat($tab, $this->indentation->getIndentLvl()));

        $this->newline = true;
    }

    /**
     * Commas start a new line unless they are found within inline parentheses or SQL 'LIMIT' clause.
     * If the previous TOKEN_VALUE is 'LIMIT', undo new line.
     */
    public function writeNewLineBecauseOfComma()
    {
        $this->newline = true;

        if (true === $this->formatter->getClauseLimit()) {
            $this->newline = false;
            $this->formatter->setClauseLimit(false);
        }
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function isTokenTypeReservedNewLine($token)
    {
        return $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_RESERVED_NEWLINE;
    }

    /**
     * @return bool
     */
    public function getNewline()
    {
        return $this->newline;
    }

    /**
     * @param bool $newline
     *
     * @return $this
     */
    public function setNewline($newline)
    {
        $this->newline = $newline;

        return $this;
    }
}
