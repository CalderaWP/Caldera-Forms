
<?php

use MathParser\Lexing\Lexer;
use MathParser\Lexing\StdMathLexer;
use MathParser\Lexing\TokenDefinition;
use MathParser\Lexing\TokenType;
use MathParser\Parsing\Parser;
use MathParser\Interpreting\TreePrinter;
use MathParser\Interpreting\LaTeXPrinter;
use MathParser\Interpreting\ASCIIPrinter;
use MathParser\Interpreting\Differentiator;
use MathParser\Interpreting\Evaluator;
use MathParser\Interpreting\RationalEvaluator;

use MathParser\StdMathParser;
use MathParser\RationalMathParser;

include '../vendor/autoload.php';


class ParserWithoutImplicitMultiplication extends Parser {
    protected static function allowImplicitMultiplication() {
        return false;
    }
}

// $lexer = new StdMathLexer();
// $tokens = $lexer->tokenize($argv[1]);
//
// $parser = new ParserWithoutImplicitMultiplication();
// $tree = $parser->parse($tokens);
//
// $treeprinter = new TreePrinter();
// var_dump($tree->accept($treeprinter));
//
// die();


$parser = new RationalMathParser();
$parser->setSimplifying(false);

$parser->parse($argv[1]);

$tokens = $parser->getTokenList();
print_r($tokens);

$tree = $parser->getTree();

$treeprinter = new TreePrinter();
echo "TreePrinter: " . $tree->accept($treeprinter) . "\n";

echo "LaTeXPrinter giving ";
$printer = new LaTeXPrinter();
echo $tree->accept($printer) . "\n";

echo "String conversion: $tree\n";

$ascii = new ASCIIPrinter();
echo $tree->accept($ascii) . "\n";


try {
    echo "Derivative: ";
    $differentiator = new Differentiator('x');
    $derivative = $tree->accept($differentiator);


    var_dump($derivative->accept($treeprinter));
    var_dump($derivative->accept($printer));
} catch(\Exception $e) {
    var_dump($e->getMessage());
}

try {
    echo "Evaluator: ";
    $evaluator = new Evaluator(['x' => 1, 'y' => 1.5 ]);
    var_dump($tree->accept($evaluator));
} catch(\Exception $e) {
    var_dump($e->getMessage());
}

try {
    echo "RationalEvaluator: ";
    $evaluator = new RationalEvaluator(['x' => '1/3', 'y' => -2]);
    var_dump($tree->accept($evaluator));
} catch(\Exception $e) {
    var_dump($e->getMessage());
}
