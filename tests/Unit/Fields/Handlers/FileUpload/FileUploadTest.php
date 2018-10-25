<?php

namespace calderawp\calderaforms\Tests\Unit\Fields\Handlers\FileUpload;

use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Unit\TestCase;

class FileUploadTest extends TestCase
{

    /**
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::__construct()
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$field
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$form
     * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$transientApi
     */
    public function test__construct()
    {
        $field = [ 'ID' => 'fld1'];
        $form = ['ID' => 'cd1' ];
        $transients = new Cf1TransientsApi();
        $handler = new FileUpload($field,$form, $transients);
        $this->assertAttributeEquals( $field,'field', $handler );
        $this->assertAttributeEquals( $form,'form', $handler );
        $this->assertAttributeEquals( $transients,'transientApi', $handler );
    }
}
