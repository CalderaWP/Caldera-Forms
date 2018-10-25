<?php

namespace calderawp\calderaforms\Tests\IntegrationFields\Handlers;

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
     * @group now
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
        $uploads = $handler->processFiles($files, [md5_file($this->test_file)], 'f1' );
        $this->assertTrue( is_array( $uploads ));
        $this->assertEquals( 1, count($uploads ) );

    }

    public function testFilterDirectoryForUpload(){
        add_filter( 'caldera_forms_upload_directory', function(){
            return 'form-uploads';
        });

    }
}
