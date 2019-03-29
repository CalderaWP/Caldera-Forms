<?php


namespace calderawp\calderaforms\cf2\RestApi\Process;


use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\RestApi\Endpoint;

class Submission extends Endpoint
{

	const URI = 'process/submission';

	const VERIFY_FIELD = '_cf_verify';

	/**
	 * @return string
	 */
	protected function getUri()
	{
		return self::URI;
	}

	/** @inheritdoc */
	protected function getArgs()
	{
		return [

			'methods' => 'POST',
			'callback' => [$this, 'createItem'],
			'permission_callback' => [$this, 'permissionsCallback' ],
			'args' => [
				self::VERIFY_FIELD => [
					'type' => 'string',
					'description' => __('Verification token (nonce) for form', 'caldera-forms'),
					'required' => true,
				],
				'formId' => [
					'type' => 'string',
					'description' => __('ID for form field belongs to', 'caldera-forms'),
					'required' => true,
				],
				'entryData' => [
					'type' => 'array',
					'description' => __('Entry Data', 'caldera-forms'),

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
		return ! empty($request->get_param( 'verify') );
		return \Caldera_Forms_Render_Nonce::verify_nonce(
			$request->get_param( 'verify'),
			$request->get_param( 'formId' )
		);
	}



	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 * @throws \Exception
	 */
	public function createItem(\WP_REST_Request $request)
	{


		$this->setFormById( $request->get_param('formId'));
		$formId = $this->getForm()[ 'ID' ];
		$entryData = $request->get_param( 'entryData' );
		$entryId = null;
		$fields = \Caldera_Forms_Forms::get_fields($this->getForm() );
		if( empty( $fields ) ){
			return rest_ensure_response( new \WP_Error( 500, __( 'Invalid form', 'caldera-forms')  ) );
		}
		$entryObject = new \Caldera_Forms_Entry_Entry();
		$entryObject->status = 'pending';
		$entryObject->form_id = $formId;
		$entryObject->user_id = get_current_user_id();
		$entryObject->datestamp = date_i18n('Y-m-d H:i:s', time(), 0);

		$entry = new \Caldera_Forms_Entry( $this->getForm(), $entryId, $entryObject );
		foreach ($fields as $fieldId => $field ){
			$fieldId = $field[ 'ID' ];
			if( in_array( \Caldera_Forms_Field_Util::get_type($field, $this->getForm() ) ,
				[
					'html'
				])
			){
				continue;
			}

			$value = isset( $entryData[ $fieldId]) ? \Caldera_Forms_Sanitize::sanitize($entryData[ $fieldId] ) : null;
			if( ! is_null($value) ){
				$entryField = new \Caldera_Forms_Entry_Field();
				$entryField->slug = $field[ 'slug' ];
				$entryField->field_id = $fieldId;
				$entryField->value =  $value;
				$entry->add_field($entryField);
			}

		}
		$entryId = $entry->save();
		if( is_numeric($entryId ) ){
			$response = rest_ensure_response( ['entryId' => $entryId, ] );
			$response->set_status(201);
		}else{
			$response = rest_ensure_response( new \WP_Error( 500, __( 'Could not submit entry','caldera-forms')) );
		}
		return $response;
	}
}
