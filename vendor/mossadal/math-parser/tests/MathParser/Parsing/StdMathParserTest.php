<?php

use MathParser\Exceptions\ParenthesisMismatchException;
use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Interpreting\TreePrinter;
use MathParser\Lexing\StdMathLexer;
use MathParser\Lexing\Token;
use MathParser\Lexing\TokenType;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\Parsing\Parser;
use MathParser\StdMathParser;
use PHPUnit\Framework\TestCase;

class ParserWithoutImplicitMultiplication extends Parser
{
    protected static function allowImplicitMultiplication()
    {
        return false;
    }
}

class StdMathParserTest extends TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new StdMathParser();
    }

    private function assertNodesEqual($node1, $node2)
    {
        $printer = new TreePrinter();
        $message = "Node1: " . $node1->accept($printer) . "\nNode 2: " . $node2->accept($printer) . "\n";

        $this->assertTrue($node1->compareTo($node2), $message);
    }

    private function assertNumberNode($node, $value)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\NumberNode', $node);
        $this->assertEquals($value, $node->getValue());
    }

    private function assertVariableNode($node, $value)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\VariableNode', $node);
        $this->assertEquals($value, $node->getName());
    }

    private function assertCompareNodes($text)
    {
        $node1 = $this->parser->parse($text);
        $node2 = $this->parser->parse($text);

        $this->assertNodesEqual($node1, $node2);
    }

    public function testCanCompareNodes()
    {
        $this->assertCompareNodes("3");
        $this->assertCompareNodes("x");
        $this->assertCompareNodes("x+y");
        $this->assertCompareNodes("sin(x)");
        $this->assertCompareNodes("(x)");
        $this->assertCompareNodes("1+x+y");
    }

    private function assertTokenEquals($value, $type, Token $token)
    {
        $this->assertEquals($value, $token->getValue());
        $this->assertEquals($type, $token->getType());
    }

    public function testCanGetTokenList()
    {
        $node = $this->parser->parse("x+y");
        $tokens = $this->parser->getTokenList();

        $this->assertTokenEquals("x", TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals("+", TokenType::AdditionOperator, $tokens[1]);
        $this->assertTokenEquals("y", TokenType::Identifier, $tokens[2]);

    }

    public function testCanGetTree()
    {
        $node = $this->parser->parse("1+x");
        $tree = $this->parser->getTree();
        $this->assertNodesEqual($node, $tree);
    }

    public function testCanParseSingleNumberExpression()
    {
        $node = $this->parser->parse("3");
        $shouldBe = new NumberNode(3);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("3.5");
        $shouldBe = new NumberNode(3.5);
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseSingleVariable()
    {
        $node = $this->parser->parse('x');
        $shouldBe = new VariableNode('x');

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('(x)');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('((x))');
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseSingleConstant()
    {
        $node = $this->parser->parse('pi');
        $shouldBe = new ConstantNode('pi');

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('(pi)');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('((pi))');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('e');
        $shouldBe = new ConstantNode('e');

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseBinaryExpression()
    {
        $node = $this->parser->parse("x+y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '*', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x/y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '/', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '^', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseWithCorrectAssociativity()
    {
        $node = $this->parser->parse("x+y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y*z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '*', new VariableNode('y')),
            '*',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x/y/z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '/', new VariableNode('y')),
            '/',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^y^z");
        $shouldBe = new ExpressionNode(
            new VariableNode('x'),
            '^',
            new ExpressionNode(new VariableNode('y'), '^', new VariableNode('z'))
        );
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseThreeTerms()
    {
        $x = new VariableNode('x');
        $y = new VariableNode('y');
        $z = new VariableNode('z');
        $mx = new ExpressionNode($x, '-', null);

        $node = $this->parser->parse("x+y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x+y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        // First term with unary minus

        $node = $this->parser->parse("-x+y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode($mx, '+', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x+y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode($mx, '+', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x-y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode($mx, '-', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x-y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode($mx, '-', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseWithCorrectPrecedence()
    {
        $x = new VariableNode('x');
        $y = new VariableNode('y');
        $z = new VariableNode('z');

        $node = $this->parser->parse("x+y*z");

        $factors = new ExpressionNode($y, '*', $z);
        $shouldBe = new ExpressionNode($x, '+', $factors);

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y+z");
        $factors = new ExpressionNode($x, '*', $y);
        $shouldBe = new ExpressionNode($factors, '+', $z);

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseParentheses()
    {
        $node = $this->parser->parse("(x)");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("((x))");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x+1)");
        $shouldBe = $this->parser->parse("x+1");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x*y)");
        $shouldBe = $this->parser->parse("x*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x^y)");
        $shouldBe = $this->parser->parse("x^y");
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testImplicitMultiplication()
    {
        $node = $this->parser->parse("2x");
        $shouldBe = $this->parser->parse("2*x");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2xy");
        $shouldBe = $this->parser->parse("2*x*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2x^2");
        $shouldBe = $this->parser->parse("2*x^2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2x^2y");
        $shouldBe = $this->parser->parse("2*x^2*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(-x)2");
        $shouldBe = $this->parser->parse("(-x)*2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^2y^2");
        $shouldBe = $this->parser->parse("x^2*y^2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x+1)(x-1)");
        $shouldBe = $this->parser->parse("(x+1)*(x-1)");
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseUnaryOperators()
    {
        $node = $this->parser->parse("-x");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '-', null);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("+x");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x+y");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', null),
            '+',
            new VariableNode('y')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x*y");
        $shouldBe = $this->parser->parse("-(x*y)");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x^y");
        $shouldBe = $this->parser->parse("-(x^y)");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(-x)^y");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', null),
            '^',
            new VariableNode('y')
        );
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testSyntaxErrorException()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('1+');
    }

    public function testSyntaxErrorException2()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('**3');
    }

    public function testSyntaxErrorException3()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('-');
    }

    public function testSyntaxErrorException4()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('e^');
    }

    public function testParenthesisMismatchException()
    {
        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('1+1)');

        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('(1+1');
    }

    public function testCanParseUnbalancedParentheses()
    {
        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('1(2');
    }

    public function testCanParseUnbalancedParentheses2()
    {
        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('1)2');
    }

    public function testCanEvaluateNode()
    {
        $f = $this->parser->parse('x+y');
        $this->assertEquals($f->evaluate(['x' => 1, 'y' => 2]), 3);
    }

    public function testParserWithoutImplicitMultiplication()
    {
        $lexer = new StdMathLexer();
        $tokens = $lexer->tokenize('2x');
        $parser = new ParserWithoutImplicitMultiplication();
        $this->expectException(SyntaxErrorException::class);
        $node = $parser->parse($tokens);
    }

    public function testNonSimplifyingParser()
    {
        $this->parser->setSimplifying(false);

        $node = $this->parser->parse("3+5");
        $shouldBe = new ExpressionNode(new NumberNode(3), '+', new NumberNode(5));
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("3-5");
        $shouldBe = new ExpressionNode(new NumberNode(3), '-', new NumberNode(5));
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-3");
        $shouldBe = new ExpressionNode(new NumberNode(3), '-', null);
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function canParseFactorial()
    {
        $node = $this->parser->parse("3!4!");
        $shouldBe = new ExpressionNode(
            new FunctionNode('!', new NumberNode(3)),
            '*',
            new FunctionNode('!', new NumberNode(4)));
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-3!");
        $shouldBe = new ExpressionNode(
            new FunctionNode('!', new NumberNode(3)),
            '-',
            null);
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function canParseInvalidFactorial()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('!1');
    }

    public function canParseInvalidFactorial2()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('1+!1');
    }

}
