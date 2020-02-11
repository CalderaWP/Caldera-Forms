<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

/**
 * @namespace MathParser::Interpreting
 * Namepace for the AST transformers implementing the Visitor interface.
 */
namespace MathParser\Interpreting;

use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Interpreting\Visitors\Visitor;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\Factories\NodeFactory;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\VariableNode;

/**
 * Differentiate an abstract syntax tree (AST).
 *
 * Implementation of a Visitor, transforming an AST into another AST
 * representing the derivative of the original AST.
 *
 * The class implements differentiation rules for all arithmetic operators
 * as well as every elementary function recognized by StdMathLexer and
 * StdmathParser, handling for example the product rule and the chain
 * rule correctly.
 *
 * To keep the resulting AST reasonably simple, a number of simplification
 * rules are built in.
 *
 * ## Example:
 *
 * ~~~{.php}
 * $parser = new StdMathParser();
 * $f = $parser->parse('exp(2x)+xy');
 * $ddx = new Differentiator('x');     // Create a d/dx operator
 * $df = $f->accept($ddx);             // $df now contains the AST of '2exp(2x)+y'
 * ~~~
 *
 * TODO: handle user specified functions
 *
 */
class Differentiator implements Visitor
{
    /**
     * Variable that we differentiate with respect to
     *
     * @var string $variable
     *
     */
    protected $variable;

    /**
     * NodeFactory $nodeFactory used for building the resulting AST.
     */
    protected $nodeFactory;

    /**
     * Class constructor
     *
     * @param string $variable Differentiation variable
     */
    public function __construct($variable)
    {
        $this->variable = $variable;

        $this->nodeFactory = new NodeFactory();
    }

    /**
     * Differentiate an ExpressionNode
     *
     * Using the usual rules for differentiating, create an ExpressionNode
     * giving an AST correctly representing the derivative `(x op y)'`
     * where `op` is one of `+`, `-`, `*`, `/` or `^`
     *
     * ### Differentiation rules:
     *
     * - \\( (f+g)' = f' + g' \\)
     * - \\( (f-g) ' = f' - g' \\)
     * - \\( (-f)' = -f' \\)
     * - \\( (f*g)' = f'g + f g' \\)
     * - \\( (f/g)' = (f' g - f g')/g^2 \\)
     * - \\( (f^g)' = f^g  (g' \\log(f) + gf'/f) \\) with a simpler expression when g is a NumberNode
     *
     *      `+`, `-`, `*`, `/` or `^`
     * @retval Node
     * @param  ExpressionNode           $node AST to be differentiated
     * @throws UnknownOperatorException if the operator is something other than
     */
    public function visitExpressionNode(ExpressionNode $node)
    {
        $operator = $node->getOperator();

        $leftValue = $node->getLeft()->accept($this);

        if ($node->getRight()) {
            $rightValue = $node->getRight()->accept($this);
        } else {
            $rightValue = null;
        }

        // Perform the right operation based on the operator
        switch ($operator) {
            case '+':
                return $this->nodeFactory->addition($leftValue, $rightValue);
            case '-':
                return $this->nodeFactory->subtraction($leftValue, $rightValue);

            // Product rule (fg)' = fg' + f'g
            case '*':
                return $this->nodeFactory->addition(
                    $this->nodeFactory->multiplication($node->getLeft(), $rightValue),
                    $this->nodeFactory->multiplication($leftValue, $node->getRight())
                );

            // Quotient rule (f/g)' = (f'g - fg')/g^2
            case '/':
                $term1 = $this->nodeFactory->multiplication($leftValue, $node->getRight());
                $term2 = $this->nodeFactory->multiplication($node->getLeft(), $rightValue);
                $numerator = $this->nodeFactory->subtraction($term1, $term2);
                $denominator = $this->nodeFactory->exponentiation($node->getRight(), new IntegerNode(2));

                return $this->nodeFactory->division($numerator, $denominator);

            // f^g = exp(g log(f)), so (f^g)' = f^g (g'log(f) + g/f)
            case '^':
                $base = $node->getLeft();
                $exponent = $node->getRight();

                if ($exponent instanceof IntegerNode || $exponent instanceof NumberNode) {
                    $power = $exponent->getValue();
                    $fpow = $this->nodeFactory->exponentiation($base, $power - 1);

                    return $this->nodeFactory->multiplication($power, $this->nodeFactory->multiplication($fpow, $leftValue));
                } elseif ($exponent instanceof RationalNode) {
                    $newPower = new RationalNode($exponent->getNumerator() - $exponent->getDenominator(), $exponent->getDenominator());
                    $fpow = $this->nodeFactory->exponentiation($base, $newPower);

                    return $this->nodeFactory->multiplication($exponent, $this->nodeFactory->multiplication($fpow, $leftValue));
                } elseif ($base instanceof ConstantNode && $base->getName() == 'e') {
                    return $this->nodeFactory->multiplication($rightValue, $node);
                } else {
                    $term1 = $this->nodeFactory->multiplication($rightValue, new FunctionNode('ln', $base));
                    $term2 = $this->nodeFactory->division(
                        $this->nodeFactory->multiplication($exponent, $base->accept($this)),
                        $base);
                    $factor2 = $this->nodeFactory->addition($term1, $term2);

                    return $this->nodeFactory->multiplication($node, $factor2);
                }

            default:
                throw new UnknownOperatorException($operator);
        }
    }

    /**
     * Differentiate a NumberNode
     *
     * Create a NumberNode representing '0'. (The derivative of
     * a constant is indentically 0).
     *
     * @retval Node
     * @param NumberNode $node AST to be differentiated
     */
    public function visitNumberNode(NumberNode $node)
    {
        return new IntegerNode(0);
    }

    public function visitIntegerNode(IntegerNode $node)
    {
        return new IntegerNode(0);
    }

    public function visitRationalNode(RationalNode $node)
    {
        return new IntegerNode(0);
    }

    /**
     * Differentiate a VariableNode
     *
     * Create a NumberNode representing '0' or '1' depending on
     * the differetiation variable.
     *
     * @retval Node
     * @param NumberNode $node AST to be differentiated
     */
    public function visitVariableNode(VariableNode $node)
    {
        if ($node->getName() == $this->variable) {
            return new IntegerNode(1);
        }

        return new IntegerNode(0);
    }

    /**
     * Differentiate a FunctionNode
     *
     * Create an ExpressionNode giving an AST correctly representing the
     * derivative `f'` where `f` is an elementary function.
     *
     * ### Differentiation rules:
     *
     *  \\( \\sin(f(x))' = f'(x)  \\cos(f(x)) \\)
     *  \\( \\cos(f(x))' = -f'(x)  \\sin(f(x)) \\)
     *  \\( \\tan(f(x))' = f'(x) (1 + \\tan(f(x))^2 \\)
     *  \\( \\operatorname{cot}(f(x))' = f'(x) (-1 - \\operatorname{cot}(f(x))^2 \\)
     *  \\( \\arcsin(f(x))' = f'(x) / \\sqrt{1-f(x)^2} \\)
     *  \\( \\arccos(f(x))' = -f'(x) / \\sqrt{1-f(x)^2} \\)
     *  \\( \\arctan(f(x))' = f'(x) / (1+f(x)^2) \\)
     *  \\( \\operatorname{arccot}(f(x))' = -f'(x) / (1+f(x)^2) \\)
     *  \\( \\exp(f(x))' = f'(x) \\exp(f(x)) \\)
     *  \\( \\log(f(x))' = f'(x) / f(x) \\)
     *  \\( \\ln(f(x))' = f'(x) / (\\log(10) * f(x)) \\)
     *  \\( \\sqrt{f(x)}' = f'(x) / (2 \\sqrt{f(x)} \\)
     *  \\( \\sinh(f(x))' = f'(x) \\cosh(f(x)) \\)
     *  \\( \\cosh(f(x))' = f'(x) \\sinh(f(x)) \\)
     *  \\( \\tanh(f(x))' = f'(x) (1-\\tanh(f(x))^2) \\)
     *  \\( \\operatorname{coth}(f(x))' = f'(x) (1-\\operatorname{coth}(f(x))^2) \\)
     *  \\( \\operatorname{arsinh}(f(x))' = f'(x) / \\sqrt{f(x)^2+1} \\)
     *  \\( \\operatorname{arcosh}(f(x))' = f'(x) / \\sqrt{f(x)^2-1} \\)
     *  \\( \\operatorname{artanh}(f(x))' = f'(x) (1-f(x)^2) \\)
     *  \\( \\operatorname{arcoth}(f(x))' = f'(x) (1-f(x)^2) \\)
     *
     *          one of the above
     * @retval Node
     * @param  FunctionNode             $node AST to be differentiated
     * @throws UnknownFunctionException if the function name doesn't match
     */
    public function visitFunctionNode(FunctionNode $node)
    {
        $inner = $node->getOperand()->accept($this);
        $arg = $node->getOperand();

        switch ($node->getName()) {
            case 'sin':
                $df = new FunctionNode('cos', $arg);
                break;
            case 'cos':
                $sin = new FunctionNode('sin', $arg);
                $df = $this->nodeFactory->unaryMinus($sin);
                break;
            case 'tan':
                $tansquare = $this->nodeFactory->exponentiation($node, 2);
                $df = $this->nodeFactory->addition(1, $tansquare);
                break;
            case 'cot':
                $cotsquare = $this->nodeFactory->exponentiation($node, 2);
                $df = $this->nodeFactory->subtraction($this->nodeFactory->unaryMinus(1), $cotsquare);
                break;

            case 'arcsin':
                $denom = new FunctionNode('sqrt',
                    $this->nodeFactory->subtraction(1, $this->nodeFactory->exponentiation($arg, 2)));

                return $this->nodeFactory->division($inner, $denom);

            case 'arccos':
                $denom = new FunctionNode('sqrt',
                    $this->nodeFactory->subtraction(1, $this->nodeFactory->exponentiation($arg, 2)));

                return $this->nodeFactory->division($this->nodeFactory->unaryMinus($inner), $denom);

            case 'arctan':
                $denom = $this->nodeFactory->addition(1, $this->nodeFactory->exponentiation($arg, 2));

                return $this->nodeFactory->division($inner, $denom);

            case 'arccot':
                $denom = $this->nodeFactory->addition(1, $this->nodeFactory->exponentiation($arg, 2));
                $df = $this->nodeFactory->unaryMinus($this->nodeFactory->division(1, $denom));
                break;

            case 'exp':
                $df = new FunctionNode('exp', $arg);
                break;
            case 'ln':
            case 'log':
                return $this->nodeFactory->division($inner, $arg);
            case 'lg':
                $denominator = $this->nodeFactory->multiplication(new FunctionNode('ln', new IntegerNode(10)), $arg);

                return $this->nodeFactory->division($inner, $denominator);

            case 'sqrt':
                $denom = $this->nodeFactory->multiplication(2, $node);

                return $this->nodeFactory->division($inner, $denom);

            case 'sinh':
                $df = new FunctionNode('cosh', $arg);
                break;

            case 'cosh':
                $df = new FunctionNode('sinh', $arg);
                break;

            case 'tanh':
                $tanhsquare = $this->nodeFactory->exponentiation(new FunctionNode('tanh', $arg), 2);
                $df = $this->nodeFactory->subtraction(1, $tanhsquare);
                break;

            case 'coth':
                $cothsquare = $this->nodeFactory->exponentiation(new FunctionNode('coth', $arg), 2);
                $df = $this->nodeFactory->subtraction(1, $cothsquare);
                break;

            case 'arsinh':
                $temp = $this->nodeFactory->addition($this->nodeFactory->exponentiation($arg, 2), 1);

                return $this->nodeFactory->division($inner, new FunctionNode('sqrt', $temp));

            case 'arcosh':
                $temp = $this->nodeFactory->subtraction($this->nodeFactory->exponentiation($arg, 2), 1);

                return $this->nodeFactory->division($inner, new FunctionNode('sqrt', $temp));

            case 'artanh':
            case 'arcoth':
                $denominator = $this->nodeFactory->subtraction(1, $this->nodeFactory->exponentiation($arg, 2));

                return $this->nodeFactory->division($inner, $denominator);

            case 'abs':
                $df = new FunctionNode('sgn', $arg);
                break;

            default:
                throw new UnknownFunctionException($node->getName());
        }

        return $this->nodeFactory->multiplication($inner, $df);
    }

    /**
     * Differentiate a ConstantNode
     *
     * Create a NumberNode representing '0'. (The derivative of
     * a constant is indentically 0).
     *
     * @retval Node
     * @param ConstantNode $node AST to be differentiated
     */
    public function visitConstantNode(ConstantNode $node)
    {
        if ($node->getName() == 'NAN') {
            return $node;
        }

        return new IntegerNode(0);
    }
}
