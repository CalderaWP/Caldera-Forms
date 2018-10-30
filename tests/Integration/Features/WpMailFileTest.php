<?php


namespace calderawp\calderaforms\Tests\Integration\Features;


use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;
use calderawp\calderaforms\Tests\Util\Traits\TestsSubmissions;
use calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;

class WpMailFileTest extends TestCase
{


    use TestsWpMail, TestsSubmissions;
    protected $formId = 'cf2_file';

    /** @inheritdoc */
    public function tearDown()
    {
        $this->entryId = null;
        $this->resetWpMailTests();
        parent::tearDown();
    }

    public function setUp()
    {
        $this->form = \Caldera_Forms_Forms::get_form($this->formId);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);
        parent::setUp();
    }

    protected function messageContains($searchText, \PHPMailer $mailer ){
        if( is_array( $searchText ) ){
            foreach( $searchText as $text ){
                $this->messageContains( $text,$mailer );
            }
        }elseif( is_string( $searchText) ){
            $this->assertNotFalse(strpos( $mailer->get_sent()->body, $searchText)  );
        }else{
            //??
        }

    }


    protected function messageNotContains($searchText, \PHPMailer $mailer ){
        if( is_array( $searchText ) ){
            foreach( $searchText as $text ){
                $this->messageNotContains( $text,$mailer );
            }
        }elseif( is_string( $searchText) ){
            $this->assertFalse(strpos( $mailer->get_sent()->body, $searchText)  );
        }else{
            //??
        }

    }

    protected function hasAttatchments( $huhWTF, \PHPMailer $mailer){
        $this->assertNotFalse(false);
    }

    protected function hasAddedToMediaLibrary( $huhWTF, \PHPMailer $mailer){
        $this->assertNotFalse(false);
    }



    public function testShowsFileInEmailIfNotAttached()
    {

        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = true;
                $field['config']['attach'] = false;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false);
        $this->assertNotNull($this->entryId);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertNotFalse( $mailer->get_sent() );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );
        $this->messageContains( $value, tests_retrieve_phpmailer_instance() );
    }


    /**
     * @group now
     */
    public function testDoesNotShowFileInEmailIfNotAttachedAndNotAddedToMediaLibrary()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                unset( $field['config']['media_lib'] );
                $field['config']['attach'] = false;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $form = \Caldera_Forms_Forms::get_form( $this->formId );
        $field = \Caldera_Forms_Field_Util::get_field( 'cf2_file_1', $form );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        add_filter('caldera_forms_send_email', '__return_true', 10001);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data, false );
        $this->assertTrue( \Caldera_Forms::should_send_mail( $field, []  ) );

        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );

        $this->messageNotContains( $value, tests_retrieve_phpmailer_instance() );
    }


    public function testNotShowsFileInEmailIfAttachedAndNotAddToMediaLibrary()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                unset( $field['config']['media_lib'] );
                $field['config']['attach'] = true;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data, false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );
        $this->messageNotContains( $value, tests_retrieve_phpmailer_instance() );
    }


    public function testShowsFileInEmailIfAttachedAndAddedToMediaLibrary()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = true;
                $field['config']['attach'] = true;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false);
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );
        $this->messageContains( $value, tests_retrieve_phpmailer_instance() );
    }

    /**
     * @group now
     */
    public function testAttachFileIfItShould()
    {

        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $form = \Caldera_Forms_Forms::get_form( $this->formId );
        $field = \Caldera_Forms_Field_Util::get_field( 'cf2_file_5', $form, true );

        $this->assertTrue( \Caldera_Forms_Files::should_attach( $field, $form ) );
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_5' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);
        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_5', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_5', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );
        $this->hasAttatchments( ['?'], tests_retrieve_phpmailer_instance() );
    }

    public function testDoesNotAttachFileIfItShouldNot()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = false;
                $field['config']['attach'] = false;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );

        //FALSE TEST!
        $this->hasAttatchments( ['?'], tests_retrieve_phpmailer_instance() );
    }

    public function testAddsToMediaLibraryIfItShould()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = true;
                $field['config']['attach'] = false;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $this->hasAddedToMediaLibrary( ['?'], tests_retrieve_phpmailer_instance() );

    }


    /**
     * @group now
     */
    public function testAddsToMediaLibraryIfItShouldAndShouldAttatch()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = true;
                $field['config']['attach'] = true;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $this->hasAttatchments( ['?'], tests_retrieve_phpmailer_instance() );
        $this->hasAddedToMediaLibrary( ['?'], tests_retrieve_phpmailer_instance() );

    }

    public function testDoesNotAddToMediaLibraryIfItShouldNot()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = false;
                $field['config']['attach'] = false;
            }
            return $field;
        },10,2);
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand()); //not using standard prefix as prefix should be meaningless
        $transientApi->setTransient($control, $fileData, 66);

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved']);

        SubmissionHelpers::fakeFormSubmit($this->formId, $data,false );
        $this->assertNotFalse( tests_retrieve_phpmailer_instance()->get_sent() );
        $this->assertNotNull($this->entryId);


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );


        $value = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId);
        $this->assertEquals(
            $fileData,
            $value
        );

        //FALSE TEST!
        $this->hasAddedToMediaLibrary( ['?'], tests_retrieve_phpmailer_instance() );
    }

}
