<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

namespace MathParser\Interpreting;

use MathParser\Exceptions\UnknownConstantException;
use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Exceptions\UnknownVariableException;
use MathParser\Extensions\Complex;
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
 * Implementation of a Visitor, transforming an AST into a rational
 * number, giving the *value* of the expression represented by
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
 * $evaluator = new RationalEvaluator();
 * $evaluator->setVariables([ 'x' => '1/2', 'y' => -1 ]);
 * result = $f->accept($evaluator);    // Evaluate $f using x=1/2, y=-1.
 * Note that rational variable values should be specified as a string.
 * ~~~
 *
 * TODO: handle user specified functions
 *
 */
class ComplexEvaluator implements Visitor
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
    public function __construct($variables = null)
    {
        $this->setVariables($variables);
    }

    /**
     * Update the variables used for evaluating
     *
     * @retval void
     * @param array $variables Key/value pair holding current variable values
     */
    public function setVariables($variables)
    {
        $this->variables = [];
        foreach ($variables as $var => $value) {
            $this->variables[$var] = Complex::parse($value);
        }
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

        $a = $node->getLeft()->accept($this);

        if ($node->getRight()) {
            $b = $node->getRight()->accept($this);
        } else {
            $b = null;
        }

        // Perform the right operation based on the operator
        switch ($operator) {
            case '+':
                return Complex::add($a, $b);
            case '-':
                if ($b === null) {
                    return Complex::mul($a, -1);
                }

                return Complex::sub($a, $b);
            case '*':
                return Complex::mul($a, $b);
            case '/':
                return Complex::div($a, $b);
            case '^':
                // This needs to be improved.
                return Complex::pow($a, $b);
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
        return Complex::create($node->getValue(), 0);
    }

    public function visitIntegerNode(IntegerNode $node)
    {
        return Complex::create($node->getValue(), 0);
    }

    public function visitRationalNode(RationalNode $node)
    {
        return Complex::create("$node", 0);
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
        $z = $node->getOperand()->accept($this);
        $a = $z->r();
        $b = $z->i();

        switch ($node->getName()) {
            // Trigonometric functions
            case 'sin':
                return Complex::sin($z);

            case 'cos':
                return Complex::cos($z);

            case 'tan':
                return Complex::tan($z);

            case 'cot':
                return Complex::cot($z);

            // Inverse trigonometric functions
            case 'arcsin':
                return Complex::arcsin($z);

            case 'arccos':
                return Complex::arccos($z);

            case 'arctan':
                return Complex::arctan($z);

            case 'arccot':
                return Complex::arccot($z);

            case 'sinh':
                return Complex::sinh($z);

            case 'cosh':
                return Complex::cosh($z);

            case 'tanh':
                return Complex::tanh($z);

            case 'coth':
                return Complex::div(1, Complex::tanh($z));

            case 'arsinh':
                return Complex::arsinh($z);

            case 'arcosh':
                return Complex::arcosh($z);

            case 'artanh':
                return Complex::artanh($z);

            case 'arcoth':
                return Complex::div(1, Complex::artanh($z));

            case 'exp':
                return Complex::exp($z);

            case 'ln':
                if ($z->i() != 0 || $z->r() <= 0) {
                    throw new \UnexpectedValueException("Expecting positive real number (ln)");
                }

                return Complex::log($z);

            case 'log':
                return Complex::log($z);

            case 'lg':
                return Complex::div(Complex::log($z), M_LN10);

            case 'sqrt':
                return Complex::sqrt($z);

            case 'abs':
                return new Complex($z->abs(), 0);

            case 'arg':
                return new Complex($z->arg(), 0);

            case 're':
                return new Complex($z->r(), 0);

            case 'im':
                return new Complex($z->i(), 0);

            case 'conj':
                return new Complex($z->r(), -$z->i());

            default:
                throw new UnknownFunctionException($node->getName());
        }

        return new FunctionNode($node->getName(), $inner);
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
                return new Complex(M_PI, 0);
            case 'e':
                return new Complex(M_E, 0);
            case 'i':
                return new Complex(0, 1);
            default:
                throw new UnknownConstantException($node->getName());
        }
    }
}
