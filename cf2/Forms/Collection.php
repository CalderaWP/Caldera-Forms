<?php


namespace calderawp\calderaforms\cf2\Forms;


use calderawp\calderaforms\cf2\Exception;

/**
 * Class Collection
 *
 * Form collection implementation WITHOUT database.
 */
class Collection implements FormCollection
{

    /**
     * The forms
     *
     * @since 1.8.10
     *
     * @var array
     */
    protected $forms;

    /**
     * Get all forms of collection
     *
     * @since 1.8.10
     *
     * @return array
     */
    public function getAll()
    {
        return  is_array($this->forms ) ? $this->forms : [];
    }
    /**
     * Add a form to this collection
     *
     * @since 1.8.10
     *
     * @param array $form
     * @return FormCollection
     */
    public function addForm(array $form)
    {
        $this->forms[$form['ID']] = $form;
        return $this;
    }

    /**
     * Get a form from this collection
     *
     * @since 1.8.10
     *
     * @param string $formId
     * @return array
     * @throws Exception
     */
    public function getForm(string $formId)
    {
        if (!$this->hasForm($formId)) {
            throw new Exception("No form with ID $formId found");
        }
        return $this->forms[$formId];
    }

    /**
     * Does collection have a form of this ID?
     *
     * @since 1.8.10
     *
     * @param string $formId
     * @return bool
     */
    public function hasForm( string $formId)
    {
        return array_key_exists($formId, $this->forms);
    }

    /**
     * Remove a form from the collection
     *
     * @since 1.8.10
     *
     * @param string $formId
     * @return FormCollection
     * @throws Exception
     */
    public function removeForm(string $formId)
    {
        if (!$this->hasForm($formId)) {
            throw new Exception("No form with ID $formId found");
        }
        unset($this->forms[$formId]);
        return $this;
    }
}