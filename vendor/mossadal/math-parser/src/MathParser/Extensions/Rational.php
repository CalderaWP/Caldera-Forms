<?php
/*
 * @package     Rational
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */
namespace MathParser\Extensions;

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Exceptions\DivisionByZeroException;

/**
* Implementation of rational number arithmetic.
*
* ## Example:
* ~~~{.php}
* $a = new Rational(1, 4);           // creates the rational number 1/4
* $b = new Rational(2, 3);           // creates the rational number -1+i
* sum = Rational::add($a, $b)        // computes the sum 1/4 + 2/3
* ~~~
*
*
*/
class Rational
{
    /**
     * int $p numerator
     */
    public $p;
    /**
     * int $q denominator
     */
    public $q;

    /**
     * Constuctor for Rational number class
     *
     * $r = new Rational(2, 4)         // creates 1/2
     * $r = new Rational(2, 4, false)  // creates 2/4
     *
     * @param int $p numerator
     * @param int $q denominator
     * @param bool $normalize (default true) If true, store in normalized form,
     *             i.e. positive denominator and gcd($p, $q) = 1
     */
    public function __construct($p, $q, $normalize=true) {
        $this->p = $p;
        $this->q = $q;

        if ($q == 0) throw new DivisionByZeroException();

        if ($normalize) $this->normalize();
    }

    /**
     * Normalize, i.e. make sure the denominator is positive and that
     * the numerator and denominator have no common factors
     */
    private function normalize()
    {
        $gcd = Math::gcd($this->p, $this->q);

        if ($gcd == 0) throw new DivisionByZeroException();

        $this->p = $this->p/$gcd;
        $this->q = $this->q/$gcd;

        if ($this->q < 0) {
            $this->p = -$this->p;
            $this->q = -$this->q;
        }
    }

    /**
     * add two rational numbers
     *
     * Rational::add($x, $y) computes and returns $x+$y
     *
     *
     * @param mixed $x (Rational, or parsable to Rational)
     * @param mixed $y (Rational, or parsable to Rational)
     * @retval Rational
     */
    public static function add($x, $y)
    {
        $X = static::parse($x);
        $Y = static::parse($y);

        $resp = $X->p * $Y->q + $X->q * $Y->p;
        $resq = $X->q * $Y->q;

        return new Rational($resp, $resq);
    }

    /**
     * subtract two rational numbers
     *
     * Rational::sub($x, $y) computes and returns $x-$y
     *
     *
     * @param mixed $x (Rational, or parsable to Rational)
     * @param mixed $y (Rational, or parsable to Rational)
     * @retval Rational
     */
    public static function sub($x, $y)
    {
        $X = static::parse($x);
        $Y = static::parse($y);

        $resp = $X->p * $Y->q - $X->q * $Y->p;
        $resq = $X->q * $Y->q;

        return new Rational($resp, $resq);
    }

    /**
     * multiply two rational numbers
     *
     * Rational::mul($x, $y) computes and returns $x*$y
     *
     *
     * @param mixed $x (Rational, or parsable to Rational)
     * @param mixed $y (Rational, or parsable to Rational)
     * @retval Rational
     */
    public static function mul($x, $y)
    {
        $X = static::parse($x);
        $Y = static::parse($y);

        $resp = $X->p * $Y->p;
        $resq = $X->q * $Y->q;

        return new Rational($resp, $resq);
    }

    /**
     * add two rational numbers
     *
     * Rational::div($x, $y) computes and returns $x/$y
     *
     *
     * @param mixed $x (Rational, or parsable to Rational)
     * @param mixed $y (Rational, or parsable to Rational)
     * @retval Rational
     */
    public static function div($x, $y)
    {
        $X = static::parse($x);
        $Y = static::parse($y);

        if ($Y->p == 0) throw new DivisionByZeroException();

        $resp = $X->p * $Y->q;
        $resq = $X->q * $Y->p;

        return new Rational($resp, $resq);
    }

    /**
     * convert rational number to string, adding a '+' if the number is positive
     *
     * @retval string
     */
    public function signed()
    {

        if ($this->q == 1) {
            return sprintf("%+d", $this->p);
        }
        return sprintf("%+d/%d", $this->p, $this->q);

    }

    /**
     * test whether a string represents an positive integer
     *
     * @retval bool
     */
    private static function isInteger($value)
    {
        return preg_match('~^\d+$~', $value);
    }

    /**
     * test whether a string represents a signed integer
     *
     * @retval bool
     */
    private static function isSignedInteger($value)
    {
        return preg_match('~^\-?\d+$~', $value);
    }

    /**
     * test if the rational number is NAN
     *
     * @retval bool
     */
    public function is_nan()
    {
        if ($this->q == 0) return true;
        return is_nan($this->p) || is_nan($this->q);
    }

    /**
     * Convert $value to Rational
     *
     * @param $value mixed
     * @throws SyntaxErrorException
     * @retval Rational
     */
    public static function parse($value, $normalize=true)
    {
        if ($value === '') return null;
        if ($value === 'NAN') return new Rational(NAN, 1);
        if ($value === 'INF') return new Rational(INF, 1);
        if ($value === '-INF') return new Rational(-INF, 1);

        $data = $value;

        $numbers = explode('/', $data);
        if (count($numbers) == 1) {
            $p = static::isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = 1;
        }
        elseif (count($numbers) != 2) {
            $p = NAN;
            $q = NAN;
        }
        else {
            $p = static::isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = static::isInteger($numbers[1]) ? intval($numbers[1]) : NAN;
        }

        if (is_nan($p) || is_nan($q)) throw new SyntaxErrorException();


        return new Rational($p, $q, $normalize);
    }

    /**
     * convert float to Rational
     *
     * Convert float to a continued fraction, with prescribed accuracy
     *
     * @param string|float $float
     * @param float $tolerance
     * @retval Rational
     */
    public static function fromFloat($float, $tolerance=1e-7)
    {
        if (is_string($float) && preg_match('~^\-?\d+([,|.]\d+)?$~', $float)) {
            $float = floatval(str_replace(',','.',$float));
        }

        if ($float == 0.0) {
            return new Rational(0,1);
        }
        $negative = ($float < 0);
        if ($negative) {
            $float = abs($float);
        }
        $num1 = 1;
        $num2 = 0;
        $den1 = 0;
        $den2 = 1;
        $oneOver = 1 / $float;
        do {
            $oneOver = 1 / $oneOver;
            $floor = floor($oneOver);
            $aux = $num1;
            $num1 = $floor * $num1 + $num2;
            $num2 = $aux;
            $aux = $den1;
            $den1 = $floor * $den1 + $den2;
            $den2 = $aux;
            $oneOver = $oneOver - $floor;
        } while (abs($float - $num1 / $den1) > $float * $tolerance);
        if ($negative) {
            $num1 *= -1;
        }

        return new Rational(intval($num1), intval($den1));
    }

    /**
     * Convert Rational to string
     *
     * @retval string
     */
    public function __toString()
    {
        if ($this->q == 1) return "$this->p";
        return "$this->p/$this->q";
    }
}
