<?php


namespace calderawp\calderaforms\Tests\Integration;


use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;

class SubmissionsWithFileFieldsTest extends TestCase
{

    /**
     * Entry ID for current submission
     *
     * @since 1.8.0
     *
     * @var int
     */
    protected $entryId;
    /**
     * ID of form we are testing
     *
     * Form config is at /tests/includes/forms/cf2-file-include.php
     *
     * @since 1.8.0
     *
     * @var string
     */
    protected $formId = 'cf2_file';

    /**
     * Form configuration we are testing with
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $form;

    /** @inheritdoc */
    public function setUp()
    {
        $this->form = \Caldera_Forms_Forms::get_form( $this->formId );
        parent::setUp();
    }

    /** @inheritdoc */
    public function tearDown(){
        $this->entryId = null;
    }

    /**
     * Capture entry ID when it is saved
     *
     * @since 1.8.0
     *
     * @uses "caldera_forms_entry_saved" hook
     *
     * @param string $entryId
     */
    public function entrySaved($entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * Test that if a file field's value is a control code at submission, and that data is in a transient, it is saved in the submission.
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group now
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testCompletingSubmission()
    {
        $fileData = [
            'http://example.org/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-43.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand() ); //not using standard prefix as prefix should be meaningless

        //A successful upload of a cf2 file field has set this in transient
        //This assumption is tested elsewhere
        $transientApi->setTransient($control, $fileData, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );

        $entry = new \Caldera_Forms_Entry($this->form, $this->entryId );
        $field = $entry->get_field( 'cf2_file_1' );
        $this->assertEquals(
            $fileData[0],
            $field->get_value()
        );
    }

    /**
     * Test that forms with multiple file fields save data to the right field
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group now
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testCompletingSubmissionMultipleFields()
    {

        $transientApi = new Cf1TransientsApi();
        $fileData1 = [
            'http://example.org/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-43.jpeg'
        ];

        $control1 = uniqid(rand() );
        $transientApi->setTransient($control1, $fileData1, 66 );
        $fileData2 = [
            'http://example.org/wp-content/uploads/screenshot-13.jpeg'
        ];
        $control2 = uniqid(rand() );
        $transientApi->setTransient($control2, $fileData2, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control1,
            'cf2_file_2' => $control2
        ]);

        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );

        $entry = new \Caldera_Forms_Entry($this->form, $this->entryId );
        $field = $entry->get_field( 'cf2_file_1' );
        $this->assertEquals(
            $fileData1[0],
            $field->get_value()
        );

        $field = $entry->get_field( 'cf2_file_2' );
        $this->assertEquals(
            $fileData2[0],
            $field->get_value()
        );

        $field = $entry->get_field( 'cf2_file_3' );
        $this->assertTrue(
            empty( $field->get_value())
        );
    }


    /**
     * Test that a field saves multiple values if allowed
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group now
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testMultiFileSubmission()
    {
        $fileData = [
            'http://example.org/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-43.jpeg',
            'http://example.org/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-433.jpeg'
        ];

        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand() ); //not using standard prefix as prefix should be meaningless


        $transientApi->setTransient($control, $fileData, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_4_multi_allowed' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );

        $entry = new \Caldera_Forms_Entry($this->form, $this->entryId );
        $field = $entry->get_field( 'cf2_file_4_multi_allowed' );
        $this->assertEquals(
            $fileData,
            $field->get_value()
        );
    }



}