<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;
use calderawp\calderaforms\cf2\Fields\Handlers\UploaderContract;


class Cf1FileUploader implements UploaderContract
{

    /** @inheritdoc */
    public function upload($file, array $args = array())
    {
       return \Caldera_Forms_Files::upload($file,$args);
    }

	/** @inheritdoc */
	public function addFilter($fieldId, $formId, $private,$transientId= null )
    {
        \Caldera_Forms_Files::add_upload_filter($fieldId,$formId,$private,$transientId);
    }
	/** @inheritdoc */
    public function removeFilter()
    {
       \Caldera_Forms_Files::remove_upload_filter();
    }

	/** @inheritdoc */
    public function scheduleFileDelete($fieldId,$formId,$file)
	{
		return \Caldera_Forms_Files::schedule_delete($fieldId,$formId,$file);
	}

	/** @inheritdoc */
	public function isFileTooLarge(array $field, $filePath)
	{
		return \Caldera_Forms_Files::is_file_too_large($field,$filePath);
	}
}