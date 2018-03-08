<?php
include_once dirname(__FILE__ ) . '/email-test-case.php';

class Test_Email extends Email_Test_Case {
    /**
     * Test that the to email is correct, when a name is not used along with email
     *
     * @group email
     */
    public function test_to_without_name(){
        $to = 'hi@example.com';
        $subject = 'subject';
        $message = 'message';
        slug_send_email($to, $subject, $message );
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertSame('hi@example.com', $mailer->get_recipient('to')->address );

    }

    /**
     * Test that the to email is correct, when a name is used along with email
     *
     * @group email
     */
    public function test_to_with_name(){
        $to = 'Someone W. Someone <hi@example.com>';
        $subject = 'subject';
        $message = 'message';
        slug_send_email($to, $subject, $message );
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals('Someone W. Someone', $mailer->get_recipient('to')->name);
        $this->assertSame('hi@example.com', $mailer->get_recipient('to')->address );

    }

    /**
     * Test that the to email is correct, when a name is used along with email
     *
     * @group torque
     */
    public function test_to_multiple(){
        $to = array( '1@example.com', '2@example.com' );
        $subject = 'subject';
        $message = 'message';
        slug_send_email($to, $subject, $message );
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertSame($to[0],$mailer->get_sent(0)->to[0][0] );
        $this->assertSame($to[1],$mailer->get_sent(0)->to[1][0] );

    }

    /**
     * Test that the subject is correct
     *
     * @group email
     */
    public function test_subject(){
        $to = 'Someone W. Someone <hi@example.com>';
        $subject = 'subject';
        $message = 'message';
        slug_send_email($to, $subject, $message );
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertSame($subject, $mailer->get_sent()->subject );

    }

    /**
     * Test that the body is correct
     *
     * @group email
     */
    public function test_body(){
        $to = 'Someone W. Someone <hi@example.com>';
        $subject = 'subject';
        $message = "The content of the  message
        
        With some line breaks
        
        And some other stuff like a !!&236d
        
        ";
        slug_send_email($to, $subject, $message );
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertDiscardWhitespace($message, $mailer->get_sent()->body );
    }
}