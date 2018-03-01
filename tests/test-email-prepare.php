<?php
include_once dirname( __FILE__ ) . '/email-test-case.php';

/**
 * Class testing Caldera_Forms_Email_Prepare
 *
 * Testing the static methods of the Caldera_Forms_Email_Prepare
 *
 * @since 1.6.0
 */
class Test_Email_Prepared extends Email_Test_Case {


	/**
	 * Test return email from rfc822 'name <email at domain.com>' format
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::email_from_rfc822()
	 * @covers Caldera_Forms_Email_Prepare::match_rfc()
	 *
	 */
	public function test_email_from_rfc822() {
		$rfc      = 'Roy Sivan <roy@sivan.com>';
		$expected = 'roy@sivan.com';

		$actual = Caldera_Forms_Email_Prepare::email_from_rfc822( $rfc );

		$this->assertEquals( $expected, $actual );
	}


	/**
	 * Test return name from rfc822 'name <email at domain.com>' format
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::name_from_rfc822()
	 * @cover Caldera_Forms_Email_Prepare::match_rfc()
	 *
	 */
	public function test_name_from_rfc822() {
		$rfc      = 'Roy Sivan <roy@sivan.com>';
		$expected = 'Roy Sivan';

		$actual = Caldera_Forms_Email_Prepare::name_from_rfc822( $rfc );

		$this->assertEquals( $expected, $actual );
	}


	/**
	 * Check the email is rfc_822 format
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::is_rfc822()
	 * @covers Caldera_Forms_Email_Prepare::email_from_rfc822()
	 *
	 */
	public function test_is_rfc822() {
		$rfc    = 'Roy Sivan <roy@sivan.com>';
		$is_rfc = Caldera_Forms_Email_Prepare::is_rfc822( $rfc );

		$this->assertNotNull( $is_rfc );
	}


	/**
	 * Prepare array of emails and names using the dataProvider
	 *
	 * @dataProvider preparedEmailFormats
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::prepare_email_array()
	 * @covers Caldera_Forms_Email_Prepare::is_rfc822()
	 *
	 * @param array $original dataProvider array of items to test
	 * @param array $expected dataProvider array of expected result from test
	 *
	 */
	public function test_prepare_email_array( $original, $expected ) {

		$actual = Caldera_Forms_Email_Prepare::prepare_email_array( $original );

		$this->assertEquals( $expected, $actual );
	}


	/**
	 * DataProvider for prepare_email_array test
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::prepare_email_array()
	 *
	 * @return array email and name combinations
	 *
	 */
	public function preparedEmailFormats() {
		return array(

			array(
				array( 'Roy Sivan <roy@sivan.com>', 'Josh@Pollock.com' ),
				array(
					array( 'name' => 'Roy Sivan', 'email' => 'roy@sivan.com' ),
					array( 'name' => '', 'email' => 'Josh@Pollock.com' ),
				),
			),

		);
	}


	/**
	 * Test if is array of rfc822 name|email formats
	 *
	 * @since 1.6.0
	 *
	 * @group email
	 *
	 * @covers Caldera_Forms_Email_Prepare::is_list()
	 *
	 */
	public function test_is_list() {
		$email_string = 'Name <address@tld.com>, Another Name <another_address@different-tld.com>';

		$is_list = Caldera_Forms_Email_Prepare::is_list( $email_string );

		$this->assertTrue( $is_list );
	}

}