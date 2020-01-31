<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

namespace MathParser\Interpreting;

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\UnknownConstantException;
use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Exceptions\UnknownVariableException;
use MathParser\Extensions\Math;
use MathParser\Interpreting\Visitors\Visitor;
use MathParser\Lexer\StdMathLexer;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\VariableNode;

/**
 * Evalutate a parsed mathematical expression.
 *
 * Implementation of a Visitor, transforming an AST into a floating
 * point number, giving the *value* of the expression represented by
 * the AST.
 *
 * The class implements evaluation of all all arithmetic operators
 * as well as every elementary function and predefined constant recognized
 * by StdMathLexer and StdmathParser.
 *
 * ## Example:
 * ~~~{.php}
 * $parser = new StdMathParser();
 * $f = $parser->parse('exp(2x)+xy');
 * $evaluator = new Evaluator();
 * $evaluator->setVariables([ 'x' => 1, 'y' => '-1' ]);
 * result = $f->accept($evaluator);    // Evaluate $f using x=1, y=-1
 * ~~~
 *
 * TODO: handle user specified functions
 *
 */
class Evaluator implements Visitor
{
    /**
     * mixed[] $variables Key/value pair holding current values
     *      of the variables used for evaluating.
     *
     */
    private $variables;

    /**
     * Constructor. Create an Evaluator with given variable values.
     *
     * @param mixed $variables key-value array of variables with corresponding values.
     */
    public function __construct($variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * Update the variables used for evaluating
     *
     * @retval void
     * @param array $variables Key/value pair holding current variable values
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * Evaluate an ExpressionNode
     *
     * Computes the value of an ExpressionNode `x op y`
     * where `op` is one of `+`, `-`, `*`, `/` or `^`
     *
     *      `+`, `-`, `*`, `/` or `^`
     * @retval float
     * @param  ExpressionNode           $node AST to be evaluated
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
                return $leftValue + $rightValue;
            case '-':
                return $rightValue === null ? -$leftValue : $leftValue - $rightValue;
            case '*':
                return $rightValue * $leftValue;
            case '/':
                if ($rightValue == 0) {
                    throw new DivisionByZeroException();
                }

                return $leftValue / $rightValue;
            case '^':
                // Check for base equal to M_E, to take care of PHP's strange
                // implementation of pow, where pow(M_E, x) is not necessarily
                // equal to exp(x).
                if ($leftValue == M_E) {
                    return exp($rightValue);
                } else {
                    return pow($leftValue, $rightValue);
                }

            default:
                throw new UnknownOperatorException($operator);
        }
    }

    /**
     * Evaluate a NumberNode
     *
     * Retuns the value of an NumberNode
     *
     * @retval float
     * @param NumberNode $node AST to be evaluated
     */
    public function visitNumberNode(NumberNode $node)
    {
        return $node->getValue();
    }

    public function visitIntegerNode(IntegerNode $node)
    {
        return $node->getValue();
    }

    public function visitRationalNode(RationalNode $node)
    {
        return $node->getValue();
    }

    /**
     * Evaluate a VariableNode
     *
     * Returns the current value of a VariableNode, as defined
     * either by the constructor or set using the `Evaluator::setVariables()` method.
     *
     *      VariableNode is *not* set.
     * @retval float
     * @see Evaluator::setVariables() to define the variables
     *
     * @param  VariableNode             $node AST to be evaluated
     * @throws UnknownVariableException if the variable respresented by the
     */
    public function visitVariableNode(VariableNode $node)
    {
        $name = $node->getName();

        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        throw new UnknownVariableException($name);
    }

    /**
     * Evaluate a FunctionNode
     *
     * Computes the value of a FunctionNode `f(x)`, where f is
     * an elementary function recognized by StdMathLexer and StdMathParser.
     *
     *      FunctionNode is *not* recognized.
     * @retval float
     * @see \MathParser\Lexer\StdMathLexer StdMathLexer
     * @see \MathParser\StdMathParser StdMathParser
     *
     * @param  FunctionNode             $node AST to be evaluated
     * @throws UnknownFunctionException if the function respresented by the
     */
    public function visitFunctionNode(FunctionNode $node)
    {
        $inner = $node->getOperand()->accept($this);

        switch ($node->getName()) {
            // Trigonometric functions
            case 'sin':
                return sin($inner);

            case 'cos':
                return cos($inner);

            case 'tan':
                return tan($inner);

            case 'cot':
                $tan_inner = tan($inner);
                if ($tan_inner == 0) {
                    return NAN;
                }

                return 1 / $tan_inner;

            // Trigonometric functions, argument in degrees
            case 'sind':
                return sin(deg2rad($inner));

            case 'cosd':
                return cos(deg2rad($inner));

            case 'tand':
                return tan(deg2rad($inner));

            case 'cotd':
                $tan_inner = tan(deg2rad($inner));
                if ($tan_inner == 0) {
                    return NAN;
                }

                return 1 / $tan_inner;

            // Inverse trigonometric functions
            case 'arcsin':
                return asin($inner);

            case 'arccos':
                return acos($inner);

            case 'arctan':
                return atan($inner);

            case 'arccot':
                return pi() / 2 - atan($inner);

            // Exponentials and logarithms
            case 'exp':
                return exp($inner);

            case 'log':
            case 'ln':
                return log($inner);

            case 'lg':
                return log10($inner);

            // Powers
            case 'sqrt':
                return sqrt($inner);

            // Hyperbolic functions
            case 'sinh':
                return sinh($inner);

            case 'cosh':
                return cosh($inner);

            case 'tanh':
                return tanh($inner);

            case 'coth':
                $tanh_inner = tanh($inner);
                if ($tanh_inner == 0) {
                    return NAN;
                }

                return 1 / $tanh_inner;

            // Inverse hyperbolic functions
            case 'arsinh':
                return asinh($inner);

            case 'arcosh':
                return acosh($inner);

            case 'artanh':
                return atanh($inner);

            case 'arcoth':
                return atanh(1 / $inner);

            case 'abs':
                return abs($inner);

            case 'sgn':
                return $inner >= 0 ? 1 : -1;

            case '!':
                $logGamma = Math::logGamma(1 + $inner);

                return exp($logGamma);

            case '!!':
                if (round($inner) != $inner) {
                    throw new \UnexpectedValueException("Expecting positive integer (semifactorial)");
                }

                return Math::SemiFactorial($inner);

            // Rounding functions
            case 'round':
                return round($inner);

            case 'floor':
                return floor($inner);

            case 'ceil':
                return ceil($inner);

            default:
                throw new UnknownFunctionException($node->getName());
        }
    }

    /**
     * Evaluate a ConstantNode
     *
     * Returns the value of a ConstantNode recognized by StdMathLexer and StdMathParser.
     *
     *      ConstantNode is *not* recognized.
     * @retval float
     * @see \MathParser\Lexer\StdMathLexer StdMathLexer
     * @see \MathParser\StdMathParser StdMathParser
     *
     * @param  ConstantNode             $node AST to be evaluated
     * @throws UnknownConstantException if the variable respresented by the
     */
    public function visitConstantNode(ConstantNode $node)
    {
        switch ($node->getName()) {
            case 'pi':
                return M_PI;
            case 'e':
                return exp(1);
            case 'NAN':
                return NAN;
            case 'INF':
                return INF;
            default:
                throw new UnknownConstantException($node->getName());
        }
    }
}
