<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/26/14
 * Time: 2:19 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryFormatter\Tokenizer;

use NilPortugues\Sql\QueryFormatter\Tokenizer\Tokenizer;

/**
 * Class TokenizerTest.
 */
class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     *
     */
    protected function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->tokenizer = null;
    }

    /**
     * @test
     */
    public function itShouldTokenizeWhiteSpace()
    {
        $sql = <<<SQL
SELECT * FROM users;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $whiteSpacesFound = false;
        foreach ($result as $token) {
            if (' ' === $token[Tokenizer::TOKEN_VALUE]) {
                $whiteSpacesFound = true;
                break;
            }
        }

        $this->assertTrue($whiteSpacesFound);
    }

    /**
     * Comment starting with # will be treated as tokens representing the whole comment.
     *
     * @test
     */
    public function itShouldTokenizeCommentStartingWithSymbolNumber()
    {
        $sql = <<<SQL
SELECT * FROM users # A comment
WHERE user_id = 2;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $commentFound = false;
        foreach ($result as $token) {
            if ('# A comment' === $token[Tokenizer::TOKEN_VALUE]) {
                $commentFound = true;
                break;
            }
        }

        $this->assertTrue($commentFound);
    }

    /**
     * Comment starting with -- will be treated as tokens representing the whole comment.
     *
     * @test
     */
    public function itShouldTokenizeCommentStartingWithDash()
    {
        $sql = <<<SQL
SELECT * FROM
-- This is another comment
users
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $commentFound = false;
        foreach ($result as $token) {
            if ('-- This is another comment' === $token[Tokenizer::TOKEN_VALUE]) {
                $commentFound = true;
                break;
            }
        }

        $this->assertTrue($commentFound);
    }

    /**
     * Comment blocks in SQL Server fashion will be treated as tokens representing the whole comment.
     *
     * @test
     */
    public function itShouldTokenizeCommentWithBlockComment()
    {
        $sql = <<<SQL
SELECT * FROM /* This is a block comment */ WHERE 1 = 2;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $commentFound = false;
        foreach ($result as $token) {
            if ('/* This is a block comment */' === $token[Tokenizer::TOKEN_VALUE]) {
                $commentFound = true;
                break;
            }
        }

        $this->assertTrue($commentFound);
    }

    /**
     * Unterminated comment block will be processed incorrectly by the tokenizer,
     * yet we should be able to detect this error in order to handle the resulting situation.
     *
     * @test
     */
    public function itShouldTokenizeCommentWithUnterminatedBlockComment()
    {
        $sql = <<<SQL
SELECT * FROM /* This is a block comment WHERE 1 = 2;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $commentStartFound = false;
        foreach ($result as $token) {
            if ('/*' === $token[Tokenizer::TOKEN_VALUE]) {
                $commentStartFound = true;
                break;
            }
        }
        $this->assertTrue($commentStartFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeQuoted()
    {
        $sql = <<<SQL
UPDATE `PREFIX_cms_category` SET `position` = 0
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $quotedFound = false;
        foreach ($result as $token) {
            if ('`position`' === $token[Tokenizer::TOKEN_VALUE]) {
                $quotedFound = true;
                break;
            }
        }

        $this->assertTrue($quotedFound);
    }

    /**
     * SQL Server syntax for defining custom variables.
     *
     * @test
     */
    public function itShouldTokenizeUserDefinedVariableWithQuotations()
    {
        $sql = <<<SQL
SELECT au_lname, au_fname, phone FROM authors WHERE au_lname LIKE @find;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $quotedFound = false;
        foreach ($result as $token) {
            if ('@find' === $token[Tokenizer::TOKEN_VALUE]) {
                $quotedFound = true;
                break;
            }
        }

        $this->assertTrue($quotedFound);
    }

    /**
     * This validates Microsoft SQL Server syntax for user variables.
     *
     * @test
     */
    public function itShouldTokenizeUserDefinedVariableWithAtSymbol()
    {
        $sql = <<<SQL
SELECT @"weird variable name";
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $this->assertNotEmpty($result);
    }

    /**
     * This test is an edge case.
     *
     * Given the provided statement, will loop forever if no condition is checking total amount
     * of the input string processed. This will happen because the tokenizer has no matching case
     * and string processing will not progress.
     *
     * @test
     */
    public function itShouldTokenizeUserDefinedVariableNoProgressTokenizer()
    {
        $sql = <<<SQL
            SELECT @ and b; /* Edge case */
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $userVariableNotFound = true;
        foreach ($result as $token) {
            if ('@' === $token[Tokenizer::TOKEN_VALUE]) {
                $userVariableNotFound = false;
                break;
            }
        }

        $this->assertTrue($userVariableNotFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeNumeralInteger()
    {
        $sql = <<<SQL
SELECT user_id FROM user WHERE user_id = 1;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $numeralIntegerFound = false;
        foreach ($result as $token) {
            if ('1' == $token[Tokenizer::TOKEN_VALUE]) {
                $numeralIntegerFound = true;
                break;
            }
        }

        $this->assertTrue($numeralIntegerFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeNumeralNegativeIntegerAsPositiveInteger()
    {
        $sql = <<<SQL
SELECT user_id FROM user WHERE user_id = -1;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $numeralIntegerFound = false;
        foreach ($result as $token) {
            if ('1' == $token[Tokenizer::TOKEN_VALUE]) {
                $numeralIntegerFound = true;
                break;
            }
        }
        $this->assertTrue($numeralIntegerFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeNumeralFloat()
    {
        $sql = <<<SQL
SELECT user_id FROM user WHERE user_id = 3.14;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $numeralIntegerFound = false;
        foreach ($result as $token) {
            if ('3.14' == $token[Tokenizer::TOKEN_VALUE]) {
                $numeralIntegerFound = true;
                break;
            }
        }
        $this->assertTrue($numeralIntegerFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeNumeralNegativeFloatAsPositiveFloat()
    {
        $sql = <<<SQL
SELECT user_id FROM user WHERE user_id = -3.14;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $numeralIntegerFound = false;
        foreach ($result as $token) {
            if ('3.14' == $token[Tokenizer::TOKEN_VALUE]) {
                $numeralIntegerFound = true;
                break;
            }
        }
        $this->assertTrue($numeralIntegerFound);
    }

    /**
     * Check if boundary characters are in array. Boundary characters are: ;:)(.=<>+-\/!^%|&#.
     *
     * @test
     */
    public function itShouldTokenizeBoundaryCharacter()
    {
        $sql = 'SELECT id_user, name FROM users';

        $result = $this->tokenizer->tokenize($sql);

        $boundaryFound = false;
        foreach ($result as $token) {
            if (',' === $token[Tokenizer::TOKEN_VALUE]) {
                $boundaryFound = true;
                break;
            }
        }
        $this->assertTrue($boundaryFound);
    }

    /**
     * Tokenize columns should not be a problem, even if using a reserved word as a column name.
     * Example: users.user_id
     * Example of edge cases: users.desc, user.from.
     *
     * @test
     */
    public function itShouldTokenizeReservedWordPrecededByDotCharacter()
    {
        $sql = <<<SQL
SELECT users.desc as userId FROM users;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $reservedTokenFound = false;
        foreach ($result as $token) {
            if ('desc' == $token[Tokenizer::TOKEN_VALUE]) {
                $reservedTokenFound = true;
                break;
            }
        }

        $this->assertTrue($reservedTokenFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeReservedTopLevel()
    {
        $sql = 'SELECT id_user, name FROM users';
        $result = $this->tokenizer->tokenize($sql);

        $reservedTopLevelTokenFound = false;

        foreach ($result as $token) {
            if ('FROM' === $token[Tokenizer::TOKEN_VALUE]) {
                $reservedTopLevelTokenFound = true;
                break;
            }
        }
        $this->assertTrue($reservedTopLevelTokenFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeReservedNewLine()
    {
        $sql = <<<SQL
UPDATE users SET registration_date = "0000-00-00" WHERE id_user = 1 OR id_user = 2;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $reservedNewLineTokenFound = false;
        foreach ($result as $token) {
            if ('OR' === $token[Tokenizer::TOKEN_VALUE]) {
                $reservedNewLineTokenFound = true;
                break;
            }
        }
        $this->assertTrue($reservedNewLineTokenFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeReserved()
    {
        $sql = <<<SQL
SELECT customer_id, customer_name, COUNT(order_id) as total
FROM customers INNER JOIN orders ON customers.customer_id = orders.customer_id
GROUP BY customer_id, customer_name
HAVING COUNT(order_id) > 5
ORDER BY COUNT(order_id) DESC;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $reservedTokenFound = false;
        foreach ($result as $token) {
            if ('INNER JOIN' === $token[Tokenizer::TOKEN_VALUE]) {
                $reservedTokenFound = true;
                break;
            }
        }
        $this->assertTrue($reservedTokenFound);
    }

    /**
     * @test
     */
    public function itShouldTokenizeFunction()
    {
        $sql = <<<SQL
SELECT customer_id, customer_name, COUNT(order_id) as total FROM customers GROUP BY customer_id, customer_name
HAVING COUNT(order_id) > 5 ORDER BY COUNT(order_id) DESC;
SQL;
        $result = $this->tokenizer->tokenize($sql);

        $functionFound = false;
        foreach ($result as $token) {
            if ('COUNT' === $token[Tokenizer::TOKEN_VALUE]) {
                $functionFound = true;
                break;
            }
        }
        $this->assertTrue($functionFound);
    }
}
