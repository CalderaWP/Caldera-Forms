<?php

namespace calderawp\calderaforms\Tests\Unit\Fields\Handlers\FileUpload;

use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Unit\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockUploader;

class FileUploadTest extends TestCase
{

    /**
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::__construct()
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$field
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$form
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$transientApi
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$uploader
     */
    public function test__construct()
    {
        $field = [ 'ID' => 'fld1'];
        $form = ['ID' => 'cd1' ];
        $transients = new Cf1TransientsApi();
        $uploader = new MockUploader();
        $handler = new FileUpload($field,$form, $transients,$uploader);
        $this->assertAttributeEquals( $field,'field', $handler );
        $this->assertAttributeEquals( $form,'form', $handler );
        $this->assertAttributeEquals( $transients,'transientApi', $handler );
        $this->assertAttributeEquals( $uploader,'uploader', $handler );
    }
}
