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

	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @uses "calderaForms/restApi/createEntry/addField" filter
	 *
	 * @param \Caldera_Forms_Entry_Field $entryField
	 * @param \Caldera_Forms_Entry $entry
	 * @param array $fieldConfig
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
}
