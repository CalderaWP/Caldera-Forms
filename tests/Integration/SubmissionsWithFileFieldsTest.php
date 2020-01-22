<?php


namespace calderawp\calderaforms\Tests\Integration;

use Brain\Monkey\Filters;
use Brain\Monkey\Actions;

use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;
use calderawp\calderaforms\Tests\Util\Traits\TestsImages;
use calderawp\calderaforms\Tests\Util\Traits\TestsSubmissions;

class SubmissionsWithFileFieldsTest extends TestCase
{

    use TestsSubmissions, TestsImages;



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
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );
        parent::setUp();
    }

    /** @inheritdoc */
    public function tearDown(){

        if ($this->entryId) {
            \Caldera_Forms_Entry_Bulk::delete_entries([$this->entryId]);
        }
        $this->entryId = null;
        remove_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );
		$this->deleteTestCatFile();
        parent::tearDown();
    }


    /**
     * Test that if a file field's value is a control code at submission, and that data is in a transient, it is saved in the submission.
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testCompletingSubmission()
    {
        $fileData = [
            'http://testCompletingSubmission.org/cf2_file_1/testCompletingSubmission-41.jpeg'
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


        $value= \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData,
            $value
        );
    }



    /**
     */
    public function testSubmissionsDoNotShareData()
    {

        $fileData1 = [
            'http://11testSubmissionsDoNotShareData.org/cf2_file_1/testCompletingSubmission-1.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand() );
        $transientApi->setTransient($control, $fileData1, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);

        $this->assertNotNull( $this->entryId );

        $lastId = $this->entryId;
        $this->resetCfGlobals();

        $control = uniqid(rand());
        $fileData2 = [
            'http://2testSubmissionsDoNotShareData.org/cf2_file_1/testCompletingSubmission-2.jpeg'
        ];
        $transientApi = new Cf1TransientsApi();

        $transientApi->setTransient($control, $fileData2, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_1' => $control
        ]);


        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );
        $this->assertNotEquals( $lastId, $this->entryId );


        $value1 = \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $lastId );
        $this->assertEquals(
            $fileData1[0],
            $value1
        );
        $value2= \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData2,
            $value2
        );



    }

    /**
     * Test that forms with multiple file fields save data to the right field
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testCompletingSubmissionMultipleFields()
    {

        $transientApi = new Cf1TransientsApi();
        $fileData1 = [
            'http://fileData1.org/cf2_file_1-1.jpeg'
        ];

        $control1 = uniqid(rand() );
        $transientApi->setTransient($control1, $fileData1, 66 );
        $fileData2 = [
            'http://fileData2.org/cf2_file_2-2.jpeg'
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


        $value= \Caldera_Forms::get_field_data('cf2_file_2', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData2,
            $value
        );

        $field = $entry->get_field( 'cf2_file_3' );
        $this->assertTrue(
            is_null( $field )
        );

        $value= \Caldera_Forms::get_field_data('cf2_file_1', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData1,
            $value
        );

    }

    /**
     * Test that a field saves single value if allwoed to save multiple values when only one value.
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testOneFileSubmissionToMultiFileFeild()
    {

        $fileData = [
            'http://example.org/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-3.jpeg'
        ];

        $transientApi = new Cf1TransientsApi();
        $control = uniqid(rand() );


        $transientApi->setTransient($control, $fileData, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_4' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );


        $value= \Caldera_Forms::get_field_data('cf2_file_4', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData,
            $value
        );
        $transientApi->deleteTransient($control);
    }


    /**
     * Test that a field saves multiple values if allowed
     *
     * @since 1.8.0
     *
     * @covers Caldera_Forms::process_submission()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testMultiFileSubmission()
    {

        $fileData = [
            'http://1.com/wp-content/uploads/41e00da3e3b46a7ba1d34355e3fc45fe/cf-payfast.jpg',
            'http://2.com/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-31.jpeg',
            'http://3.com/wp-content/uploads/6ce32e892d96e3e1931f2001e52477de/screenshot-3113.jpeg',
            'https://4.com/wp-content/uploads/41e00da3e3b46a7ba1d34355e3fc45fe/cf-payfast.png'
        ];

        $transientApi = new Cf1TransientsApi();
        $control = uniqid('tupl' );


        $transientApi->setTransient($control, $fileData, 66 );

        $data = SubmissionHelpers::submission_data($this->formId, [
            'test_field_1' => 'Hi Roy',
            'cf2_file_4' => $control
        ]);
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );

        SubmissionHelpers::fakeFormSubmit($this->formId,$data);
        $this->assertNotNull( $this->entryId );

        $value= \Caldera_Forms::get_field_data('cf2_file_4', $this->form, $this->entryId );
        $this->assertEquals(
            $fileData,
            $value
        );
    }

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::addFilter()
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::removeFilter()
	 */
	public function testScheduleDelete(){

		$file = $this->createSmallCat();
		$this->assertTrue(file_exists($file['tmp_name']));
		$fieldId = 'testScheduleDelete';

		$uploadArgs = [
			'private' => true,
			'field_id' =>$fieldId,
			'form_id' =>  $this->formId
		];
		$field = \Caldera_Forms_Field_Util::get_field($fieldId,$this->form);
		$this->assertTrue( is_array($field));
		$this->assertFalse( \Caldera_Forms_Files::should_add_to_media_library($field));
		$this->assertFalse( \Caldera_Forms_Files::is_persistent($field));

		$uploader = new Cf1FileUploader();
		$this->assertTrue ( file_exists( $file[ 'tmp_name']) );


		/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
		$scheduler = caldera_forms_get_v2_container()->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
		$this->assertTrue( $uploader->scheduleFileDelete($uploadArgs['field_id'], $uploadArgs[ 'form_id'], $file['tmp_name'] ) );
		$this->assertTrue ( file_exists( $file[ 'tmp_name']) );

		$scheduler->runJobs(99);
		$this->assertFalse( file_exists( $file[ 'tmp_name']) );


	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::scheduleFileDelete()
	 *
	 */
	public function testDeleteScheduled(){

		$file = $this->createSmallCat();
		$this->assertTrue(file_exists($file['tmp_name']));
		$fieldId = 'testScheduleDelete';

		$uploadArgs = [
			'private' => true,
			'field_id' =>$fieldId,
			'form_id' =>  $this->formId
		];
		$field = \Caldera_Forms_Field_Util::get_field($fieldId,$this->form);
		$this->assertTrue( is_array($field));
		$this->assertFalse( \Caldera_Forms_Files::should_add_to_media_library($field));
		$this->assertFalse( \Caldera_Forms_Files::is_persistent($field));

		$uploader = new Cf1FileUploader();
		$this->assertTrue ( file_exists( $file[ 'tmp_name']) );


		/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
		$scheduler = caldera_forms_get_v2_container()->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
		$this->assertTrue( $uploader->scheduleFileDelete($uploadArgs['field_id'], $uploadArgs[ 'form_id'], $file['tmp_name'] ) );
		$this->assertTrue ( file_exists( $file[ 'tmp_name']) );

		$scheduler->runJobs(99);
		$this->assertFalse( file_exists( $file[ 'tmp_name']) );


	}



}
