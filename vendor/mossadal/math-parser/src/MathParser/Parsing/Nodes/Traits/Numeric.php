<?php
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/** @namespace MathParser::Parsing::Nodes::Traits
 *
 * Traits for Nodes
 */
namespace MathParser\Parsing\Nodes\Traits;

use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\RationalNode;

/**
 * Trait for upgrading numbers (ints and floats) to NumberNode,
 * making it possible to call the Node constructors directly
 * with numbers, making the code cleaner.
 *
 */
trait Numeric {
    protected function isNumeric($operand)
    {
        return ($operand instanceof NumberNode || $operand instanceof IntegerNode || $operand instanceof RationalNode);

    }

    protected function orderType($node)
    {
        if ($node instanceof IntegerNode) return Node::NumericInteger;
        if ($node instanceof RationalNode) return Node::NumericRational;
        if ($node instanceof NumberNode) return Node::NumericFloat;

        return 0;
    }
    protected function resultingType($node, $other)
    {
        return max($this->orderType($node), $this->orderType($other));
    }
}
