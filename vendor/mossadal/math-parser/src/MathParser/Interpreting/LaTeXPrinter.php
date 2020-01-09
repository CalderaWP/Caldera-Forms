<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

namespace MathParser\Interpreting;

use MathParser\Exceptions\UnknownConstantException;
use MathParser\Interpreting\Visitors\Visitor;
use MathParser\Lexing\StdMathLexer;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\VariableNode;

/**
 * Create LaTeX code for prettyprinting a mathematical expression
 * (for example via MathJax)
 *
 * Implementation of a Visitor, transforming an AST into a string
 * giving LaTeX code for the expression.
 *
 * The class in general does *not* generate the best possible LaTeX
 * code, and needs more work to be used in a production setting.
 *
 * ## Example:
 * ~~~{.php}
 * $parser = new StdMathParser();
 * $f = $parser->parse('exp(2x)+xy');
 * printer = new LaTeXPrinter();
 * result = $f->accept($printer);    // Generates "e^{2x}+xy"
 * ~~~
 *
 * Note that surrounding `$`, `$$` or `\begin{equation}..\end{equation}`
 * has to be added manually.
 *
 */
class LaTeXPrinter implements Visitor
{
    /**
     * StdMathLexer $lexer
     */
    private $lexer;

    /**
     * Flag to determine if division should be typeset
     * with a solidus, e.g. x/y or a proper fraction \frac{x}{y}
     */
    private $solidus = false;

    /**
     * Constructor. Create a LaTeXPrinter.
     */
    public function __construct()
    {
        $this->lexer = new StdMathLexer();
    }

    /**
     * Generate LaTeX code for an ExpressionNode
     *
     * Create a string giving LaTeX code for an ExpressionNode `(x op y)`
     * where `op` is one of `+`, `-`, `*`, `/` or `^`
     *
     * ### Typesetting rules:
     *
     * - Adds parentheses around each operand, if needed. (I.e. if their precedence
     *   lower than that of the current Node.) For example, the AST `(^ (+ 1 2) 3)`
     *   generates `(1+2)^3` but `(+ (^ 1 2) 3)` generates `1^2+3` as expected.
     * - Multiplications are typeset implicitly `(* x y)` returns `xy` or using
     *   `\cdot` if the first factor is a FunctionNode or the (left operand) in the
     *   second factor is a NumberNode, so `(* x 2)` return `x \cdot 2` and `(* (sin x) x)`
     *   return `\sin x \cdot x` (but `(* x (sin x))` returns `x\sin x`)
     * - Divisions are typeset using `\frac`
     * - Exponentiation adds braces around the power when needed.
     *
     * @retval string
     * @param ExpressionNode $node AST to be typeset
     */
    public function visitExpressionNode(ExpressionNode $node)
    {

        $operator = $node->getOperator();
        $left = $node->getLeft();
        $right = $node->getRight();

        switch ($operator) {
            case '+':
                $leftValue = $left->accept($this);
                $rightValue = $this->parenthesize($right, $node);

                return "$leftValue+$rightValue";

            case '-':
                if ($right) {
                    // Binary minus

                    $leftValue = $left->accept($this);
                    $rightValue = $this->parenthesize($right, $node);

                    return "$leftValue-$rightValue";
                } else {
                    // Unary minus

                    $leftValue = $this->parenthesize($left, $node);

                    return "-$leftValue";
                }

            case '*':
                $operator = '';
                if ($this->MultiplicationNeedsCdot($left, $right)) {
                    $operator = '\cdot ';
                }
                $leftValue = $this->parenthesize($left, $node);
                $rightValue = $this->parenthesize($right, $node);

                return "$leftValue$operator$rightValue";

            case '/':
                if ($this->solidus) {
                    $leftValue = $this->parenthesize($left, $node);
                    $rightValue = $this->parenthesize($right, $node);

                    return "$leftValue$operator$rightValue";
                }

                return '\frac{' . $left->accept($this) . '}{' . $right->accept($this) . '}';

            case '^':
                $leftValue = $this->parenthesize($left, $node, '', true);

                // Typeset exponents with solidus
                $this->solidus = true;
                $result = $leftValue . '^' . $this->bracesNeeded($right);
                $this->solidus = false;

                return $result;
        }
    }

    /**
     * Check if a multiplication needs an inserted \cdot or if
     * it can be safely written with implicit multiplication.
     *
     * @retval bool
     * @param $left  AST of first factor
     * @param $right AST of second factor
     */
    private function MultiplicationNeedsCdot($left, $right)
    {
        if ($left instanceof FunctionNode) {
            return true;
        }

        if ($this->isNumeric($right)) {
            return true;
        }

        if ($right instanceof ExpressionNode && $this->isNumeric($right->getLeft())) {
            return true;
        }

        return false;
    }

    /**
     * Generate LaTeX code for a NumberNode
     *
     * Create a string giving LaTeX code for a NumberNode. Currently,
     * there is no special formatting of numbers.
     *
     * @retval string
     * @param NumberNode $node AST to be typeset
     */
    public function visitNumberNode(NumberNode $node)
    {
        $val = $node->getValue();

        return "$val";
    }

    public function visitIntegerNode(IntegerNode $node)
    {
        $val = $node->getValue();

        return "$val";
    }

    public function visitRationalNode(RationalNode $node)
    {
        $p = $node->getNumerator();
        $q = $node->getDenominator();

        if ($q == 1) {
            return "$p";
        }

        if ($this->solidus) {
            return "$p/$q";
        }

        return "\\frac{{$p}}{{$q}}";
    }

    /**
     * Generate LaTeX code for a VariableNode
     *
     * Create a string giving LaTeX code for a VariableNode. Currently,
     * there is no special formatting of variables.
     *
     * @retval string
     * @param VariableNode $node AST to be typeset
     */
    public function visitVariableNode(VariableNode $node)
    {
        return $node->getName();
    }

    /**
     * Generate LaTeX code for factorials
     *
     * @retval string
     * @param FunctionNode $node AST to be typeset
     */
    private function visitFactorialNode(FunctionNode $node)
    {
        $functionName = $node->getName();
        $op = $node->getOperand();
        $operand = $op->accept($this);

        // Add parentheses most of the time.
        if ($this->isNumeric($op)) {
            if ($op->getValue() < 0) {
                $operand = "($operand)";
            }
        } elseif ($op instanceof VariableNode || $op instanceof ConstantNode) {
            // Do nothing
        } else {
            $operand = "($operand)";
        }

        return "$operand$functionName";
    }

    /**
     * Generate LaTeX code for a FunctionNode
     *
     * Create a string giving LaTeX code for a functionNode.
     *
     * ### Typesetting rules:
     *
     * - `sqrt(op)` is typeset as `\sqrt{op}
     * - `exp(op)` is either typeset as `e^{op}`, if `op` is a simple
     *      expression or as `\exp(op)` for more complicated operands.
     *
     * @retval string
     * @param FunctionNode $node AST to be typeset
     */

    public function visitFunctionNode(FunctionNode $node)
    {
        $functionName = $node->getName();

        $operand = $node->getOperand()->accept($this);

        switch ($functionName) {
            case 'sqrt':
                return "\\$functionName{" . $node->getOperand()->accept($this) . '}';
            case 'exp':
                $operand = $node->getOperand();

                if ($operand->complexity() < 10) {
                    $this->solidus = true;
                    $result = 'e^' . $this->bracesNeeded($operand);
                    $this->solidus = false;

                    return $result;
                }
                // Operand is complex, typset using \exp instead

                return '\exp(' . $operand->accept($this) . ')';

            case 'ln':
            case 'log':
            case 'sin':
            case 'cos':
            case 'tan':
            case 'arcsin':
            case 'arccos':
            case 'arctan':
                break;

            case 'abs':
                $operand = $node->getOperand();

                return '\lvert ' . $operand->accept($this) . '\rvert ';

            case '!':
            case '!!':
                return $this->visitFactorialNode($node);

            default:
                $functionName = 'operatorname{' . $functionName . '}';
        }

        return "\\$functionName($operand)";
    }

    /**
     * Generate LaTeX code for a ConstantNode
     *
     * Create a string giving LaTeX code for a ConstantNode.
     * `pi` typesets as `\pi` and `e` simply as `e`.
     *
     * @retval string
     * @param  ConstantNode             $node AST to be typeset
     * @throws UnknownConstantException for nodes representing other constants.
     */
    public function visitConstantNode(ConstantNode $node)
    {
        switch ($node->getName()) {
            case 'pi':
                return '\pi{}';
            case 'e':
                return 'e';
            case 'i':
                return 'i';
            case 'NAN':
                return '\operatorname{NAN}';
            case 'INF':
                return '\infty{}';
            default:throw new UnknownConstantException($node->getName());
        }
    }

    /**
     *  Add parentheses to the LaTeX representation of $node if needed.
     *
     *
     *                          node. Operands with a lower precedence have parentheses
     *                          added.
     *                          be added at the beginning of the returned string.
     * @retval string
     * @param Node           $node     The AST to typeset
     * @param ExpressionNode $cutoff   A token representing the precedence of the parent
     * @param bool           $addSpace Flag determining whether an additional space should
     */
    public function parenthesize(Node $node, ExpressionNode $cutoff, $prepend = '', $conservative = false)
    {
        $text = $node->accept($this);

        if ($node instanceof ExpressionNode) {
            // Second term is a unary minus
            if ($node->getOperator() == '-' && $node->getRight() == null) {
                return "($text)";
            }

            if ($cutoff->getOperator() == '-' && $node->lowerPrecedenceThan($cutoff)) {
                return "($text)";
            }
            if ($node->strictlyLowerPrecedenceThan($cutoff)) {
                return "($text)";
            }

            if ($conservative) {
                // Add parentheses more liberally for / and ^ operators,
                // so that e.g. x/(y*z) is printed correctly
                if ($cutoff->getOperator() == '/' && $node->lowerPrecedenceThan($cutoff)) {
                    return "($text)";
                }
                if ($cutoff->getOperator() == '^' && $node->getOperator() == '^') {
                    return '{' . $text . '}';
                }
            }
        }

        if ($this->isNumeric($node) && $node->getValue() < 0) {
            return "($text)";
        }

        return "$prepend$text";
    }

    /**
     * Add curly braces around the LaTex representation of $node if needed.
     *
     * Nodes representing a single ConstantNode, VariableNode or NumberNodes (0--9)
     * are returned as-is. Other Nodes get curly braces around their LaTeX code.
     *
     * @retval string
     * @param Node $node AST to parse
     */
    public function bracesNeeded(Node $node)
    {
        if ($node instanceof VariableNode || $node instanceof ConstantNode) {
            return $node->accept($this);
        } elseif ($node instanceof IntegerNode && $node->getValue() >= 0 && $node->getValue() <= 9) {
            return $node->accept($this);
        } else {
            return '{' . $node->accept($this) . '}';
        }
    }

    /**
     * Check if Node is numeric, i.e. a NumberNode, IntegerNode or RationalNode
     *
     * @retval bool
     * @param Node $node AST to check
     */
    private function isNumeric(Node $node)
    {
        return ($node instanceof NumberNode || $node instanceof IntegerNode || $node instanceof RationalNode);
    }
}
