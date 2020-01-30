<?php
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/


/**
* @namespace MathParser::Parsing
*
* Parser related classes
*/
namespace MathParser\Parsing;

use MathParser\Lexing\Token;
use MathParser\Lexing\TokenType;
use MathParser\Lexing\TokenPrecedence;
use MathParser\Lexing\TokenAssociativity;

use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\SubExpressionNode;
use MathParser\Parsing\Nodes\PostfixOperatorNode;
use MathParser\Parsing\Nodes\Factories\NodeFactory;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\RationalNode;

use MathParser\Parsing\Stack;

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Exceptions\ParenthesisMismatchException;

use MathParser\Interpreting\PrettyPrinter;

/**
* Mathematical expression parser, based on the shunting yard algorithm.
*
* Parse a token string into an abstract syntax tree (AST).
*
* As the parser loops over the individual tokens, two stacks are kept
* up to date. One stack ($operatorStack) consists of hitherto unhandled
* tokens corresponding to ''operators'' (unary and binary operators, function
* applications and parenthesis) and a stack of parsed sub-expressions (the
* $operandStack).
*
* If the current token is a terminal token (number, variable or constant),
* a corresponding node is pushed onto the operandStack.
*
* Otherwise, the precedence of the current token is compared to the top
* element(t) on the operatorStack, and as long as the current token has
* lower precedence, we keep popping operators from the stack to constuct
* more complicated subexpressions together with the top items on the operandStack.
*
* Once the token list is empty, we pop the remaining operators as above, and
* if the formula was well-formed, the only thing remaining on the operandStack
* is a completely parsed AST, which we return.
*/
class Parser
{
    /**
    * Token[] list of tokens to process
    */
    protected $tokens;
    /**
    * Stack stack of operators waiting to process
    */
    protected $operatorStack;
    /**
    * Stack stack of operands waiting to process
    */
    protected $operandStack;
    /**
     * NodeFactory
     */
     protected $nodeFactory;

    protected $rationalFactory = false;
    protected $simplifyingParser = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nodeFactory = new NodeFactory();
    }

    public function setRationalFactory($flag)
    {
        $this->rationalFactory = $flag;
    }

    public function setSimplifying($flag)
    {
        $this->simplifyingParser = $flag;
    }

    /**
    * Parse list of tokens
    *
    * @param array $tokens Array (Token[]) of input tokens.
    * @retval Node AST representing the parsed expression.
    */
    public function parse(array $tokens)
    {
        // Filter away any whitespace
        $tokens = $this->filterTokens($tokens);

        // Insert missing implicit multiplication tokens
        if (static::allowImplicitMultiplication()) {
            $tokens = $this->parseImplicitMultiplication($tokens);
        }

        $this->tokens = $tokens;

        // Perform the actual parsing
        return $this->shuntingYard($tokens);
    }

    /**
    * Implementation of the shunting yard parsing algorithm
    *
    * @param array $tokens Token[] array of tokens to process
    * @retval Node AST of the parsed expression
    * @throws SyntaxErrorException
    * @throws ParenthesisMismatchException
    */
    private function shuntingYard(array $tokens)
    {
        // Clear the oepratorStack
        $this->operatorStack = new Stack();

        // Clear the operandStack.
        $this->operandStack = new Stack();

        // Remember the last token handled, this is done to recognize unary operators.
        $lastNode = null;

        // Loop over the tokens
        for ($index = 0; $index < count($tokens); $index++)
        {
            $token = $tokens[$index];

            // echo "current token $token\n";
            // echo("operands:" . $this->operandStack . "\n");
            // echo("operators: " . $this->operatorStack . "\n\n");

            if ($this->rationalFactory) {
                $node = Node::rationalFactory($token);
            } else {
                $node = Node::factory($token);
            }

            // Handle closing parentheses
            if ($token->getType() == TokenType::CloseParenthesis) {
                $this->handleSubExpression();
            }
            // Push terminal tokens on the operandStack
            elseif ($node->isTerminal()) {
                $this->operandStack->push($node);

                // Push function applications or open parentheses `(` onto the operatorStack
            } elseif ($node instanceof FunctionNode) {
                $this->operatorStack->push($node);

            } elseif ($node instanceof SubExpressionNode) {
                $this->operatorStack->push($node);

                // Handle the remaining operators.
            } elseif ($node instanceof PostfixOperatorNode) {
                $op = $this->operandStack->pop();
                if ($op == NULL) throw new SyntaxErrorException();
                $this->operandStack->push(new FunctionNode($node->getOperator(), $op));

            } elseif ($node instanceof ExpressionNode) {

                // Check for unary minus and unary plus.
                $unary = $this->isUnary($node, $lastNode);

                if ($unary) {
                    switch($token->getType()) {
                        // Unary +, just ignore it
                        case TokenType::AdditionOperator:
                        $node = null;
                        break;
                        // Unary -, replace the token.
                        case TokenType::SubtractionOperator:
                        $node->setOperator('~');
                        break;
                    }
                } else {
                    // Pop operators with higher priority

                    while ($node->lowerPrecedenceThan($this->operatorStack->peek())) {

                        $popped = $this->operatorStack->pop();
                        $popped = $this->handleExpression($popped);
                        $this->operandStack->push($popped);
                    }
                }

                if ($node) $this->operatorStack->push($node);
            }

            // Remember the current token (if it hasn't been nulled, for example being a unary +)
            if ($node) $lastNode = $node;
        }


        // Pop remaining operators

        while (!$this->operatorStack->isEmpty()) {
            $node = $this->operatorStack->pop();
            $node = $this->handleExpression($node);
            $this->operandStack->push($node);
        }

        // Stack should be empty here
        if ($this->operandStack->count() > 1) {
            throw new SyntaxErrorException();
        }

        return $this->operandStack->pop();
    }


    /**
    * Populate node with operands.
    *
    * @param Node $node
    * @retval Node
    * @throws SyntaxErrorException
    */
    protected function handleExpression($node)
    {
        if ($node instanceof FunctionNode) throw new ParenthesisMismatchException($node->getOperator());
        if ($node instanceof SubExpressionNode) throw new ParenthesisMismatchException($node->getOperator());

        if (!$this->simplifyingParser) return $this->naiveHandleExpression($node);

        if ($node->getOperator() == '~') {

            $left = $this->operandStack->pop();
            if ($left === null) {
                throw new SyntaxErrorException();
            }

            if ($left instanceof NumberNode) {
                return new NumberNode(-$left->getValue());
            }

            if ($left instanceof IntegerNode) {
                return new IntegerNode(-$left->getValue());
            }

            if ($left instanceof RationalNode) {
                return new RationalNode(-$left->getNumerator(), $left->getDenominator());
            }

            $node->setOperator('-');
            $node->setLeft($left);

            return $this->nodeFactory->simplify($node);
        }

        $right = $this->operandStack->pop();
        $left = $this->operandStack->pop();
        if ($right === null || $left === null) {
            throw new SyntaxErrorException();
        }

        $node->setLeft($left);
        $node->setRight($right);

        return $this->nodeFactory->simplify($node);
    }

    /**
    * Populate node with operands, without any simplification.
    *
    * @param Node $node
    * @retval Node
    * @throws SyntaxErrorException
    */
    protected function naiveHandleExpression($node)
    {
        if ($node->getOperator() == '~') {

            $left = $this->operandStack->pop();
            if ($left === null) {
                throw new SyntaxErrorException();
            }

            $node->setOperator('-');
            $node->setLeft($left);

            return $node;
        }

        $right = $this->operandStack->pop();
        $left = $this->operandStack->pop();
        if ($right === null || $left === null) {
            throw new SyntaxErrorException();
        }

        $node->setLeft($left);
        $node->setRight($right);

        return $node;
    }

    /**
    * Remove Whitespace from the token list.
    *
    * @param array $tokens Input list of tokens
    * @retval Token[]
    */
    protected function filterTokens(array $tokens)
    {
        $filteredTokens = array_filter($tokens, function (Token $t) {
            return $t->getType() !== TokenType::Whitespace;
        });

        // Return the array values only, because array_filter preserves the keys
        return array_values($filteredTokens);
    }

    /**
    * Insert multiplication tokens where needed (taking care of implicit mulitplication).
    *
    * @param array $tokens Input list of tokens (Token[])
    * @retval Token[]
    */
    protected function parseImplicitMultiplication(array $tokens)
    {
        $result = [];
        $lastToken = null;
        foreach ($tokens as $token) {
            if (Token::canFactorsInImplicitMultiplication($lastToken, $token)) {
                $result[] = new Token('*', TokenType::MultiplicationOperator);
            }
            $lastToken = $token;
            $result[] = $token;
        }
        return $result;
    }

    /**
    * Determine if the parser allows implicit multiplication. Create a
    * subclass of Parser, overriding this function, returning false instead
    * to diallow implicit multiplication.
    *
    * ### Example:
    *
    * ~~~{.php}
    * class ParserWithoutImplicitMultiplication extends Parser {
    *   protected static function allowImplicitMultiplication() {
    *     return false;
    *   }
    * }
    *
    * $lexer = new StdMathLexer();
    * $tokens = $lexer->tokenize('2x');
    * $parser = new ParserWithoutImplicitMultiplication();
    * $node = $parser->parse($tokens); // Throws a SyntaxErrorException
    * ~~~
    * @property allowImplicitMultiplication
    * @retval boolean
    */
    protected static function allowImplicitMultiplication()
    {
        return true;
    }

    /**
     * Determine if $node is in fact a unary operator.
     *
     * If $node can be a unary operator (i.e. is a '+' or '-' node),
     * **and** this is the first node we parse or the previous node
     * was a SubExpressionNode, i.e. an opening parenthesis, or the
     * previous node was already a unary minus, this means that the
     * current node is in fact a unary '+' or '-' and we return true,
     * otherwise we return false.
     *
     * @param Node $node Current node
     * @param Node|null $lastNode Previous node handled by the Parser
     * @retval boolean
     */
    protected function isUnary($node, $lastNode)
    {
        if (!($node->canBeUnary())) return false;

        // Unary if it is the first token
        if ($this->operatorStack->isEmpty() && $this->operandStack->isEmpty()) return true;
        // or if the previous token was '('
        if ($lastNode instanceof SubExpressionNode) return true;
        // or last node was already a unary minus
        if ($lastNode instanceof ExpressionNode && $lastNode->getOperator() == '~') return true;

        return false;
    }

    /** Handle a closing parenthesis, popping operators off the
     * operator stack until we find a matching opening parenthesis.
     *
     * @throws ParenthesisMismatchException
     */
    protected function handleSubExpression()
    {
        // Flag, checking for mismatching parentheses
        $clean = false;

        // Pop operators off the operatorStack until its empty, or
        // we find an opening parenthesis, building subexpressions
        // on the operandStack as we go.
        while ($popped = $this->operatorStack->pop()) {

            // ok, we have our matching opening parenthesis
            if ($popped instanceof SubExpressionNode) {
                $clean = true;
                break;
            }

            $node = $this->handleExpression($popped);
            $this->operandStack->push($node);

        }

        // Throw an error if the parenthesis couldn't be matched
        if (!$clean) {
            throw new ParenthesisMismatchException();
        }


        // Check to see if the parenthesis pair was in fact part
        // of a function application. If so, create the corresponding
        // FunctionNode and push it onto the operandStack.
        $previous = $this->operatorStack->peek();
        if ($previous instanceof FunctionNode) {
            $node = $this->operatorStack->pop();
            $operand = $this->operandStack->pop();
            $node->setOperand($operand);
            $this->operandStack->push($node);
        }
    }

}
