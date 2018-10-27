<?php


namespace calderawp\calderaforms\Tests\Util\Traits;

/**
 * Trait TestsWpMail
 *
 * Use: Testing wp_mail with MockPHPMailer
 *

use \calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;
class SomethingTest extends TestCase {
    public function tearDown(){
        parent::tearDown();
        $this->reset();
    }

}

 *
 *
 */
trait TestsWpMail
{


    /** @inheritdoc */
    public function reset()
    {
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
     * @return \MockPHPMailer
     */
    protected function get_mock_mailer(){
        return tests_retrieve_phpmailer_instance();
    }

}