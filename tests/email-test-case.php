<?php
function slug_send_email( $to, $subject, $message, $headers = '' ){
    wp_mail( $to, $subject, $message, $headers );
}

/**
 * Test case for emails
 */
abstract class Email_Test_Case extends WP_UnitTestCase{


    use \calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;

    /** @inheritdoc */
    public function setUp(){
        parent::setUp();
        $this->reset();
    }

    /** @inheritdoc */
    public function tearDown(){
        parent::tearDown();
        $this->reset();
    }


}