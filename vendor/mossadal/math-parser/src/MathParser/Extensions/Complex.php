<?php
/*
 * @package     Complex
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */
namespace MathParser\Extensions;

use MathParser\Extensions\Math as Math;
use MathParser\Extensions\Rational as Rational;

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Exceptions\DivisionByZeroException;

/**
* Implementation of complex number arithmetic with the
* standard transcendental functions.
*
* ## Example:
* ~~~{.php}
* $z = new Complex(3, 4);           // creates the complex number 3+4i
* $w = new Complex(-1, 1);          // creates the complex number -1+i
* $product = Complex::mul($z, $w)   // computes the product (3+4i)(-1+i)
* ~~~
*
*
*/
class Complex
{
    /**
     * float $x real part
     */
    private $x;
    /**
     * float $y real part
     */
    private $y;

    /**
     * Constructor
     *
     * Construct a complex number with given real and imaginary part
     *
     * @param float $real real part
     * @param float $imag imaginary part
     */
    public function __construct($real, $imag)
    {
        $this->x = $real;
        $this->y = $imag;
    }

    /**
     * Real part
     *
     * @retval float real part
     */
    public function r()
    {
        return $this->x;
    }

    /**
     * Imaginary part
     *
     * @retval float imaginary part
     */
    public function i()
    {
        return $this->y;
    }

    /**
     * Modulus (absolute value)
     *
     * Return the modulus of the complex number z=x+iy, i.e.
     * sqrt(x^2 + y^2)
     *
     * @retval float modulus
     */
    public function abs()
    {
        return hypot($this->x, $this->y);
    }

    /**
     * Argument (principal value)
     *
     * Returns the prinicpal argument of the complex number,
     * i.e. a number t with -pi < t <= pi, such that z = rexp(i*t) for
     * some positive real r
     * @retval float argument
     */
    public function arg()
    {
        return atan2($this->y, $this->x);
    }


    /**
     * test if the complex number is NAN
     *
     * @retval bool
     */
    public function is_nan()
    {
        return is_nan($this->x) || is_nan($this->y);
    }

    /**
     * check whether a string represents a signed real number
     *
     * @param string $value
     * @retval bool true if $value is a signed real number
     */
    private static function isSignedReal($value)
    {
        return preg_match('~^\-?\d+([,|.]\d+)?$~', $value);
    }

    /**
     * convert string to floating point number, if possible
     *
     * decimal commas accepted
     *
     * @param string $value
     * @throws SyntaxErrorException if the string cannot be parsed
     * @retval float
     */
    private static function parseReal($value)
    {
        if ($value == '') return null;

        $x = str_replace(',', '.', $value);
        if (static::isSignedReal($x)) return floatval($x);
        else throw new SyntaxErrorException();
    }

    /**
     * convert data to a complex number, if possible
     *
     *
     * @param mixed $value (Complex, Rational, int, float and strings accepted)
     * @throws SyntaxErrorException if the string cannot be parsed
     * @retval Complex
     */
    public static function parse($value)
    {
        if ($value instanceof Complex) return $value;
        if ($value instanceof Rational) return new Complex($value.p/$value.q, 0);

        if (is_int($value)) return new Complex($value, 0);
        if (is_float($value)) return new Complex($value, 0);

        if (!is_string($value)) throw new SyntaxErrorException();

        // Match complex numbers with an explicit i
        $matches = array();

        $valid = \preg_match(
            '#^([-,\+])?([0-9/,\.]*?)([-,\+]?)([0-9/,\.]*?)i$#',
            \trim($value),
            $matches
        );

        if ($valid == 1) {
            $real = $matches[2];
            if ($real === '') {
                $matches[3] = $matches[1];
                $real = '0';
            }
            $imaginary = $matches[4];
            if ($imaginary === '') $imaginary = '1';

            if ($matches[1] && $matches[1] == '-') {
                $real = '-' . $real;
            }
            if ($matches[3] && $matches[3] == '-') {
                $imaginary = '-' . $imaginary;
            }

            try {
                $a = Rational::parse($real);
                $realPart = $a->p/$a->q;
            } catch(SyntaxErrorException $e)
            {
                $realPart = static::parseReal($real);
            }

            try {
                $b = Rational::parse($imaginary);
                $imaginaryPart = $b->p/$b->q;
            } catch(SyntaxErrorException $e)
            {
                $imaginaryPart = static::parseReal($imaginary);
            }

        }
        else {
            // That failed, try matching a rational number
            try {

                $a = Rational::parse($value);
                $realPart = $a->p/$a->q;
                $imaginaryPart = 0;

            } catch(SyntaxErrorException $e) {
                // Final attempt, try matching a real number
                $realPart = static::parseReal($value);
                $imaginaryPart = 0;
            }
        }

        $z = new Complex($realPart, $imaginaryPart);

        return $z;
    }

    /**
     * convert data to a floating point number, if possible
     *
     *
     * @param mixed $value (float, int, Rational)
     * @throws SyntaxErrorException if the string cannot be parsed
     * @retval float
     */
    private static function toFloat($x)
    {
        if (is_float($x)) return $x;
        if (is_int($x)) return $x;
        if (is_string($x)) {
            $r = Rational::parse($x);
            return $r->p/$r->q;
        }
        throw new SyntaxErrorException();
    }

    /**
     * create a complex number from its real and imaginary parts
     *
     *
     * @param mixed $real (float, int, Rational)
     * @param mixed $imag (float, int, Rational)
     * @throws SyntaxErrorException if the string cannot be parsed
     * @retval float
     */
    public static function create($real, $imag)
    {
        $x = static::toFloat($real);
        $y = static::toFloat($imag);

        return new Complex($x, $y);
    }


    /**
     * add two complex numbers
     *
     * Complex::add($z, $w) computes and returns $z+$w
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param mixed $w (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function add($z, $w) {
        if (!($z instanceof Complex)) $z = static::parse($z);
        if (!($w instanceof Complex)) $w = static::parse($w);

        return static::create($z->x + $w->x, $z->y + $w->y);
    }

    /**
     * subtract two complex numbers
     *
     * Complex::sub($z, $w) computes and returns $z-$w
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param mixed $w (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function sub($z, $w) {
        if (!($z instanceof Complex)) $z = static::parse($z);
        if (!($w instanceof Complex)) $w = static::parse($w);

        return static::create($z->x - $w->x, $z->y - $w->y);
    }

    /**
     * multiply two complex numbers
     *
     * Complex::mul($z, $w) computes and returns $z*$w
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param mixed $w (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function mul($z, $w) {
        if (!($z instanceof Complex)) $z = static::parse($z);
        if (!($w instanceof Complex)) $w = static::parse($w);

        return static::create($z->x * $w->x - $z->y * $w->y, $z->x * $w->y + $z->y * $w->x);
    }

    /**
     * divide two complex numbers
     *
     * Complex::div($z, $w) computes and returns $z/$w
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param mixed $w (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function div($z, $w) {
        if (!($z instanceof Complex)) $z = static::parse($z);
        if (!($w instanceof Complex)) $w = static::parse($w);

        $d = $w->x * $w->x + $w->y * $w->y;

        if ($d == 0) throw new DivisionByZeroException();

        return static::create(($z->x * $w->x + $z->y * $w->y)/$d, (-$z->x * $w->y + $z->y * $w->x)/$d);
    }

    /**
     * powers of two complex numbers
     *
     * Complex::pow($z, $w) computes and returns the principal value of $z^$w
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param mixed $w (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function pow($z, $w) {
        // If exponent is an integer, compute the power using a square-and-multiply algorithm
        if (is_int($w)) return static::powi($z, $w);

        // Otherwise compute the principal branch: z^w = exp(wlog z)
        return static::exp(static::mul($w, static::log($z)));
    }

    /**
     * integer power of a complex number
     *
     * Complex::powi($z, $n) computes and returns $z^$n where $n is an integer
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @param int $n
     * @retval Complex
     */
    private static function powi($z, $n) {
        if ($n < 0) return static::div(1,static::powi($z, -$n));

        if ($n == 0) return new Complex(1,0);

        $y = new Complex(1,0);
        while ($n > 1) {
            if ($n % 2 == 0) {
                $n = $n / 2;
            } else {
                $y = static::mul($z, $y);
                $n = ($n-1)/2;
            }
            $z = static::mul($z, $z);
        }

        return static::mul($z, $y);
    }

    // Transcendental functions

    /**
     * complex sine function
     *
     * Complex::sin($z) computes and returns sin($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function sin($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::create(sin($z->x)*cosh($z->y), cos($z->x)*sinh($z->y));
    }

    /**
     * complex cosine function
     *
     * Complex::cos($z) computes and returns cos($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function cos($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::create(cos($z->x)*cosh($z->y), -sin($z->x)*sinh($z->y));
    }

    /**
     * complex tangent function
     *
     * Complex::tan($z) computes and returns tan($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function tan($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $d = cos($z->x)*cos($z->x) + sinh($z->y)*sinh($z->y);
        return static::create(sin($z->x)*cos($z->x)/$d, sinh($z->y)*cosh($z->y)/$d);
    }

    /**
     * complex cotangent function
     *
     * Complex::cot($z) computes and returns cot($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function cot($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $d = sin($z->x)*sin($z->x) + sinh($z->y)*sinh($z->y);
        return static::create(sin($z->x)*cos($z->x)/$d, -sinh($z->y)*cosh($z->y)/$d);
    }

    /**
     * complex inverse sine function
     *
     * Complex::arcsin($z) computes and returns the principal branch of arcsin($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arcsin($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $I = new Complex(0,1);
        $iz = static::mul($z, $I);                                       // iz
        $temp = static::sqrt(static::sub(1, static::mul($z, $z)));       // sqrt(1-z^2)
        return static::div(static::log(static::add($iz, $temp)), $I);
    }

    /**
     * complex inverse cosine function
     *
     * Complex::arccos($z) computes and returns the principal branch of arccos($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arccos($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $I = new Complex(0,1);
        $temp = static::mul(static::sqrt(static::sub(1, static::mul($z, $z))), $I);
        return static::div(static::log(static::add($z, $temp)), $I);
    }

    /**
     * complex inverse tangent function
     *
     * Complex::arctan($z) computes and returns the principal branch of arctan($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arctan($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $I = new Complex(0,1);
        $iz = static::mul($z, $I);
        $w = static::div( static::add(1, $iz), static::sub(1, $iz) );
        $logw = static::log($w);
        return static::div($logw, new Complex(0,2));
    }

    /**
     * complex inverse cotangent function
     *
     * Complex::arccot($z) computes and returns the principal branch of arccot($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arccot($z)
    {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::sub(M_PI/2, static::arctan($z));
    }

    /**
     * complex exponential function
     *
     * Complex::exp($z) computes and returns exp($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */    public static function exp($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $r = exp($z->x);
        return new Complex($r*cos($z->y), $r*sin($z->y));
    }

    /**
     * complex logarithm function
     *
     * Complex::log($z) computes and returns the principal branch of log($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */    public static function log($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $r = $z->abs();
        $theta = $z->arg();

        if ($r == 0) return new Complex(NAN, NAN);

        return new Complex(log($r), $theta);
    }

    /**
     * complex hyperbolic sine function
     *
     * Complex::sinh($z) computes and returns sinh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
     public static function sinh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::create(sinh($z->x)*cos($z->y), cosh($z->x)*sin($z->y));
    }

    /**
     * complex hyperbolic cosine function
     *
     * Complex::cosh($z) computes and returns cosh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function cosh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::create(cosh($z->x)*cos($z->y), sinh($z->x)*sin($z->y));
    }

    /**
     * complex hyperbolic tangent function
     *
     * Complex::tanh($z) computes and returns tanh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function tanh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $d = sinh($z->x)*sinh($z->x) + cos($z->y)*cos($z->y);
        return static::create(sinh($z->x)*cosh($z->x)/$d, sin($z->y)*cos($z->y)/$d);
    }

    /**
     * complex inverse hyperbolic sine function
     *
     * Complex::arsinh($z) computes and returns the principal branch of arsinh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arsinh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::log(static::add($z, static::sqrt(static::add(1, static::mul($z, $z)))));
    }

    /**
     * complex inverse hyperbolic cosine function
     *
     * Complex::arcosh($z) computes and returns the principal branch of arcosh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function arcosh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::log(static::add($z, static::sqrt(static::add(-1, static::mul($z, $z)))));
    }

    /**
     * complex inverse hyperbolic tangent function
     *
     * Complex::artanh($z) computes and returns the principal branch of artanh($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function artanh($z) {
        if (!($z instanceof Complex)) $z = static::parse($z);

        return static::div(static::log(static::div(static::add(1,$z), static::sub(1,$z))),2);
    }

    /**
     * complex square root function
     *
     * Complex::sqrt($z) computes and returns the principal branch of sqrt($z)
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval Complex
     */
    public static function sqrt($z)
    {
        if (!($z instanceof Complex)) $z = static::parse($z);

        $r = sqrt($z->abs());
        $theta = $z->arg()/2;

        return new Complex($r*cos($theta), $r*sin($theta));
    }

    /**
     * string representation of a complex number
     *
     *
     * @param mixed $z (Complex, or parsable to Complex)
     * @retval string
     */
    public function __toString()
    {
        // TODO: use Number:: helper functions.

        $realAsRational = Rational::fromFloat($this->x);
        if ($realAsRational->q <= 100) {
            $real = "$realAsRational";
        } else $real = sprintf('%f', $this->x);

        $imagAsRational = Rational::fromFloat($this->y);
        if ($imagAsRational->q <= 100) {
            $imag = $imagAsRational->signed();
        } else $imag = sprintf('%+f', $this->y);

        if ($this->y == 0) return $real;
        if ($this->x == 0) {
            if ($this->y == 1) return 'i';
            if ($this->y == -1) return '-i';
            if ($imag[0] == '+') $imag = substr($imag, 1);
            return "${imag}i";
        }

        if ($this->y == 1) $imag = '+';
        if ($this->y == -1) $imag = '-';
        return "$real${imag}i";
    }

    public function signed()
    {
        $str = (string)($this);
        if ($str[0] != '-') return "+$str";

        return $str;
    }
}
