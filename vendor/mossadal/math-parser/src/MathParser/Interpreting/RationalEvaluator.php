<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
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
class RationalEvaluator implements Visitor
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

    private function isInteger($value)
    {
        return preg_match('~^\d+$~', $value);
    }

    private function isSignedInteger($value)
    {
        return preg_match('~^\-?\d+$~', $value);
    }

    public function parseRational($value)
    {
        $data = $value;

        $numbers = explode('/', $data);
        if (count($numbers) == 1) {
            $p = $this->isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = 1;
        } elseif (count($numbers) != 2) {
            $p = NAN;
            $q = NAN;
        } else {
            $p = $this->isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = $this->isInteger($numbers[1]) ? intval($numbers[1]) : NAN;
        }

        if (is_nan($p) || is_nan($q)) {
            throw new \UnexpectedValueException("Expecting rational number");
        }

        return new RationalNode($p, $q);
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
            if ($value instanceof RationalNode) {
                $this->variables[$var] = $value;
            } elseif ($this->isInteger($value)) {
                $this->variables[$var] = new RationalNode(intval($value), 1);
            } else {
                $this->variables[$var] = $this->parseRational($value);
            }
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
                $p = $a->getNumerator() * $b->getDenominator() + $a->getDenominator() * $b->getNumerator();
                $q = $a->getDenominator() * $b->getDenominator();

                return new RationalNode($p, $q);
            case '-':
                if ($b === null) {
                    return new RationalNode(-$a->getNumerator(), $a->getDenominator());
                }
                $p = $a->getNumerator() * $b->getDenominator() - $a->getDenominator() * $b->getNumerator();
                $q = $a->getDenominator() * $b->getDenominator();

                return new RationalNode($p, $q);
            case '*':
                $p = $a->getNumerator() * $b->getNumerator();
                $q = $a->getDenominator() * $b->getDenominator();

                return new RationalNode($p, $q);
            case '/':
                if ($b->getNumerator() == 0) {
                    throw new DivisionByZeroException();
                }

                $p = $a->getNumerator() * $b->getDenominator();
                $q = $a->getDenominator() * $b->getNumerator();

                return new RationalNode($p, $q);
            case '^':
                return $this->rpow($a, $b);
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
        throw new \UnexpectedValueException("Expecting rational number");
    }

    public function visitIntegerNode(IntegerNode $node)
    {
        return new RationalNode($node->getValue(), 1);
    }

    public function visitRationalNode(RationalNode $node)
    {
        return $node;
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
            case 'cos':
            case 'tan':
            case 'cot':
            case 'arcsin':
            case 'arccos':
            case 'arctan':
            case 'arccot':
            case 'exp':
            case 'log':
            case 'ln':
            case 'lg':
            case 'sinh':
            case 'cosh':
            case 'tanh':
            case 'coth':
            case 'arsinh':
            case 'arcosh':
            case 'artanh':
            case 'arcoth':
                throw new \UnexpectedValueException("Expecting rational expression");

            case 'abs':
                return new RationalNode(abs($inner->getNumerator()), $inner->getDenominator());

            case 'sgn':
                if ($inner->getNumerator() >= 0) {
                    return new RationalNode(1, 0);
                } else {
                    return new RationalNode(-1, 0);
                }

            // Powers
            case 'sqrt':
                return $this->rpow($inner, new RationalNode(1, 2));

            case '!':
                if ($inner->getDenominator() == 1 && $inner->getNumerator() >= 0) {
                    return new RationalNode(Math::Factorial($inner->getNumerator()), 1);
                }

                throw new \UnexpectedValueException("Expecting positive integer (factorial)");

            case '!!':
                if ($inner->getDenominator() == 1 && $inner->getNumerator() >= 0) {
                    return new RationalNode(Math::SemiFactorial($inner->getNumerator()), 1);
                }

                throw new \UnexpectedValueException("Expecting positive integer (factorial)");

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
            case 'e':
            case 'i':
            case 'NAN':
            case 'INF':
                throw new \UnexpectedValueException("Expecting rational number");
            default:
                throw new UnknownConstantException($node->getName());
        }
    }

    /**
     * Private cache for prime sieve
     *
     * @var array int $sieve
     *
     */
    private static $sieve = [];

    /**
     * Integer factorization
     *
     * Computes an integer factorization of $n using
     * trial division and a cached sieve of computed primes
     *
     * @param type var Description
     */
    public static function ifactor($n)
    {

        // max_n = 2^31-1 = 2147483647
        $d = 2;
        $factors = [];
        $dmax = floor(sqrt($n));

        self::$sieve = array_pad(self::$sieve, $dmax, 1);

        do {
            $r = false;
            while ($n % $d == 0) {
                if (array_key_exists($d, $factors)) {
                    $factors[$d]++;
                } else {
                    $factors[$d] = 1;
                }

                $n /= $d;
                $r = true;
            }
            if ($r) {
                $dmax = floor(sqrt($n));
            }
            if ($n > 1) {
                for ($i = $d; $i <= $dmax; $i += $d) {
                    self::$sieve[$i] = 0;
                }
                do {
                    $d++;
                } while ($d < $dmax && self::$sieve[$d] != 1);

                if ($d > $dmax) {
                    if (array_key_exists($n, $factors)) {
                        $factors[$n]++;
                    } else {
                        $factors[$n] = 1;
                    }
                }
            }
        } while ($n > 1 && $d <= $dmax);

        return $factors;
    }

    /**
     * Compute a power free integer factorization: n = pq^d,
     * where p is d-power free.
     *
     * The function returns an array:
     * [
     *    'square' => q,
     *    'nonSquare' => p
     * ]
     *
     * @param int $n input
     */
    public static function powerFreeFactorization($n, $d)
    {
        $factors = self::ifactor($n);

        $square = 1;
        $nonSquare = 1;

        foreach ($factors as $prime => $exponent) {
            $remainder = $exponent % $d;

            if ($remainder != 0) {
                $reducedExponent = ($exponent - $remainder) / $d;
                $nonSquare *= $prime;
            } else {
                $reducedExponent = $exponent / $d;
            }
            $square *= pow($prime, $reducedExponent);
        }

        return ['square' => $square, 'nonSquare' => $nonSquare];
    }

    private function rpow($a, $b)
    {
        if ($b->getDenominator() == 1) {
            $n = $b->getNumerator();
            if ($n >= 0) {
                return new RationalNode(pow($a->getNumerator(), $n), pow($a->getDenominator(), $n));
            } else {
                return new RationalNode(pow($a->getDenominator(), -$n), pow($a->getNumerator(), -$n));
            }
        }
        if ($a->getNumerator() < 0) {
            throw new \UnexpectedValueException("Expecting rational number");
        }

        $p = $a->getNumerator();
        $q = $a->getDenominator();

        $alpha = $b->getNumerator();
        $beta = $b->getDenominator();

        if ($alpha < 0) {
            $temp = $p;
            $p = $q;
            $q = $temp;
            $alpha = -$alpha;
        }

        $pp = pow($p, $alpha);
        $qq = pow($q, $alpha);

        $ppFactors = self::powerFreeFactorization($pp, $beta);
        $qqFactors = self::powerFreeFactorization($qq, $beta);

        if ($ppFactors['nonSquare'] == 1 && $qqFactors['nonSquare'] == 1) {
            return new RationalNode($ppFactors['square'], $qqFactors['square']);
        }

        throw new \UnexpectedValueException("Expecting rational number");
    }
}
