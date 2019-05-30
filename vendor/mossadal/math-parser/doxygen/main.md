math-parser
===========


DESCRIPTION
-----------

PHP parser and evaluator library for mathematical expressions.

Intended use: safe and reasonably efficient evaluation of user submitted formulas. The library supports basic arithmetic and elementary functions, as well as variables and extra functions.

The lexer and parser produces an abstract syntax tree (AST) that can be traversed using a tree interepreter. The math-parser library ships with three interpreters:

* an evaluator computing the value of the given expression.
* a differentiator transforming the AST into a (somewhat) simplied AST representing the derivative of the supplied expression.
* a rudimentary LaTeX output generator, useful for pretty printing expressions using MathJax


EXAMPLES
--------

It is possible to fine-tune the lexer and parser, but the library ships with a StdMathParser class, capable of tokenizing and parsing standard mathematical expressions, including aritmethical operations as well as elementary functions.

~~~{.php}
use MathParser\StdMathParser;
use MathParser\Interpreting\Evaluator;

$parser = new StdMathParser();

// Generate an abstract syntax tree
$AST = $parser->parse('1+2');

// Do something with the AST, e.g. evaluate the expression:
$evaluator = new Evaluator();

$value = $AST->accept($evaluator);
echo $value;
~~~

More interesting example, containing variables:

~~~{.php}
$AST = $parser->parse('x+sqrt(y)');

$evaluator->setVariables([ 'x' => 2, 'y' => 3 ]);
$value = $AST->accept($evaluator);
~~~

We can do other things with the AST. The library ships with a differentiator, computing the (symbolic) derivative with respect to a given variable.

~~~{.php}
use MathParser\Interpreting\Differentiator;

$differentiator = new Differentiator('x');
$f = $parser->parse('exp(2*x)-x*y');
$df = $f->accept($differentiator);

// $df now contains the AST of '2*exp(x)-y' and can be evaluated further
$evaluator->setVariables([ 'x' => 1, 'y' => 2 ]);
$df->accept($evaluator);
~~~

### Implicit multiplication

Another helpful feature is that the parser understands implicit multiplication. An expression as `2x` is parsed the same as `2*x` and `xsin(x)cos(x)^2` is parsed as `x*sin(x)*cos(x)^2`.

Note that implicit multiplication has the same precedence as explicit multiplication. In particular `xy^2z` is parsed as `x*y^2*z` and **not** as `x*y^(2*z)`.

To make full use of implicit multiplication, the standard lexer only allows one-letter variables. (Otherwise, we wouldn't know if `xy` should be parsed as `x*y` or as the single variable `xy`).

THANKS
------

The Lexer is based on the lexer described by Marc-Oliver Fiset in his [blog](http://marcofiset.com/programming-language-implementation-part-1-lexer/).

The parser is a version of the "Shunting yard" algorithm, described for example by [Theodore Norvell](http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm#shunting_yard).
