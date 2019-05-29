<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/22/14
 * Time: 10:09 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryFormatter\Helper;

use NilPortugues\Sql\QueryFormatter\Formatter;
use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class Comment.
 */
class Comment
{
    /**
     * @var \NilPortugues\Sql\QueryFormatter\Formatter
     */
    protected $formatter;

    /**
     * @var Indent
     */
    protected $indentation;

    /**
     * @var NewLine
     */
    protected $newLine;

    /**
     * @param Formatter $formatter
     * @param Indent    $indentation
     * @param NewLine   $newLine
     */
    public function __construct(Formatter $formatter, Indent $indentation, NewLine $newLine)
    {
        $this->formatter = $formatter;
        $this->indentation = $indentation;
        $this->newLine = $newLine;
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function stringHasCommentToken($token)
    {
        return $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_COMMENT
        || $token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_BLOCK_COMMENT;
    }

    /**
     * @param        $token
     * @param string $tab
     * @param        $queryValue
     *
     * @return string
     */
    public function writeCommentBlock($token, $tab, $queryValue)
    {
        if ($token[Tokenizer::TOKEN_TYPE] === Tokenizer::TOKEN_TYPE_BLOCK_COMMENT) {
            $indent = \str_repeat($tab, $this->indentation->getIndentLvl());

            $this->formatter->appendToFormattedSql("\n".$indent);
            $queryValue = \str_replace("\n", "\n".$indent, $queryValue);
        }

        $this->formatter->appendToFormattedSql($queryValue);
        $this->newLine->setNewline(true);

        return $this->formatter->getFormattedSql();
    }
}
