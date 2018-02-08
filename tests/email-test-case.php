<?php
function slug_send_email( $to, $subject, $message, $headers = '' ){
    wp_mail( $to, $subject, $message, $headers );
}

/**
 * Test case -- step 2
 */
abstract  class Email_Test_Case extends WP_UnitTestCase{

    /** @inheritdoc */
    public function setUp(){
        parent::setUp();
        $this->reset_mailer();
    }

    /** @inheritdoc */
    public function tearDown(){
        parent::tearDown();
        $this->reset_mailer();
    }


    /**
     * Reset mailer
     *
     * @return bool
     */
    protected function reset_mailer(){
        return reset_phpmailer_instance();
    }

    /**
     * Get mock mailer
     *
     * Wraps tests_retrieve_phpmailer_instance()
     *
     * @return MockPHPMailer
     */
    protected function get_mock_mailer(){
        return tests_retrieve_phpmailer_instance();
    }
}