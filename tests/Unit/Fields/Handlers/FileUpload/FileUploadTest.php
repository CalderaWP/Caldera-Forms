<?php

namespace calderawp\calderaforms\Tests\Unit\Fields\Handlers\FileUpload;

use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Unit\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockUploader;

class FileUploadTest extends TestCase
{

    /**
     * Test setup of properties
     *
     * @since 1.8.0
     *
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::__construct()
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$field
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$form
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$uploader
     */
    public function test__construct()
    {
        $field = [ 'ID' => 'fld1'];
        $form = ['ID' => 'cd1' ];
        $uploader = new MockUploader();
        $handler = new FileUpload($field,$form,$uploader);
        $this->assertAttributeEquals( $field,'field', $handler );
        $this->assertAttributeEquals( $form,'form', $handler );
        $this->assertAttributeEquals( $uploader,'uploader', $handler );
    }
}
