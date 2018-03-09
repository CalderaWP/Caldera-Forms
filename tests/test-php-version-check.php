<?php
/**
 * Class Test_PHP_Version_Check
 *
 * @covers Caldera_Forms_Admin_PHP
 */
class Test_PHP_Version_Check extends Caldera_Forms_Test_Case{

    /**
     * Check that when we check if a version meets minimum requirement and it is the SAME as the minimum, TRUE is returned.
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::greater_than()
     *
     * @group php_check
     */
    public function test_greater_than_with_equal_version(){
        $this->assertTrue( Caldera_Forms_Admin_PHP::greater_than('7.1.1', '7.1.1' ) );
    }

    /**
     * Check that when we check if a version meets minimum requirement and it is the GREATER as the minimum, TRUE is returned.
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::greater_than()
     *
     * @group php_check
     */
    public function test_greater_than_true(){
        $this->assertTrue( Caldera_Forms_Admin_PHP::greater_than('5.2', '5.3' ) );
        $this->assertTrue( Caldera_Forms_Admin_PHP::greater_than('5.2', '7.1' ) );
    }

    /**
     * Check that when we check if a version meets minimum requirement and it is the LESS as the minimum, FALSE is returned.
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::greater_than()
     *
     * @group php_check
     */
    public function test_greater_than_false(){
        $this->assertFalse( Caldera_Forms_Admin_PHP::greater_than('7.1', '5.3' ) );
        $this->assertFalse( Caldera_Forms_Admin_PHP::greater_than('7.1', '7.0' ) );
    }

    /**
     * Check that when we use null for version, the version is PHP_VERSION
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::greater_than()
     *
     * @group php_check
     */
    public function test_php_version_null(){
        $this->assertTrue( Caldera_Forms_Admin_PHP::greater_than(PHP_VERSION ) );

    }

    /**
     * Test that the minimum supported version is supported
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::$min_supported_version
     * @covers Caldera_Forms_Admin_PHP::get_minimum_supported_version()
     * @covers Caldera_Forms_Admin_PHP::is_version_supported()
     *
     * @group php_check
     */
    public function test_supported(){
        //@todo increase this to match changes to Caldera_Forms_Admin_PHP::$min_supported_version
        $this->assertEquals( '5.2.4', Caldera_Forms_Admin_PHP::get_minimum_supported_version() );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_supported( Caldera_Forms_Admin_PHP::get_minimum_supported_version() ) );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_supported( '5.2.17' ) );
    }

    /**
     * Test that the minimum supported version is supported
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::is_version_supported()
     *
     * @group php_check
     */
    public function test_not_supported(){
        //@todo increase this to match changes to Caldera_Forms_Admin_PHP::$min_supported_version
        $this->assertFalse( Caldera_Forms_Admin_PHP::is_version_supported( '5.1' ) );
        $this->assertFalse( Caldera_Forms_Admin_PHP::is_version_supported( '5.2.3' ) );
    }

    /**
     * Test that the minimum tested version when it is valid
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::$min_tested_version
     * @covers Caldera_Forms_Admin_PHP::get_minimum_tested_version()
     * @covers Caldera_Forms_Admin_PHP::is_version_tested()
     *
     * @group php_check
     */
    public function test_tested(){
        //@todo increase this to match changes to Caldera_Forms_Admin_PHP::$min_supported_version
        $this->assertEquals( '5.4', Caldera_Forms_Admin_PHP::get_minimum_tested_version() );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_tested( Caldera_Forms_Admin_PHP::get_minimum_tested_version() ) );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_tested( '5.4' ) );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_tested( '5.5.6' ) );
        $this->assertTrue( Caldera_Forms_Admin_PHP::is_version_tested( '7.0' ) );
    }

    /**
     * Test that the minimum tested version when it is invalid
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms_Admin_PHP::is_version_supported()
     *
     * @group php_check
     */
    public function test_not_tested(){
        //@todo increase this to match changes to Caldera_Forms_Admin_PHP::$min_supported_version
        $this->assertFalse( Caldera_Forms_Admin_PHP::is_version_tested( '5.3.1' ) );
        $this->assertFalse( Caldera_Forms_Admin_PHP::is_version_tested( '5.2.3' ) );
    }


}