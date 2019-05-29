<?php
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Parsing\Nodes;

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Interpreting\Visitors\Visitor;

/**
 * AST node representing a number (int or float)
 */
class RationalNode extends Node
{
    /**
     * int $p The numerator of the represented number.
     */
    private $p;
    /**
     * int $q The denominator of the represented number.
     */
    private $q;

    /**
     * Constructor. Create a RationalNode with given value.
     */
    public function __construct($p, $q, $normalize = true)
    {
        if (!is_int($p) || !is_int($q)) {
            throw new \UnexpectedValueException();
        }

        if ($q == 0) {
            throw new DivisionByZeroException();
        }

        $this->p = $p;
        $this->q = $q;

        if ($normalize) {
            $this->normalize();
        }
    }

    /**
     * Returns the value
     * @retval int|float
     */
    public function getValue()
    {
        return (1.0 * $this->p) / $this->q;
    }

    public function getNumerator()
    {
        return $this->p;
    }

    public function getDenominator()
    {
        return $this->q;
    }

    /**
     * Implementing the Visitable interface.
     */
    public function accept(Visitor $visitor)
    {
        return $visitor->visitRationalNode($this);
    }

    /**
     * Implementing the compareTo abstract method.
     */
    public function compareTo($other)
    {
        if ($other === null) {
            return false;
        }
        if ($other instanceof IntegerNode) {
            return $this->getDenominator() == 1 && $this->getNumerator() == $other->getValue();
        }
        if (!($other instanceof RationalNode)) {
            return false;
        }

        return $this->getNumerator() == $other->getNumerator() && $this->getDenominator() == $other->getDenominator();
    }

    private function normalize()
    {
        $a = $this->p;
        $b = $this->q;

        $sign = 1;
        if ($a < 0) {
            $sign = -$sign;
        }

        if ($b < 0) {
            $sign = -$sign;
        }
        while ($b != 0) {
            $m = $a % $b;
            $a = $b;
            $b = $m;
        }

        $gcd = $a;
        $this->p = $this->p / $gcd;
        $this->q = $this->q / $gcd;

        if ($this->q < 0) {
            $this->q = -$this->q;
            $this->p = -$this->p;
        }
    }
}
