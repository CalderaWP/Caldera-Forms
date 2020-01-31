<?php
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

namespace MathParser\Parsing\Nodes\Factories;

use MathParser\Parsing\Nodes\Interfaces\ExpressionNodeFactory;

use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\RationalNode;

use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\Traits\Sanitize;
use MathParser\Parsing\Nodes\Traits\Numeric;

use MathParser\Exceptions\DivisionByZeroException;

/**
* Factory for creating an ExpressionNode representing '/'.
*
* Some basic simplification is applied to the resulting Node.
*
*/
class DivisionNodeFactory implements ExpressionNodeFactory
{
    use Sanitize;
    use Numeric;


    /**
    * Create a Node representing '$leftOperand/$rightOperand'
    *
    * Using some simplification rules, create a NumberNode or ExpressionNode
    * giving an AST correctly representing '$leftOperand/$rightOperand'.
    *
    * ### Simplification rules:
    *
    * - To simplify the use of the function, convert integer params to NumberNodes
    * - If $leftOperand is a NumberNode representing 0, return 0
    * - If $rightOperand is a NumberNode representing 1, return $leftOperand
    * - If $leftOperand and $rightOperand are equal, return '1'
    *
    * @param Node|int $leftOperand Numerator
    * @param Node|int $rightOperand Denominator
    * @retval Node
    */
    public function makeNode($leftOperand, $rightOperand)
    {
        $leftOperand = $this->sanitize($leftOperand);
        $rightOperand = $this->sanitize($rightOperand);

        // Return rational number?
        // if ($leftOperand instanceof NumberNode && $rightOperand instanceof NumberNode)
        //    return new NumberNode($leftOperand->getValue() / $rightOperand->getValue());

        $node = $this->numericFactors($leftOperand, $rightOperand);
        if ($node) return $node;

        if ($leftOperand->compareTo($rightOperand)) {
            return new IntegerNode(1);
        }

        return new ExpressionNode($leftOperand, '/', $rightOperand);
    }

    /** Simplify division nodes when factors are numeric
    * @param Node $leftOperand
    * @param Node $rightOperand
    * @retval Node|null
    **/
    protected function numericFactors($leftOperand, $rightOperand)
    {
        if ($this->isNumeric($rightOperand) && $rightOperand->getValue() == 0) {
            throw new DivisionByZeroException();
        }
        if ($this->isNumeric($rightOperand) && $rightOperand->getValue() == 1)
        {
            return $leftOperand;
        }
        if ($this->isNumeric($leftOperand) && $leftOperand->getValue() == 0)
        {
            return new IntegerNode(0);
        }

        if (!$this->isNumeric($leftOperand) || !$this->isNumeric($rightOperand)) {
            return null;
        }
        $type = $this->resultingType($leftOperand, $rightOperand);

        switch($type) {
            case Node::NumericFloat:
            return new NumberNode($leftOperand->getValue() / $rightOperand->getValue());

            case Node::NumericRational:
            case Node::NumericInteger:
            $p = $leftOperand->getNumerator() * $rightOperand->getDenominator();
            $q = $leftOperand->getDenominator() * $rightOperand->getNumerator();
            return new RationalNode($p, $q);
        }

        return null;
    }
}
