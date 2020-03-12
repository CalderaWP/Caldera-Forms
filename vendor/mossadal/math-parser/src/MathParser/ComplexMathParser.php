<?php 

namespace MathParser;

/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/



use MathParser\Parsing\Parser;
use MathParser\Lexing\ComplexLexer;

class ComplexMathParser extends AbstractMathParser
{

    public function __construct()
    {
        $this->lexer = new ComplexLexer();
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
