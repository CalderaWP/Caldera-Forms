<?php


namespace calderawp\calderaforms\Tests\Integration\Features;


use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;
use calderawp\calderaforms\Tests\Util\Traits\TestsImages;
use calderawp\calderaforms\Tests\Util\Traits\TestsSubmissions;
use calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;

class WpMailAttachTest extends TestCase
{


    use TestsImages;

    /** @inheritdoc */
    public function tearDown()
    {
        $this->deleteTestCatFile();
        parent::tearDown();
    }

    public function setUp()
    {
        parent::setUp();
    }


    /**
     * @since 1.8.0
     *
     * @covers \Caldera_Forms_Email_Filters::mail_attachment_check()
     */
    public function testDoesAttachOneFileIfItShould()
    {
        $form = $this->aTestFormForCf2FieldFields();
        $field = \Caldera_Forms_Field_Util::get_field('cf2_file', $form );
        $this->assertTrue( \Caldera_Forms_Field_Util::is_file_field( $field, $form ));
        $this->assertTrue( \Caldera_Forms_Files::should_attach($field,$form));
        $file = $this->createSmallCat();
        $this->assertTrue( file_exists( $file['tmp_name' ] ) );

        $mail = $this->getTestInputForMail();
        $data = $this->getTestData($file);

        $mail = \Caldera_Forms_Email_Filters::mail_attachment_check( $mail, $data, $form );
        $this->assertEquals( ['/tmp/small-cat.jpeg'],$mail['attachments'] );

    }

    /**
     * @since 1.8.0
     *
     * @covers \Caldera_Forms_Email_Filters::mail_attachment_check()
     */
    public function testDoesAttachTwoFileIfItShould()
    {
        $form = $this->aTestFormForCf2FieldFields();
        $field = \Caldera_Forms_Field_Util::get_field('cf2_file', $form );
        $this->assertTrue( \Caldera_Forms_Field_Util::is_file_field( $field, $form ));
        $this->assertTrue( \Caldera_Forms_Files::should_attach($field,$form));
        $file = $this->createSmallCat();
        $this->assertTrue( file_exists( $file['tmp_name' ] ) );
        $file2 = $this->createTinyCat();
        $this->assertTrue( file_exists( $file2['tmp_name' ] ) );

        $mail = $this->getTestInputForMail();
        $data = $this->getTestData($file);
        $data['cf2_file'] = [
            $file['tmp_name'],
            $file2['tmp_name']
        ];

        $mail = \Caldera_Forms_Email_Filters::mail_attachment_check( $mail, $data, $form );
        $this->assertEquals( ['/tmp/small-cat.jpeg', '/tmp/tiny-cat.jpeg'],$mail['attachments'] );

    }

    /**
     * @since 1.8.0
     *
     * @covers \Caldera_Forms_Email_Filters::mail_attachment_check()
     */
    public function testDoesNotAttachFileIfItShouldNot()
    {

        $form = $this->aTestFormForCf2FieldFields();
        $form['fields' ]['cf2_file'][ 'config' ][ 'attach' ] = false;
        $field = \Caldera_Forms_Field_Util::get_field('cf2_file', $form );
        $this->assertTrue( \Caldera_Forms_Field_Util::is_file_field( $field, $form ));
        $this->assertFalse( \Caldera_Forms_Files::should_attach($field,$form));
        $file = $this->createSmallCat();
        $this->assertTrue( file_exists( $file['tmp_name' ] ) );

        $mail = $this->getTestInputForMail();
        $data = $this->getTestData($file);

        $mail = \Caldera_Forms_Email_Filters::mail_attachment_check( $mail, $data, $form );
        $this->assertEquals( [],$mail['attachments'] );
    }







    
    
    protected function aTestFormForCf2FieldFields(){
        return
            array (
                '_last_updated' => 'Tue, 30 Oct 2018 22:02:36 +0000',
                'ID' => 'CF5bd8b087f28a7',
                'cf_version' => '1.7.4',
                'name' => 'File2',
                'scroll_top' => 0,
                'success' => 'Form has been successfully submitted. Thank you.						',
                'db_support' => 1,
                'pinned' => 0,
                'hide_form' => 1,
                'avatar_field' => NULL,
                'form_ajax' => 1,
                'custom_callback' => '',
                'layout_grid' =>
                    array (
                        'fields' =>
                            array (
                                'cf2_file' => '1:1',
                                'fld_4195777' => '2:1',
                                'fld_6551880' => '2:1',
                            ),
                        'structure' => '12|12',
                    ),
                'fields' =>
                    array (
                        'cf2_file' =>
                            array (
                                'ID' => 'cf2_file',
                                'type' => 'cf2_file',
                                'label' => 'cf2 file',
                                'slug' => 'cf2_file',
                                'conditions' =>
                                    array (
                                        'type' => '',
                                    ),
                                'caption' => '',
                                'config' =>
                                    array (
                                        'custom_class' => '',
                                        'attach' => 1,
                                        'multi_upload_text' => 'Add More',
                                        'media_lib' => 1,
                                        'allowed' => '',
                                        'email_identifier' => 0,
                                        'personally_identifying' => 0,
                                    ),
                            ),
                        'fld_4195777' =>
                            array (
                                'ID' => 'fld_4195777',
                                'type' => 'button',
                                'label' => 'Click Me',
                                'slug' => 'click_me',
                                'conditions' =>
                                    array (
                                        'type' => '',
                                    ),
                                'caption' => '',
                                'config' =>
                                    array (
                                        'custom_class' => '',
                                        'type' => 'submit',
                                        'class' => 'btn btn-default',
                                        'target' => '',
                                    ),
                            ),
                    ),
                'page_names' =>
                    array (
                        0 => 'Page 1',
                    ),
                'mailer' =>
                    array (
                        'on_insert' => 1,
                        'sender_name' => 'Caldera Forms Notification',
                        'sender_email' => 'fake@calderawp.com',
                        'reply_to' => '',
                        'email_type' => 'html',
                        'recipients' => '',
                        'bcc_to' => '',
                        'email_subject' => 'File2',
                        'email_message' => '{summary}',
                    ),
                'check_honey' => 1,
                'antispam' =>
                    array (
                        'sender_name' => '',
                        'sender_email' => '',
                    ),
                'conditional_groups' =>
                    array (
                    ),
                'settings' =>
                    array (
                        'responsive' =>
                            array (
                                'break_point' => 'sm',
                            ),
                    ),
                'privacy_exporter_enabled' => false,
                'version' => '1.7.4',
                'db_id' => '65',
                'type' => 'primary',
                'processors' =>
                    array (
                    ),
            );
    }



    /**
     * @return array
     */
    protected function getTestInputForMail()
    {
        $mail = array(
            'recipients' =>
                array(
                    0 => 'fake@calderawp.com',
                ),
            'subject' => 'File2',
            'message' => 'Hi Roy',
            'headers' =>
                array(
                    0 => 'From: Caldera Forms Notification <fake@calderawp.com>',
                    1 => 'Reply-To: ',
                    2 => 'Content-type: text/html',
                ),
            'attachments' =>
                array(),
            'from' => 'fake@calderawp.com',
            'from_name' => 'Caldera Forms Notification',
            'bcc' => false,
            'replyto' => 'fake@calderawp.com',
            'html' => true,
            'csv' => false,
        );
        return $mail;
    }

    /**
     * @param $file
     * @return array
     */
    protected function getTestData($file)
    {
        $data = array(
            'cf2_file' => [],
            'fld_4195777' => 'click',
            'fld_6551880' => null,
        );


        $data['cf2_file'] = [
            $file['tmp_name']
        ];
        return $data;
    }

}
