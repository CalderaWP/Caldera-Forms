<?php
/*
 * @package     Lexical analysis
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

/**
 * @namespace MathParser::Lexing
 * Lexer and Token related classes.
 *
 * [Lexical analysis](https://en.wikipedia.org/wiki/Lexical_analysis)
 * or *lexing* is the process of converting an input string into a sequence
 * of tokens, representing discrete parts of the input each carrying certain meaning.
 *
 */
namespace MathParser\Lexing;

use MathParser\Exceptions\UnknownTokenException;

/**
 * Generic very simple lexer, capable of matching tokens defined by regular expressions.
 *
 * The Lexer works on an input string, sequentially building a list of matched
 * Tokens (or throwing an Exception if the input string cannot be tokenized).
 *
 * The Lexer is context independent and without lookahead, and cannot for
 * example distinguish between `-` used as a binary subtraction operation
 * or as a unary negation. This is  handled by the parser.
 *
 * Tokens are added to the Lexer as TokenDefinition instances, and are saved in
 * an ordered list. Hence some care has to be taken when defining the Lexer (see
 * the implementation of StdMathLexer). For example, if we want the lexer to
 * recognize `sin` as well as `sinh` as separate tokens, the more specific `sinh`
 * pattern should be added to the Lexer *before* `sin`.
 */
class Lexer
{
    /**
     * TokenDefinition[] $tokenDefinition list of tokens recognized by the Lexer.
     */
    private $tokenDefinitions = [];

    /**
     * Add a Token to the list of tokens recognized by the Lexer.
     *
     * Adds the supplied TokenDefinition at the end of the list of known
     * tokens.
     *
     * @retval void
     * @param TokenDefinition $tokenDefinition token to add to the list of known tokens.
     */
    public function add(TokenDefinition $tokenDefinition)
    {
        $this->tokenDefinitions[] = $tokenDefinition;
    }

    /**
     * Convert an input string to a list of tokens.
     *
     * Using the list of knowns tokens, sequentially match the input string to
     * known tokens. Note that the first matching token from the list is chosen,
     * so if there are tokens sharing parts of the pattern (e.g. `sin` and `sinh`),
     * care should be taken to add `sinh` before `sin`, otherwise the lexer will
     * never match a `sinh`.
     *
     * @retval Token[] sequence of recognized tokens
     *      that doesn't match any knwon token.
     * @param  string                $input  String to tokenize.
     * @throws UnknownTokenException throwns when encountering characters in the input string
     */
    public function tokenize($input)
    {
        // The list of tokens we'll eventually return
        $tokens = [];

        // The currentIndex indicates where we are inside the input string
        $currentIndex = 0;

        while ($currentIndex < strlen($input)) {
            // We try to match only what is after the currentIndex,
            // as the content before is already converted to tokens
            $token = $this->findMatchingToken(substr($input, $currentIndex));

            // If no tokens were matched, it means that the string has invalid tokens
            // for which we did not define a token definition
            if (!$token) {
                throw new UnknownTokenException(substr($input, $currentIndex));
            }

            // Add the matched token to our list of token
            $tokens[] = $token;

            // Increment the string index by the lenght of the matched token,
            // so we can now process the rest of the string.
            $currentIndex += $token->length();
        }

        return $tokens;
    }

    /**
     * Find a matching token at the begining of the provided input.
     *
     * @retval Token|null Matched token
     * @param string $input
     */
    private function findMatchingToken($input)
    {
        // Check with all tokenDefinitions
        foreach ($this->tokenDefinitions as $tokenDefinition) {
            $token = $tokenDefinition->match($input);

            // Return the first token that was matched.
            if ($token) {
                return $token;
            }
        }

        // Return null if no tokens were matched.

        return null;
    }
}
