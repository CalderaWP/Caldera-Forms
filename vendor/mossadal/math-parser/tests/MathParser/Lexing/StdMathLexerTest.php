<?php

use MathParser\Exceptions\UnknownTokenException;
use MathParser\Lexing\StdMathLexer;
use MathParser\Lexing\Token;
use MathParser\Lexing\TokenType;
use PHPUnit\Framework\TestCase;

class StdMathLexerTest extends TestCase
{
    private $lexer;

    public function setUp()
    {
        $lexer = new StdMathLexer();
        $this->lexer = $lexer;
    }

    public function testCanTokenizeNumber()
    {
        $tokens = $this->lexer->tokenize('325');
        $this->assertTokenEquals('325', TokenType::PosInt, $tokens[0]);

        $tokens = $this->lexer->tokenize('-5');
        $this->assertCount(2, $tokens);
        $this->assertTokenEquals('-', TokenType::SubtractionOperator, $tokens[0]);
        $this->assertTokenEquals('5', TokenType::PosInt, $tokens[1]);

        $tokens = $this->lexer->tokenize('2.3');
        $this->assertTokenEquals('2.3', TokenType::RealNumber, $tokens[0]);

        $tokens = $this->lexer->tokenize('2.3e+3');
        $this->assertTokenEquals('2.3e+3', TokenType::RealNumber, $tokens[0]);

        $tokens = $this->lexer->tokenize('2.3e4');
        $this->assertTokenEquals('2.3e4', TokenType::RealNumber, $tokens[0]);

        $tokens = $this->lexer->tokenize('2.3e-2');
        $this->assertTokenEquals('2.3e-2', TokenType::RealNumber, $tokens[0]);

        $tokens = $this->lexer->tokenize('-2.3');
        $this->assertCount(2, $tokens);
        $this->assertTokenEquals('-', TokenType::SubtractionOperator, $tokens[0]);
        $this->assertTokenEquals('2.3', TokenType::RealNumber, $tokens[1]);

        $tokens = $this->lexer->tokenize('-2.3e1');
        $this->assertCount(2, $tokens);
        $this->assertTokenEquals('-', TokenType::SubtractionOperator, $tokens[0]);
        $this->assertTokenEquals('2.3e1', TokenType::RealNumber, $tokens[1]);

    }

    public function testCanTokenizeOperator()
    {
        $tokens = $this->lexer->tokenize('+');

        $t = $tokens[0];
        $this->assertTokenEquals('+', TokenType::AdditionOperator, $t);
    }

    public function testCanTokenizeNumbersAndOperators()
    {
        $tokens = $this->lexer->tokenize('3+5');

        $this->assertCount(3, $tokens);

        $this->assertTokenEquals('3', TokenType::PosInt, $tokens[0]);
        $this->assertTokenEquals('+', TokenType::AdditionOperator, $tokens[1]);
        $this->assertTokenEquals('5', TokenType::PosInt, $tokens[2]);
    }

    public function testExceptionIsThrownOnUnknownToken()
    {
        $this->expectException(UnknownTokenException::class);

        $this->lexer->tokenize('@');
    }

    private function assertTokenEquals($value, $type, Token $token)
    {
        $this->assertEquals($value, $token->getValue());
        $this->assertEquals($type, $token->getType());
    }

    public function testIdentifierTokens()
    {
        $tokens = $this->lexer->tokenize('xy');

        $this->assertEquals(count($tokens), 2);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals('y', TokenType::Identifier, $tokens[1]);

        $tokens = $this->lexer->tokenize('xsinx');

        $this->assertEquals(count($tokens), 3);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals('sin', TokenType::FunctionName, $tokens[1]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[2]);

        $tokens = $this->lexer->tokenize('xsix');

        $this->assertEquals(count($tokens), 4);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals('s', TokenType::Identifier, $tokens[1]);
        $this->assertTokenEquals('i', TokenType::Identifier, $tokens[2]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[3]);

        $tokens = $this->lexer->tokenize('asin');

        $this->assertEquals(count($tokens), 1);
        $this->assertTokenEquals('arcsin', TokenType::FunctionName, $tokens[0]);

        $tokens = $this->lexer->tokenize('a sin');

        $this->assertEquals(count($tokens), 3);
        $this->assertTokenEquals('a', TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals(' ', TokenType::Whitespace, $tokens[1]);
        $this->assertTokenEquals('sin', TokenType::FunctionName, $tokens[2]);

    }

    public function testParenthesisTokens()
    {
        $tokens = $this->lexer->tokenize('(()');

        $this->assertEquals(count($tokens), 3);
        $this->assertTokenEquals('(', TokenType::OpenParenthesis, $tokens[0]);
        $this->assertTokenEquals('(', TokenType::OpenParenthesis, $tokens[1]);
        $this->assertTokenEquals(')', TokenType::CloseParenthesis, $tokens[2]);

        $tokens = $this->lexer->tokenize('(x+1)');
        $this->assertEquals(count($tokens), 5);
        $this->assertTokenEquals('(', TokenType::OpenParenthesis, $tokens[0]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[1]);
        $this->assertTokenEquals('+', TokenType::AdditionOperator, $tokens[2]);
        $this->assertTokenEquals('1', TokenType::PosInt, $tokens[3]);
        $this->assertTokenEquals(')', TokenType::CloseParenthesis, $tokens[4]);

    }

    public function testWhitepsace()
    {
        $tokens = $this->lexer->tokenize("  x\t+\n ");

        $this->assertEquals(count($tokens), 6);
        $this->assertTokenEquals('  ', TokenType::Whitespace, $tokens[0]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[1]);
        $this->assertTokenEquals("\t", TokenType::Whitespace, $tokens[2]);
        $this->assertTokenEquals("+", TokenType::AdditionOperator, $tokens[3]);
        $this->assertTokenEquals("\n", TokenType::Terminator, $tokens[4]);
        $this->assertTokenEquals(' ', TokenType::Whitespace, $tokens[5]);

    }

    public function testArcsin()
    {
        $tokens = $this->lexer->tokenize("asin");
        $this->assertTokenEquals('arcsin', TokenType::FunctionName, $tokens[0]);

        $tokens = $this->lexer->tokenize("arcsin");
        $this->assertTokenEquals('arcsin', TokenType::FunctionName, $tokens[0]);

        $tokens = $this->lexer->tokenize("asin(x)");
        $this->assertTokenEquals('arcsin', TokenType::FunctionName, $tokens[0]);
        $this->assertTokenEquals('(', TokenType::OpenParenthesis, $tokens[1]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[2]);
        $this->assertTokenEquals(')', TokenType::CloseParenthesis, $tokens[3]);

        $tokens = $this->lexer->tokenize("arcsin(x)");
        $this->assertTokenEquals('arcsin', TokenType::FunctionName, $tokens[0]);
        $this->assertTokenEquals('(', TokenType::OpenParenthesis, $tokens[1]);
        $this->assertTokenEquals('x', TokenType::Identifier, $tokens[2]);
        $this->assertTokenEquals(')', TokenType::CloseParenthesis, $tokens[3]);
    }

    public function testArccos()
    {
        $tokens = $this->lexer->tokenize("acos");
        $this->assertTokenEquals('arccos', TokenType::FunctionName, $tokens[0]);

        $tokens = $this->lexer->tokenize("arccos");
        $this->assertTokenEquals('arccos', TokenType::FunctionName, $tokens[0]);
    }

    public function testArctan()
    {
        $tokens = $this->lexer->tokenize("atan");
        $this->assertTokenEquals('arctan', TokenType::FunctionName, $tokens[0]);

        $tokens = $this->lexer->tokenize("arctan");
        $this->assertTokenEquals('arctan', TokenType::FunctionName, $tokens[0]);
    }

}
