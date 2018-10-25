<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Transients\TransientApiContract;


class FileUpload
{

    /**
     * @var array
     */
    protected $field;
    /**
     * @var array
     */
    protected $form;

    /**
     * @var UploaderContract
     */
    protected  $uploader;
    public function __construct(array $field, array $form, UploaderContract $uploader )
    {
        $this->field = $field;
        $this->form = $form;
        $this->uploader = $uploader;
    }


    /**
     * @param array $files
     * @param array $hashes
     * @return array
     * @throws \Exception
     */
    public function processFiles(array $files,array $hashes  ){

        $i = 0;
        foreach ($files as  $file) {
            $isPrivate = \Caldera_Forms_Files::is_private($this->field);


                $uploadArgs = array(
                    'private' => true,
                    'field_id' => $this->field['ID'],
                    'form_id' => $this->form['ID']
                );



            $expected = $hashes[$i];
            $actual      = md5_file( $file['tmp_name'] );

            if ( $expected !== $actual ) {
                throw new \Exception(__( 'Content hash did not match expected.' ), 412 );
            }


            $this->uploader
                ->addFilter(
                    $this->field[ 'ID' ],
                    $this->form[ 'ID' ],
                    $isPrivate
            );


            $upload = wp_handle_upload($file, array( 'test_form' => false, 'action' => 'foo' ) );
            $this->uploader->removeFilter();
            if( !empty( $field['config']['media_lib'] ) ){
                \Caldera_Forms_Files::add_to_media_library( $upload, $field );
            }


            $uploads[] = $upload['url'];
            $i++;

        }



        return $uploads;
    }
}