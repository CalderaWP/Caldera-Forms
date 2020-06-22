<?php

/**
 * Interface Caldera_Forms_DB_Form_Interface
 *
 * Describes the public API of the database API for form configuration in CF 1.x
 */
interface Caldera_Forms_DB_Form_Interface
{

    /**
     * Get all forms
     *
     * @param bool $primary Optional. If true, primary forms are returned, if false revisions. Default is true
     * @return array|bool Array of found forms or false if none found.
     * @since 1.9.1
     *
     */
    public function get_all($primary = true);

    /**
     * Get a form -- or a collection of form revisions by form ID
     *
     * @param string $form_id Form ID
     * @param bool $primary_only Optional. If only primary form should be returned.
     *
     * @return array|bool
     * @since 1.9.1
     *
     */
    public function get_by_form_id($form_id, $primary_only = true);

    /**
     * Create new entry
     *
     * @param array $data
     *
     * @return bool|int|null
     * @since 1.9.1
     *
     */
    public function create(array $data);

    /**
     * Update saved
     *
     * @param array $data
     *
     * @return false|int
     * @since 1.9.1
     *
     */
    public function update(array $data);


    /**
     * Delete all forms, including revisions, by form ID
     *
     * @param string $form_id Form ID
     *
     * @return bool
     * @since 1.9.1
     *
     */
    public function delete_by_form_id($form_id);

    /**
     * Delete one or more form configs, including revisions by id(s)
     *
     * @param int|array $ids Id or array of IDs -- DB id not form ID
     *
     * @return bool|false|int
     * @since 1.9.1
     *
     */
    public function delete($ids);


}