<?php


namespace calderawp\calderaforms\cf2\Process;

use \calderawp\calderaforms\cf2\CalderaFormsV2Contract;

class EntryProcessHooks
{

	/**
	 * @var CalderaFormsV2Contract
	 */
	protected $container;


	public function __construct(CalderaFormsV2Contract $container)
	{
		$this->container = $container;
	}

	/**
	 * Subscribe to all events
	 *
	 * @since 1.9.0
	 */
	public function subscribe()
	{
		add_filter('calderaForms/restApi/createEntry/addField',
			[$this, 'validateField'],
			10, 3
		);
		add_filter('calderaForms/restApi/createEntry/preSave',
			[$this, 'subscribeToPreSave'],
			10, 2
		);
		add_filter('calderaForms/restApi/createEntry/postSave',
			[$this, 'subscribeToPostSave'],
			10, 2
		);

	}

	/**
	 * Validate fields
	 *
	 * @since 1.9.0
	 *
	 * @uses "calderaForms/restApi/createEntry/addField" filter
	 *
	 * @param \Caldera_Forms_Entry_Field $entryField Entry field value
	 * @param \Caldera_Forms_Entry $entry Entry object
	 * @param array $fieldConfig Field config
	 *
	 * @return \Caldera_Forms_Entry_Field
	 */
	public function validateField(
		\Caldera_Forms_Entry_Field $entryField,
		\Caldera_Forms_Entry $entry,
		array $fieldConfig
	) {
		$entryField->value = \Caldera_Forms::validate_field_with_filters(
			$fieldConfig,
			$entryField->get_value(),
			$entry->get_form()
		);
		return $entryField;
	}

	/**
	 * Trigger actions and events before entry is saved.
	 *
	 * @since 1.9.0
	 *
	 * @uses "calderaForms/restApi/createEntry/preSave" filter
	 *
	 * @param \Caldera_Forms_Entry $entry Entry object
	 * @param string $sessionId Session ID
	 *
	 * @return \Caldera_Forms_Entry
	 */
	public function subscribeToPreSave(\Caldera_Forms_Entry $entry, $sessionId)
	{
		//caldera_forms_submit_pre_process_start $form, $referrer, $process_id
		/**
		 * Start pre process loop
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		do_action( 'calderaForms/submit/preProcess/start',$entry,$sessionId );
		//caldera_forms_submit_pre_process_end $form, $referrer, $process_id
		/**
		 * Filter entry at end of pre process loop
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		do_action( 'calderaForms/submit/preProcess/end',$entry,$sessionId );
		return $entry;
	}

	/**
	 * Trigger actions and events before entry is saved.
	 *
	 * @since 1.9.0
	 *
	 * @uses "calderaForms/restApi/createEntry/postSave" filter
	 *
	 * @param \Caldera_Forms_Entry $entry Entry object
	 * @param string $sessionId Session ID
	 *
	 * @return \Caldera_Forms_Entry
	 */
	public function subscribeToPostSave(\Caldera_Forms_Entry $entry, $sessionId)
	{
		//caldera_forms_submit_process_start $form, $referrer, $process_id,$entryid
		/**
		 * Start process loop
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		do_action( 'calderaForms/submit/postProcess/start',$entry,$sessionId );
		//caldera_forms_submit_process_end $form, $referrer, $process_id
		/**
		 * After process loop
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		do_action( 'calderaForms/submit/postProcess/end',$entry,$sessionId );
		//caldera_forms_submit_complete $form, $referrer, $process_id, $entryid
		/**
		 * Processing is completed
		 *
		 * @since 1.9.0
		 *
		 * @param \Caldera_Forms_Entry $entry Entry being saved. No entry ID is set yet.
		 * @param string $sessionId ID of session
		 */
		do_action( 'calderaForms/submit/complete',$entry,$sessionId );
		return $entry;
	}
}
