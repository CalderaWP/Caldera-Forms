<?php
include_once dirname( __FILE__ ) . '/email-test-case.php';

/**
 * Class testing Caldera_Forms_Email_Prepare
 */
class Test_Email_Prepared extends Email_Test_Case {


	/**
	 * test return email from rfc822
	 */
	public function test_email_from_rfc822() {
		$_rfc      = 'Roy Sivan <roy@sivan.com>';
		$_expected = 'roy@sivan.com';

		$_actual = Caldera_Forms_Email_Prepare::email_from_rfc822( $_rfc );

		$this->assertEquals( $_expected, $_actual );
	}


	/**
	 * test return name from rfc822
	 */
	public function test_name_from_rfc822() {
		$_rfc      = 'Roy Sivan <roy@sivan.com>';
		$_expected = 'Roy Sivan';

		$_actual = Caldera_Forms_Email_Prepare::name_from_rfc822( $_rfc );

		$this->assertEquals( $_expected, $_actual );
	}


	/**
	 * check the email is rfc_822 format
	 */
	public function test_is_rfc822() {
		$_rfc    = 'Roy Sivan <roy@sivan.com>';
		$_is_rfc = Caldera_Forms_Email_Prepare::is_rfc822( $_rfc );

		$this->assertNotNull( $_is_rfc );
	}


	/**
	 * prepare array of emails and names using the dataProvider
	 *
	 * @dataProvider preparedEmailFormats
	 *
	 * @param $original
	 * @param $expected
	 */
	public function test_prepare_email_array( $original, $expected ) {

		$_actual = Caldera_Forms_Email_Prepare::prepare_email_array( $original );

		$this->assertEquals( $expected, $_actual );
	}


	/**
	 * dataProvider for prepare_email_array test
	 *
	 * @return array
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
	 * test if is array of email address
	 */
	public function test_is_list() {
		$_email_string = 'Name <address@tld.com>, Another Name <another_address@different-tld.com>';

		$_is_list = Caldera_Forms_Email_Prepare::is_list( $_email_string );

		$this->assertTrue( $_is_list );
	}

}