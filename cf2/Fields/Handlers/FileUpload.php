<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;

use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Transients\TransientApiContract;


class FileUpload
{

    /**
     * Field config
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $field;
    /**
     * Form config
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $form;

    /**
     * Upload handler
     *
     * @since 1.8.0
     *
     * @var UploaderContract
     */
    protected $uploader;

    /**
     * FileUpload constructor.
     *
     * @since 1.8.0
     *
     * @param array $field Field config
     * @param array $form Form config
     * @param UploaderContract $uploader Upload handler to use
     */
    public function __construct(array $field, array $form, UploaderContract $uploader)
    {
        $this->field = $field;
        $this->form = $form;
        $this->uploader = $uploader;
    }


    /**
     * Process file upload
     *
     * @since 1.8.0
     *
     * @param array $files Files to process
     * @param array $hashes Supplied file hashes to compare actual hashes against
     * @return array
     * @throws \Exception
     */
    public function processFiles(array $files, array $hashes)
    {
        $uploads = [];
        $i = 0;
        foreach ($files as $file) {
            $isPrivate = \Caldera_Forms_Files::is_private($this->field);

            $expected = $hashes[$i];
            $actual = md5_file($file['tmp_name']);

            if ($expected !== $actual ) {
                throw new Exception(__( 'Content hash did not match expected.' ), 412 );
            }

            $this->uploader
                ->addFilter(
                    $this->field['ID'],
                    $this->form['ID'],
                    $isPrivate
                );

            if (!$this->isAllowedType($file)) {
                throw new Exception(__('This file type is not allowed. Please try another.', 'caldera-forms'), 415);
            }

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $upload = wp_handle_upload($file, array('test_form' => false, 'action' => 'foo'));


            $this->uploader->removeFilter();
            if (\Caldera_Forms_Files::should_attach($this->field, $this->form)) {
                \Caldera_Forms_Files::add_to_media_library($upload, $this->field);
            }


            $uploads[] = $upload['url'];
            $i++;

        }

        return $uploads;
    }

    /**
     * Check if file type if allowed for this field
     *
     * @since 1.8.0
     *
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function isAllowedType($file)
    {
        if (empty($this->field['config']['allowed'])) {
            return true;
        }
        $fileName = ! empty( $file[ 'name' ] ) ? $file[ 'name' ] :basename($file['tmp_name']);
        $filetype = wp_check_filetype(basename($fileName), null);
        return in_array(strtolower($filetype['ext']), $this->getAllowedTypes());
    }

    /**
     * Get allowed file types for file field
     *
     * @since 1.8.0
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        $types = !empty($this->field['config']['allowed']) ? $this->field['config']['allowed'] : [];
        if (!is_array($types)) {
            $types = explode(',', $types);
        }
        if( in_array( 'jpg', $types ) && ! in_array( 'jpeg', $types ) ){
        	$types[] = 'jpeg';
		}
        return $types;
    }

}
