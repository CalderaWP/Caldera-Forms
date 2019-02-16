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

/**
 * Class Indent.
 */
class Indent
{
    /**
     * @var bool
     */
    protected $inlineIndented = false;

    /**
     * @var bool
     */
    protected $increaseSpecialIndent = false;

    /**
     * @var int
     */
    protected $indentLvl = 0;

    /**
     * @var bool
     */
    protected $increaseBlockIndent = false;

    /**
     * @var array
     */
    protected $indentTypes = [];

    /**
     * Increase the Special Indent if increaseSpecialIndent is true after the current iteration.
     *
     * @return $this
     */
    public function increaseSpecialIndent()
    {
        if ($this->increaseSpecialIndent) {
            ++$this->indentLvl;
            $this->increaseSpecialIndent = false;
            \array_unshift($this->indentTypes, 'special');
        }

        return $this;
    }

    /**
     * Increase the Block Indent if increaseBlockIndent is true after the current iteration.
     *
     * @return $this
     */
    public function increaseBlockIndent()
    {
        if ($this->increaseBlockIndent) {
            ++$this->indentLvl;
            $this->increaseBlockIndent = false;
            \array_unshift($this->indentTypes, 'block');
        }

        return $this;
    }

    /**
     * Closing parentheses decrease the block indent level.
     *
     * @param Formatter $formatter
     *
     * @return $this
     */
    public function decreaseIndentLevelUntilIndentTypeIsSpecial(Formatter $formatter)
    {
        $formatter->setFormattedSql(\rtrim($formatter->getFormattedSql(), ' '));
        --$this->indentLvl;

        while ($j = \array_shift($this->indentTypes)) {
            if ('special' !== $j) {
                break;
            }
            --$this->indentLvl;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function decreaseSpecialIndentIfCurrentIndentTypeIsSpecial()
    {
        \reset($this->indentTypes);

        if (\current($this->indentTypes) === 'special') {
            --$this->indentLvl;
            \array_shift($this->indentTypes);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getIncreaseBlockIndent()
    {
        return $this->increaseBlockIndent;
    }

    /**
     * @return bool
     */
    public function getIncreaseSpecialIndent()
    {
        return $this->increaseSpecialIndent;
    }

    /**
     * @return int
     */
    public function getIndentLvl()
    {
        return $this->indentLvl;
    }

    /**
     * @return mixed
     */
    public function getIndentTypes()
    {
        return $this->indentTypes;
    }

    /**
     * @param bool $increaseBlockIndent
     *
     * @return $this
     */
    public function setIncreaseBlockIndent($increaseBlockIndent)
    {
        $this->increaseBlockIndent = $increaseBlockIndent;

        return $this;
    }

    /**
     * @param bool $increaseSpecialIndent
     *
     * @return $this
     */
    public function setIncreaseSpecialIndent($increaseSpecialIndent)
    {
        $this->increaseSpecialIndent = $increaseSpecialIndent;

        return $this;
    }

    /**
     * @param int $indentLvl
     *
     * @return $this
     */
    public function setIndentLvl($indentLvl)
    {
        $this->indentLvl = $indentLvl;

        return $this;
    }

    /**
     * @param array $indentTypes
     *
     * @return $this
     */
    public function setIndentTypes($indentTypes)
    {
        $this->indentTypes = $indentTypes;

        return $this;
    }

    /**
     * @param bool $inlineIndented
     *
     * @return $this
     */
    public function setInlineIndented($inlineIndented)
    {
        $this->inlineIndented = $inlineIndented;

        return $this;
    }

    /**
     * @return bool
     */
    public function getInlineIndented()
    {
        return $this->inlineIndented;
    }
}
