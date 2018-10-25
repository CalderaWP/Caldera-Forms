<?php

namespace calderawp\calderaforms\Tests\IntegrationFields\Handlers;

use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Integration\TestCase;

class FileUploadTest extends TestCase
{
    protected $test_file;


    public function setUp()
    {
        $orig_file = __DIR__ . '/screenshot.jpeg';
        $this->test_file = '/tmp/screenshot.jpg';
        copy($orig_file, $this->test_file);
        parent::setUp();
    }

    /**
     * @throws \Exception
     *
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::processFiles()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     */
    public function testProcessFile()
    {

        $formId = 'cf2_file';
        $fieldId = 'cf2_file_1';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $control = \Caldera_Forms_Field_Util::generate_file_field_unique_id(
            $field,
            $form
        );

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.jpeg',
                'size' => filesize($this->test_file),
                'tmp_name' => $this->test_file,
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );
        $uploads = $handler->processFiles($files, [md5_file($this->test_file)] );
        $this->assertTrue( is_array( $uploads ));
        $this->assertEquals( 1, count($uploads ) );

    }

    protected $filterWasCalled;
    /**
     * Test that caldera_forms_upload_directory filter is respected
     *
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::processFiles()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     * @throws \Exception
     */
    public function testFilterDirectoryForUpload(){
        add_filter( 'caldera_forms_upload_directory', function() {
            return 'form-uploads';
        });

        $formId = 'cf2_file';
        $fieldId = 'cf2_file_2';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $this->assertFalse(  \Caldera_Forms_Files::is_private($field) );

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.jpeg',
                'size' => filesize($this->test_file),
                'tmp_name' => $this->test_file,
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );

        $uploads = $handler->processFiles($files, [md5_file($this->test_file)] );
        $this->assertTrue( is_array($uploads));
        $this->assertNotFalse( strpos($uploads[0], 'form-uploads'), $uploads[0]);

    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::isAllowedType()
     *
     * @group now
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     * @throws \calderawp\calderaforms\cf2\Exception
     */
    public function testAllTypesAllowedWhenNotSpecified()
    {
        $formId = 'cf2_file';
        $fieldId = 'cf2_file_1';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.jpeg',
                'size' => filesize($this->test_file),
                'tmp_name' => $this->test_file,
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );

        $this->assertTrue( $handler->isAllowedType( $files[0] ) );

    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::isAllowedType()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     * @throws \calderawp\calderaforms\cf2\Exception
     */
    public function testTypesAllowedWhenSpecified()
    {
        $formId = 'cf2_file';
        $fieldId = 'cf2_file_2';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $this->assertFalse(  \Caldera_Forms_Files::is_private($field) );

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.png',
                'size' => filesize($this->test_file),
                'tmp_name' => $this->test_file,
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );

        $this->assertTrue( $handler->isAllowedType( $files[0] ) );

    }


    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     */
    public function testGetAllowedTypes(){
        $formId = 'cf2_file';
        $fieldId = 'cf2_file_2';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );
        $this->assertTrue(is_array( $handler->getAllowedTypes() ) );
        $this->assertTrue( in_array( 'png', $handler->getAllowedTypes() ) );
        $this->assertTrue( in_array( 'jpg', $handler->getAllowedTypes() ) );
    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::isAllowedType()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     * @throws \calderawp\calderaforms\cf2\Exception
     */
    public function testTypesNotAllowedWhenNotSpecified()
    {
        $formId = 'cf2_file';
        $fieldId = 'cf2_file_2';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $this->assertFalse(  \Caldera_Forms_Files::is_private($field) );

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.gif',
                'size' => filesize($this->test_file),
                'tmp_name' => '/tmp/screenshot.gif',
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );
        $this->assertFalse( $handler->isAllowedType( $files[0] ) );

    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::isAllowedType()
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::processFiles()
     *
     * @group cf2
     * @group file
     * @group field
     * @group cf2_file
     *
     */
    public function testProcessInvalidTypeThrowsException()
    {
        $this->expectException(Exception::class);
        $formId = 'cf2_file';
        $fieldId = 'cf2_file_3';
        $form = \Caldera_Forms_Forms::get_form( $formId );
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$form);
        $this->assertFalse(  \Caldera_Forms_Files::is_private($field) );

        $files = [
            [
                'file' => file_get_contents($this->test_file),
                'name' => 'screenshot.jpeg',
                'size' => filesize($this->test_file),
                'tmp_name' => $this->test_file,
            ]
        ];

        $handler = new FileUpload(
            $field,
            $field,
            new Cf1FileUploader()
        );

        $handler->processFiles($files, [md5_file($this->test_file)]);
    }

}
