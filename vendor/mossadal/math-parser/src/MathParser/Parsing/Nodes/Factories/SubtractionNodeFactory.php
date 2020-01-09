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

/**
 * Factory for creating an ExpressionNode representing '-'.
 *
 * Some basic simplification is applied to the resulting Node.
 *
 */
class SubtractionNodeFactory implements ExpressionNodeFactory
{
    use Sanitize;
    use Numeric;

    /**
    * Create a Node representing 'leftOperand - rightOperand'
    *
    * Using some simplification rules, create a NumberNode or ExpressionNode
    * giving an AST correctly representing 'rightOperand - leftOperand'.
    *
    * ### Simplification rules:
    *
    * - To simplify the use of the function, convert integer params to NumberNodes
    * - If $rightOperand is null, return a unary minus node '-x' instead
    * - If $leftOperand and $rightOperand are both NumberNodes, return a single NumberNode containing their difference
    * - If $rightOperand is a NumberNode representing 0, return $leftOperand unchanged
    * - If $leftOperand and $rightOperand are equal, return '0'
    *
    * @param Node|int|float $leftOperand Minuend
    * @param Node|int|float $rightOperand Subtrahend
    * @retval Node
    */
    public function makeNode($leftOperand, $rightOperand)
    {
        if ($rightOperand === null) return $this->createUnaryMinusNode($leftOperand);

        $leftOperand = $this->sanitize($leftOperand);
        $rightOperand = $this->sanitize($rightOperand);

        $node = $this->numericTerms($leftOperand, $rightOperand);
        if ($node) return $node;

        if ($leftOperand->compareTo($rightOperand)) {
            return new IntegerNode(0);
        }

        return new ExpressionNode($leftOperand, '-', $rightOperand);
    }

    /** Simplify subtraction nodes for numeric operands
     * @param Node $leftOperand
     * @param Node $rightOperand
     * @retval Node|null
     */
    protected function numericTerms($leftOperand, $rightOperand)
    {
        if ($this->isNumeric($rightOperand) && $rightOperand->getValue() == 0) return $leftOperand;

        if (!$this->isNumeric($leftOperand) || !$this->isNumeric($rightOperand)) {
            return null;
        }

        $type = $this->resultingType($leftOperand, $rightOperand);

        switch($type) {
            case Node::NumericFloat:
            return new NumberNode($leftOperand->getValue() - $rightOperand->getValue());

            case Node::NumericRational:
            $p = $leftOperand->getNumerator() * $rightOperand->getDenominator() - $leftOperand->getDenominator() * $rightOperand->getNumerator();
            $q = $leftOperand->getDenominator() * $rightOperand->getDenominator();
            return new RationalNode($p, $q);

            case Node::NumericInteger:
            return new IntegerNode($leftOperand->getValue() - $rightOperand->getValue());
        }

        return null;
    }

    /**
     * Create a Node representing '-$operand'
     *
     * Using some simplification rules, create a NumberNode or ExpressionNode
     * giving an AST correctly representing '-$operand'.
     *
     * ### Simplification rules:
     *
     * - To simplify the use of the function, convert integer params to NumberNodes
     * - If $operand is a NumberNodes, return a single NumberNode containing its negative
     * - If $operand already is a unary minus, 'x=-y', return y
     *
     * @param Node|int $operand Operand
     * @retval Node
     */
    public function createUnaryMinusNode($operand)
    {
        $operand = $this->sanitize($operand);

        if ($operand instanceof NumberNode) {
            return new NumberNode(-$operand->getValue());
        }
        if ($operand instanceof IntegerNode) {
            return new IntegerNode(-$operand->getValue());
        }
        if ($operand instanceof RationalNode) {
            return new RationalNode(-$operand->getNumerator(), $operand->getDenominator());
        }
        // --x => x
        if ($operand instanceof ExpressionNode && $operand->getOperator() == '-' && $operand->getRight() === null) {
            return $operand->getLeft();
        }
        return new ExpressionNode($operand, '-', null);
    }

}
