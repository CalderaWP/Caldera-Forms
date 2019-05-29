<?php
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
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
class ASCIIPrinter implements Visitor
{
    /**
     * StdMathLexer $lexer
     */
    private $lexer;

    /**
     * Constructor. Create an ASCIIPrinter.
     */
    public function __construct()
    {
        $this->lexer = new StdMathLexer();
    }

    /**
     * Generate ASCII output code for an ExpressionNode
     *
     * Create a string giving ASCII output representing an ExpressionNode `(x op y)`
     * where `op` is one of `+`, `-`, `*`, `/` or `^`
     *
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
            case '/':

                $leftValue = $this->parenthesize($left, $node, '', false);
                $rightValue = $this->parenthesize($right, $node, '', true);

                return "$leftValue$operator$rightValue";

            case '^':
                $leftValue = $this->parenthesize($left, $node, '', true);
                $rightValue = $this->parenthesize($right, $node, '', false);

                return "$leftValue$operator$rightValue";
        }
    }

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

        //if ($p < 1) return "($p/$q)";

        return "$p/$q";
    }

    public function visitVariableNode(VariableNode $node)
    {
        return (string) ($node->getName());
    }

    private function visitFactorialNode(FunctionNode $node)
    {
        $functionName = $node->getName();
        $op = $node->getOperand();
        $operand = $op->accept($this);

        // Add parentheses most of the time.
        if ($op instanceof NumberNode || $op instanceof IntegerNode || $op instanceof RationalNode) {
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

    public function visitFunctionNode(FunctionNode $node)
    {
        $functionName = $node->getName();

        if ($functionName == '!' || $functionName == '!!') {
            return $this->visitFactorialNode($node);
        }

        $operand = $node->getOperand()->accept($this);

        return "$functionName($operand)";
    }

    public function visitConstantNode(ConstantNode $node)
    {
        switch ($node->getName()) {
            case 'pi':
                return 'pi';
            case 'e':
                return 'e';
            case 'i':
                return 'i';
            case 'NAN':
                return 'NAN';
            case 'INF':
                return 'INF';

            default:throw new UnknownConstantException($node->getName());
        }
    }

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

            if ($conservative) {
                // Add parentheses more liberally for / and ^ operators,
                // so that e.g. x/(y*z) is printed correctly
                if ($cutoff->getOperator() == '/' && $node->lowerPrecedenceThan($cutoff)) {
                    return "($text)";
                }
                if ($cutoff->getOperator() == '^' && $node->getOperator() == '^') {
                    return "($text)";
                }
            }

            if ($node->strictlyLowerPrecedenceThan($cutoff)) {
                return "($text)";
            }
        }

        if (($node instanceof NumberNode || $node instanceof IntegerNode || $node instanceof RationalNode) && $node->getValue() < 0) {
            return "($text)";
        }

        // Treat rational numbers as divisions on printing
        if ($node instanceof RationalNode && $node->getDenominator() != 1) {
            $fakeNode = new ExpressionNode($node->getNumerator(), '/', $node->getDenominator());

            if ($fakeNode->lowerPrecedenceThan($cutoff)) {
                return "($text)";
            }
        }

        return "$prepend$text";
    }
}
