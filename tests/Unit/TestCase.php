<?php


namespace calderawp\calderaforms\Tests\Unit;


use Brain\Monkey;

use calderawp\calderaforms\Tests\Util\Traits\SharedFactories;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

/**
 * Class TestCase
 *
 * Default test case for all unit tests
 * @package CalderaLearn\RestSearch\Tests\Unit
 */
abstract class TestCase extends FrameworkTestCase
{
    use SharedFactories;
    /**
     * Prepares the test environment before each test.
     */
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();

        $this->setup_common_wp_stubs();
    }

    /**
     * Cleans up the test environment after each test.
     */
    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    //phpcs:disable
    /**
     * Set up the stubs for the common WordPress escaping and internationalization functions.
     */
    protected function setup_common_wp_stubs()
    {
        // Common internationalization functions.
        Monkey\Functions\stubs(array(
            '__',
            'esc_html__',
            'esc_html_x',
            'esc_attr_x',
        ));
    }
    //phpcs:enable
}
