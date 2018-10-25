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
     * @var TransientApiContract
     */
    protected  $transientApi;

    /**
     * @var UploaderContract
     */
    protected  $uploader;
    public function __construct(array $field, array $form, TransientApiContract $transientApi, UploaderContract $uploader )
    {
        //$field[ 'type' ] = FileFieldType::getCf1Identifier();
        $this->field = $field;
        $this->form = $form;
        $this->transientApi = $transientApi;
        $this->uploader = $uploader;
    }


    /**
     * @param array $files
     * @param array $hashes
     * @param $controlCode
     * @return mixed
     * @throws \Exception
     */
    public function processFiles(array $files,array $hashes, $controlCode  ){
        $transdata = call_user_func([$this->transientApi, 'getTransient'],$controlCode);
        if( ! is_array( $transdata ) ){
            $transdata = [];
        }

        $i = 0;
        foreach ($files as  $file) {
            if (!\Caldera_Forms_Files::is_private($this->field)) {
                $uploadArgs = array(
                    'private' => false,
                    'field_id' => $this->field['ID'],
                    'form_id' => $this->form['ID']
                );
            } else {
                $uploadArgs = array(
                    'private' => true,
                    'field_id' => $this->field['ID'],
                    'form_id' => $this->form['ID']
                );
            }

            $expected = $hashes[$i];
            $actual      = md5_file( $file['tmp_name'] );

            if ( $expected !== $actual ) {
                throw new \Exception(__( 'Content hash did not match expected.' ), 412 );
            }


            $upload = wp_handle_upload($file, array( 'test_form' => false, 'action' => 'foo' ) );
            if( !empty( $field['config']['media_lib'] ) ){
                \Caldera_Forms_Files::add_to_media_library( $upload, $field );
            }


            $uploads[] = $upload['url'];
            $i++;

        }

        call_user_func([$this->transientApi, 'setTransient'],$controlCode, array_merge( $transdata, $uploads ), 24 * 60 * 60 );


        return $controlCode;
    }
}