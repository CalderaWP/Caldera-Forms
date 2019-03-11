<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


interface UploaderContract
{

    /**
     * Do the upload
     *
     * @param array $file File to upload
     * @param array $args Optional Additonal args to pass to upload function
     * @return mixed
     */
    public function upload($file, array $args = array());

    /**
     * Add upload related filters
     *
     * Changes directory name
     *
     * @since 1.8.0
     *
     * @param string $fieldId The field ID for file field
     * @param string $formId The form ID
     * @param boolean $private
     * @return void
     */
    public function addFilter($fieldId, $formId, $private, $transientId);

    /**
     * Remove upload related filters
     *
     * @since 1.8.0
     *
     * @return void
     */
    public function removeFilter();

	/**
	 * Schedule file to be deleted as soon as possible
	 *
	 * @since 1.8.0
	 *
	 * @param string $fieldId ID of field
	 * @param string $formId ID of form
	 * @param string $file Path to file to delete.
	 *
	 * @return bool
	 */
	public function scheduleFileDelete($fieldId,$formId,$file);

	/**
	 * Check if file is too large to upload
	 *
	 * @since 1.8.0
	 *
	 * @param array $field Field config
	 * @param string $filePath Path to file to check
	 *
	 * @return bool
	 */
	public function isFileTooLarge(array $field,$filePath);
}