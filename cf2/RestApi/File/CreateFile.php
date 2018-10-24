<?php


namespace calderawp\calderaforms\cf2\RestApi\File;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;

class CreateFile extends File
{

    /** @inheritdoc */
    protected function getArgs()
    {
        return [

            'methods' => 'POST',
            'callback' => [$this, 'createItem'],
            'permission_callback' => [$this, 'permissionsCallback' ],
            'args' => [
                'hashes' => [
                    'description' => __('MD5 has of files to upload, should have same order as files arg', 'caldera-forms')
                ],
                'verify' => [
                    'type' => 'string',
                    'description' => __('Verification token (nonce) for form', 'caldera-forms'),
                    'required' => true,
                ],
                'formId' => [
                    'type' => 'string',
                    'description' => __('ID for form field belongs to', 'caldera-forms'),
                    'required' => true,
                ],
                'fieldId' => [
                    'type' => 'string',
                    'description' => __('ID for field files are submitted to', 'caldera-forms'),
                    'required' => true,
                ],
                'control' => [
                    'type' => 'string',
                    'description' => __('Unique control string for field', 'caldera-forms'),
                    'required' => true,
                ]
            ]
        ];
    }

    /**
     * Permissions check for file field uploads
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request Request object
     *
     * @return bool
     */
    public function permissionsCallback(\WP_REST_Request $request ){
        $form =  \Caldera_Forms_Forms::get_form($request->get_param( 'formId' ) );
        if( FileFieldType::getCf1Identifier() !== \Caldera_Forms_Field_Util::get_type( $request->get_param( 'fieldId' ), $form ) ){
            return false;
        }

        return \Caldera_Forms_Render_Nonce::verify_nonce(
            $request->get_param( 'verify'),
            $request->get_param( 'formId' )
        );
    }

    /**
     * Upload file for cf2 file field
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request
     * @return mixed|\WP_Error|\WP_REST_Response
     */
    public function createItem(\WP_REST_Request $request)
    {

        $files = $request->get_file_params();
        $formId = $request->get_param('formId');
        $this->setFormById($formId);
        $fieldId = $request->get_param('fieldId');
        $field = \Caldera_Forms_Field_Util::get_field($fieldId,$this->getForm());
        $uploads = [];
        $uploader = $this->getUploader($field);
        $hashes = $request->get_param( 'hashes');
        $controlCode = $request->get_param( 'control'  );
        $transdata = \Caldera_Forms_Transient::get_transient($controlCode);
        if( ! is_array( $transdata ) ){
            $transdata = [];
        }

        $i = 0;
        foreach ($files as  $file) {
            if (!\Caldera_Forms_Files::is_private($field)) {
                $uploadArgs = array(
                    'private' => false,
                    'field_id' => $field['ID'],
                    'form_id' => $this->getForm()['ID']
                );
            } else {
                $uploadArgs = array(
                    'private' => true,
                    'field_id' => $field['ID'],
                    'form_id' => $this->getForm()['ID']
                );
            }

            $expected = $hashes[$i];
            $actual      = md5_file( $file['tmp_name'] );

            if ( $expected !== $actual ) {
                return new \WP_Error( 'rest_upload_hash_mismatch', __( 'Content hash did not match expected.' ), array( 'status' => 412 ) );
            }


            $upload = wp_handle_upload($file, array( 'test_form' => false, 'action' => 'foo' ) );
            if( !empty( $field['config']['media_lib'] ) ){
                \Caldera_Forms_Files::add_to_media_library( $upload, $field );
            }


            $uploads[] = $upload['url'];
            $i++;

        }

        \Caldera_Forms_Transient::set_transient($controlCode, array_merge( $transdata, $uploads ), DAY_IN_SECONDS);

        $response = rest_ensure_response([
            'control' => $controlCode
        ]);
        $response->set_status(201);

        return $response;
    }

    /**
     * @param $field
     * @return callable|\WP_Error
     */
    protected function getUploader($field)
    {
        $uploader = \Caldera_Forms_Files::get_upload_handler( $this->getForm(), $field );
        if( ! is_callable( $uploader) ){
            return new \WP_Error( 'invalid-upload-handler', sprintf( __( 'Invalid file upload handler. See %s', 'caldera-forms'), 'https://calderaforms.com/doc/alternative-file-upload-directory/') );
        }

        return $uploader;
    }


}