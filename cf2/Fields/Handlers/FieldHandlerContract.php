<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


interface FieldHandlerContract
{

    /**
     * Process field entry (pre-save)
     *
     * @since 1.8.0
     *
     * @uses "caldera_forms_process_field_$fieldType" filter
     *
     * @param mixed $entry Current value
     * @param array $field Field config
     * @param array $form Form config
     * @return mixed
     */
    public function processField($entry, $field, $form);

    /**
     * Prepare to save field
     *
     * @since 1.8.0
     *
     * @uses "caldera_forms_save_field_$fieldType" filter
     *
     * @param mixed $entry Current value
     * @param array $field Field config
     * @param array $form Form config
     * @param int $entry_id The ID of the entry being saved
     * @return mixed
     */
    public function saveField($entry, $field, $form, $entry_id);

    /**
     * Prepare field for entry viewer
     *
     * @uses "caldera_forms_view_field_$FieldType" filter
     * @return mixed
     *
     * @param mixed $field_value Saved field value
     * @param array $field Field config
     * @param array $form Form config
     * @return mixed
     */
    public function viewField($field_value, $field, $form);

}
