<?php

namespace calderawp\calderaforms\Tests\Integration\Handlers;

use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler;
use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\Traits\TestsImages;

class FileFieldHandlerTest extends TestCase
{

	use TestsImages;


    /**
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler::processField()
     *
     * @group field
     * @group cf2
     * @group process
     */
    public function testProcessField()
    {
        $container = $this->getContainer();
        $handler = new FileFieldHandler($container );
        $control = uniqid(rand());
        $data = [ 'https://foo.com/example.gif'];
        $container->getTransientsApi()
            ->setTransient($control, $data );
        $this->assertEquals( $data, $handler->processField($control, ['ID' => 'cf1'], ['ID' => 'fld1'] ) );
    }

    /**
     *
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler::processField()
     *
     * @group field
     * @group cf2
     * @group process
     */
    public function testNotProcessField()
    {
        $container = $this->getContainer();
        $handler = new FileFieldHandler($container);
        $this->assertTrue( empty($handler->processField([], ['ID' => 'cf1'], ['ID' => 'fld1'] ) ) );
    }


    /**
     * Test that transient is deleted when field is saved
     *
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler::saveField();
     */
    public function testDeletesTransient(){
        $field = array(
            'ID' => 'cf2_file_4',
            'type' => 'cf2_file',
            'label' => 'multiple allowed',
            'slug' => 'cf2_file_4',
            'conditions' =>
                array(
                    'type' => '',
                ),
            'caption' => '',
            'config' =>
                array(
                    'multi_upload' => true,
                    'custom_class' => '',
                    'multi_upload_text' => '',
                    'allowed' => 'png',
                    'email_identifier' => 0,
                    'personally_identifying' => 0,
                    'media_lib' => true
                ),
        );
        $container = $this->getContainer();
        $handler = new FileFieldHandler($container);
        $control = uniqid('roy3sivan');
        $container->getTransientsApi()
            ->setTransient($control, ['https://hiroy.club/roy.gif' ] );
        $form = [ 'ID' => 'cf1' ];
        $handler->processField($control, $field, $form );
        $handler->saveField( $control, $field, $form, 2 );
        $this->assertTrue(
            empty( $container->getTransientsApi()->getTransient($control) )
        );

    }


	/**
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
	 */
	public function testKnowsFileIsTooLarge()
	{
		$field = [ 'ID' => 'fld1', 'config' => [ 'max_upload' => 42 ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new Cf1FileUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$this->assertTrue( $handler->isFileTooLarge( $this->createSmallCat() ) );

	}

	/**
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::processFiles()
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::isFileTooLarge()
	 */
	public function testExceptionWhenFileIsTooLarge()
	{
		$this->expectException(Exception::class);
		$field = [ 'ID' => 'fld1', 'config' => [ 'max_upload' => 42 ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new Cf1FileUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$handler->processFiles([ $this->createSmallCat() ], []);

	}

	/**
 	 *
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
	 */
	public function testAllowsFileSize()
	{
		$field = [ 'ID' => 'fld1', 'config' => [ 'max_upload' => 0 ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new Cf1FileUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$file = $this->createSmallCat();
		$this->assertFalse( $handler->isFileTooLarge( $file ) );


		$field = [ 'ID' => 'fld1', 'config' => [ 'max_upload' => 1000 ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new Cf1FileUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$image = $this->createSmallCat();
		$this->assertTrue( $handler->isFileTooLarge( $image ) );

	}
}
