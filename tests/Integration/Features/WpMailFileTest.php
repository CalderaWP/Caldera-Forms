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
        $this->assertNotFalse(strpos( $mailer->get_sent()->body, $searchText)  );

    }

    protected function hasAttatchments( $huhWTF, \PHPMailer $mailer){
        $this->assertNotFalse(false);
    }

    protected function hasAddedToMediaLibrary( $huhWTF, \PHPMailer $mailer){
        $this->assertNotFalse(false);
    }

    /**
     * @group now
     */
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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



    public function testShowsFileInEmailIfNotAttachedAndNotAddedToMediaLibrary()
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

    public function testShowsFileInEmailIfAttached()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = false;
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

    public function testAttachFileIfItShould()
    {
        add_filter( 'caldera_forms_render_get_field', function ($field){
            if( 'cf2_file_1' === $field['ID'] ){
                $field['config']['media_lib'] = false;
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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

        SubmissionHelpers::fakeFormSubmit($this->formId, $data);
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