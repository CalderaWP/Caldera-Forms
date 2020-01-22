<?php


namespace calderawp\calderaforms\cf2\Forms;


use calderawp\calderaforms\cf2\Exception;

/**
 * Interface FormCollection
 *
 * Describes a collection of saved forms
 */
interface FormCollection
{
    /**
     * Get all forms of collection
     *
     * @return array
     * @since 1.8.10
     *
     */
    public function getAll();

    /**
     * Add a form to this collection
     *
     * @param array $form
     * @return FormCollection
     * @since 1.8.10
     *
     */
    public function addForm(array $form);

    /**
     * Get a form from this collection
     *
     * @param string $formId
     * @return array
     * @throws Exception
     * @since 1.8.10
     *
     */
    public function getForm(string $formId);

    /**
     * Does collection have a form of this ID?
     *
     * @param string $formId
     * @return bool
     * @since 1.8.10
     *
     */
    public function hasForm(string $formId);

    /**
     * Remove a form from the collection
     *
     * @param string $formId
     * @return FormCollection
     * @throws Exception
     * @since 1.8.10
     *
     */
    public function removeForm(string $formId);
}