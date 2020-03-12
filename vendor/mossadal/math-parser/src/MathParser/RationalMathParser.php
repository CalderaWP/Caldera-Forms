<?php 

namespace MathParser;

use MathParser\Lexing\StdMathLexer;
use MathParser\Parsing\Parser;

/**
 * Convenience class for using the MathParser library.
 *
 * StdMathParser is a wrapper for the StdMathLexer and Parser
 * classes, and if you do not require any tweaking, this is the
 * most straightforward way to use the MathParser library.
 *
 * ### Example usage:
 *
 * ~~~{.php}
 * use MathParser\RationalMathParser;
 * use MathParser\Interpreting\Evaluator;
 * use MathParser\Interpreting\Differentiator;
 *
 * $parser = new RationalMathParser();
 * $AST = $parser->parse('2x + 2y^2/sin(x)');
 *
 * // Do whatever you want with the parsed expression,
 * // for example evaluate it.
 * $evaluator = new Evaluator([ 'x' => 1, 'y' => 2 ]);
 * $value = $AST->accept($evaluator);
 *
 * // or differentiate it:
 * $d_dx = new Differentiator('x');
 * $derivative = $AST->accept($d_dx);
 * $valueOfDerivative = $derivative->accept($evaluator);
 * ~~~
 *
 */
class RationalMathParser extends AbstractMathParser
{

    public function __construct()
    {
        $this->lexer = new StdMathLexer();
        $this->parser = new Parser();
        $this->parser->setRationalFactory(true);
    }
    
    /**
     * Parse the given mathematical expression into an abstract syntax tree.
     *
     * @param string $text Input
     * @retval Node
     */
    public function parse($text)
    {
        $this->tokens = $this->lexer->tokenize($text);
        $this->tree = $this->parser->parse($this->tokens);

        return $this->tree;
    }
    
}
