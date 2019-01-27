<?php


namespace calderawp\calderaforms\Tests\Util\Mocks;
use calderawp\calderaforms\cf2\Fields\Handlers\UploaderContract;

class MockUploader implements UploaderContract
{


    public function upload($file, array $args = array())
    {
        // TODO: Implement upload() method.
    }

    public function removeFilter()
    {
        // TODO: Implement removeFilter() method.
    }

    public function addFilter($fieldId, $formId, $private,$controlCode = null)
    {
        // TODO: Implement addFilter() method.
    }

    public function scheduleFileDelete($fieldId, $formId, $file)
	{
		// TODO: Implement scheduleFileDelete() method.
	}



	public function isFileTooLarge(array $field, $filePath)
	{
		// TODO: Implement isFileTooLarge() method.
	}
}