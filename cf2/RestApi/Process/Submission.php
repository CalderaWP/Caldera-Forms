<?php


namespace calderawp\calderaforms\cf2\RestApi\Process;


use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\RestApi\Endpoint;
use calderawp\calderaforms\cf2\RestApi\Token\VerifiesFormToken;
use calderawp\calderaforms\cf2\RestApi\Token\UsesFormJwtContract;

class Submission extends Endpoint implements UsesFormJwtContract
{

	use VerifiesFormToken;

	/**
	 * @var string
	 *
	 * @since 1.9.0
	 */
	const URI = 'process/submission';

	/**
	 * @var string
	 *
	 * @since 1.9.0
	 */
	const VERIFY_FIELD = '_cf_verify';

	/**
	 * @var string
	 *
	 * @since 1.9.0
	 */
	const SESSION_ID_FIELD = '_sessionPublicKey';

	/**
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function getUri()
	{
		return self::URI . '/(?P<formId>[\w-]+)';
	}


	/** @inheritdoc */
	protected function getArgs()
	{
		return [

			'methods' => 'POST',
			'callback' => [$this, 'createItem'],
			'permission_callback' => [$this, 'permissionsCallback'],
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
				'entryValues' => [
					'type' => 'object',
					'description' => __('Entry values', 'caldera-forms'),

				],
				self::SESSION_ID_FIELD => [
					'type' => 'string',
					'description' => __('Unique session ID used to create token', 'caldera-forms'),
					'required' => true,
				],
			],
		];
	}


	/**
	 * Create an entry
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 * @throws \Exception
	 */
	public function createItem(\WP_REST_Request $request)
	{

		$this->setFormById($this->getFormIdFromRequest($request));
		$formId = $this->getForm()[ 'ID' ];
		$entryValues = $request->get_param('entryValues');
		$entryId = null;
		$fields = \Caldera_Forms_Forms::get_fields($this->getForm());
		if (empty($fields)) {
			return rest_ensure_response(new \WP_Error(500, __('Invalid form', 'caldera-forms'), [
				'formID' => $formId,
				'external' => $this->getForm()['_external_form'],
			] ));
		}
		$sessionId = $this->getSessionIdFromRequest($request);
		$entryObject = new \Caldera_Forms_Entry_Entry();
		$entryObject->status = 'pending';
		$entryObject->form_id = $formId;
		$entryObject->user_id = get_current_user_id();
		$entryObject->datestamp = date_i18n('Y-m-d H:i:s', time(), 0);

		$entry = new \Caldera_Forms_Entry($this->getForm(), $entryId, $entryObject);
		foreach ($fields as $fieldId => $field) {
			$fieldId = $field[ 'ID' ];
			if (in_array(\Caldera_Forms_Field_Util::get_type($field, $this->getForm()),
				[
					'html',
				])
			) {
				continue;
			}

			$value = isset($entryValues[ $fieldId ]) ? \Caldera_Forms_Sanitize::sanitize($entryValues[ $fieldId ]) : null;
			if (!is_null($value)) {
				$entryField = new \Caldera_Forms_Entry_Field();
				$entryField->slug = $field[ 'slug' ];
				$entryField->field_id = $fieldId;
				$entryField->value = $value;
				try {
					$entry = $this->addEntryFieldToEntryWithFilter($entryField, $entry, $field);
				} catch (Exception $e) {
					return rest_ensure_response($e->toWpError());
				}

			}

		}

		$entryField = new \Caldera_Forms_Entry_Field();
		$entryField->slug = self::SESSION_ID_FIELD;
		$entryField->field_id = self::SESSION_ID_FIELD;
		$entryField->value = $sessionId;
		try {
			$entry = $this->addEntryFieldToEntryWithFilter($entryField, $entry, [
				'slug' => self::SESSION_ID_FIELD,
				'ID' => self::SESSION_ID_FIELD,
				'type' => self::SESSION_ID_FIELD,
			]);
		} catch (Exception $e) {
			return rest_ensure_response($e->toWpError());
		}

		/**
		 * Runs BEFORE an entry is saved when creating via REST API
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		$entry = apply_filters('calderaForms/restApi/createEntry/preSave', $entry,$sessionId);
		$entryId = $entry->save();
		/**
		 * Runs AFTER an entry is saved when creating via REST API
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. $entryId = $entry->get_entry_id();
		 * @param string $sessionId ID of session
		 */
		$entry = apply_filters('calderaForms/restApi/createEntry/postSave', $entry,$sessionId);
		if (is_numeric($entryId)&&$entryId) {

			/**
			 * Runs AFTER an entry created via REST API completes
			 *
			 * @since 1.9.0
			 *
			 * @param \Caldera_Forms_Entry $entry Entry being saved. $entryId = $entry->get_entry_id();
			 * @param string $sessionId ID of session
			 */
			do_action('calderaForms/restApi/createEntry/complete', $entry,$sessionId);

			/**
			 * Runs before an entry is saved when creating via REST API
			 *
			 * @since 1.9.0
			 *
			 * @param array $responseData Data to return
			 * @param \Caldera_Forms_Entry $entry Entry that has been saved
			 * @param string $sessionId ID of session
			 */
			$responseData = apply_filters('calderaForms/restApi/createEntry/responseData', [
				'entryId' => $entryId,
				'message' => $this->getForm()['success']
			], $entry,$sessionId);
			$response = rest_ensure_response($responseData);
			$response->set_status(201);
		} else {
			$response = rest_ensure_response(new \WP_Error(500, __('Could not submit entry', 'caldera-forms')));
		}
		return $response;
	}

	/**
	 * @inheritdoc
	 */
	protected function getFormIdFromRequest(\WP_REST_Request $request)
	{
		$urlParams=  $request->get_url_params();
		return ! empty($urlParams['formId']) ? $urlParams['formId'] : $request->get_param('formId');
	}

	/**
	 * @inheritdoc
	 */
	protected function getTokenFromRequest(\WP_REST_Request $request)
	{
		return $request->get_param(self::VERIFY_FIELD);
	}

	/**
	 * @inheritdoc
	 */
	protected function getSessionIdFromRequest(\WP_REST_Request $request)
	{
		return $request->get_param(self::SESSION_ID_FIELD);
	}

	/**
	 * Add entry field to entry, with a filter
	 *
	 * @since 1.9.0
	 *
	 * @param \Caldera_Forms_Entry_Field $entryField Entry field to be added
	 * @param \Caldera_Forms_Entry $entry Entry it is being added to
	 * @param array $fieldConfig Config for field being saved
	 *
	 * @return \Caldera_Forms_Entry
	 * @throws Exception
	 */
	protected function addEntryFieldToEntryWithFilter(\Caldera_Forms_Entry_Field $entryField, \Caldera_Forms_Entry $entry,array $fieldConfig)
	{
		/**
		 * Runs before an entry field is added to entry when creating via REST API
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry_Field $entryField Entry field to be added
		 * @param \Caldera_Forms_Entry $entry Entry it is being added to
		 * @param array $fieldConfig Config for field being saved
		 */
		$entryField = apply_filters('calderaForms/restApi/createEntry/addField', $entryField, $entry, $fieldConfig);
		if( is_wp_error( $entryField->value ) ){
			throw  Exception::fromWpError($entryField->value );
		}
		$entry->add_field($entryField);
		return $entry;
	}


}
