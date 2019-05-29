<?php
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/** @namespace MathParser::Parsing::Nodes::Factories
 *
 * Classes implementing the ExpressionNodeFactory interfaces,
 * and related functionality.
 *
 */
namespace MathParser\Parsing\Nodes\Factories;

use MathParser\Parsing\Nodes\Factories\AdditionNodeFactory;
use MathParser\Parsing\Nodes\Factories\SubtractionNodeFactory;
use MathParser\Parsing\Nodes\Factories\MultiplicationNodeFactory;
use MathParser\Parsing\Nodes\Factories\DivisionNodeFactory;
use MathParser\Parsing\Nodes\Factories\ExponentiationNodeFactory;
use MathParser\Parsing\Nodes\Factories\UnaryMinusNodeFactory;
use MathParser\Parsing\Nodes\ExpressionNode;

/**
 * Helper class for creating ExpressionNodes.
 *
 * Wrapper class, setting up factories for creating ExpressionNodes
 * of various types (one for each operator). These factories take
 * case of basic simplification.
 *
 * ### Examples
 *
 * ~~~{.php}
 * use MathParser\Parsing\Nodes\Factories\NodeFactory;
 *
 * $factory = new NodeFactory();
 * // Create AST for 'x/y + x*y'
 * $node = $factory->addition(
 *      $factory->division(new VariableNode('x'), new VariableNode('y')),
 *      $factory->multiplication(new VariableNode('x'), new VariableNode('y'))
 * );
 * ~~~
 */
class NodeFactory {
    /**
     * Factory for creating addition nodes
     *
     * @var AdditionNodeFactory $additionFactory;
     **/
    protected $additionFactory;
    /**
     * Factory for creating subtraction nodes (including unary minus)
     *
     * @var SubtractionNodeFactory $subtractionFactory;
     **/
    protected $subtractionFactory;
    /**
     * Factory for creating multiplication nodes
     *
     * @var MultiplicationNodeFactory $multiplicationFactory;
     **/
    protected $multiplicationFactory;
    /**
     * Factory for creating division nodes
     *
     * @var DivisionByZeroException $divisionFactory;
     **/
    protected $divisionFactory;
    /**
     * Factory for creating exponentiation nodes
     *
     * @var ExponentiationNodeFactory $exponentiationFactory;
     **/
    protected $exponentiationFactory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->additionFactory = new AdditionNodeFactory();
        $this->subtractionFactory = new SubtractionNodeFactory();
        $this->multiplicationFactory = new MultiplicationNodeFactory();
        $this->divisionFactory = new DivisionNodeFactory();
        $this->exponentiationFactory = new ExponentiationNodeFactory();
    }

    /**
     * Create an addition node representing '$leftOperand + $rightOperand'.
     *
     * @param mixed $leftOperand
     * @param mixed $rightOperand
     * @retval ExpressionNode
     *
     */
    public function addition($leftOperand, $rightOperand)
    {
        return $this->additionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a subtraction node representing '$leftOperand - $rightOperand'.
     *
     * @param mixed $leftOperand
     * @param mixed $rightOperand
     * @retval ExpressionNode
     *
     */
    public function subtraction($leftOperand, $rightOperand)
    {
        return $this->subtractionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a multiplication node representing '$leftOperand * $rightOperand'.
     *
     * @param mixed $leftOperand
     * @param mixed $rightOperand
     * @retval ExpressionNode
     *
     */
    public function multiplication($leftOperand, $rightOperand)
    {
        return $this->multiplicationFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a division node representing '$leftOperand / $rightOperand'.
     *
     * @param mixed $leftOperand
     * @param mixed $rightOperand
     * @retval ExpressionNode
     *
     */
    public function division($leftOperand, $rightOperand)
    {
        return $this->divisionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create an exponentiation node representing '$leftOperand ^ $rightOperand'.
     *
     * @param mixed $leftOperand
     * @param mixed $rightOperand
     * @retval ExpressionNode
     *
     */
    public function exponentiation($leftOperand, $rightOperand)
    {
        return $this->exponentiationFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a unary minus node representing '-$operand'.
     *
     * @param mixed $operand
     * @retval ExpressionNode
     *
     */
    public function unaryMinus($operand)
    {
        return $this->subtractionFactory->createUnaryMinusNode($operand);
    }

    /**
     * Simplify the given ExpressionNode, using the appropriate factory.
     *
     * @param ExpressionNode $node
     * @retval Node Simplified version of the input
     */
    public function simplify(ExpressionNode $node)
    {
        switch($node->getOperator()) {
            case '+': return $this->addition($node->getLeft(), $node->getRight());
            case '-': return $this->subtraction($node->getLeft(), $node->getRight());
            case '*': return $this->multiplication($node->getLeft(), $node->getRight());
            case '/': return $this->division($node->getLeft(), $node->getRight());
            case '^': return $this->exponentiation($node->getLeft(), $node->getRight());
        }
    }

}
