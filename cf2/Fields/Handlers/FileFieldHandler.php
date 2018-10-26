<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;



class FileFieldHandler extends FieldHandler
{

    /**
     * Tracks transients used
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $transientsUsed;

    /**
     * Process field entry (pre-save)
     *
     * @since 1.8.0
     *
     * @param mixed $entry Current value
     * @param array $field Field config
     * @param array $form Form config
     * @return mixed
     */
    public function processField($entry, $field, $form)
    {
        if (is_null($entry)) {
            return $entry;
        }
        if (is_string($entry)) {
            $cached = $this->
            container
                ->getTransientsApi()
                ->getTransient($entry);
            $this->transientsUsed[$this->key($field, $form)] = $entry;
            if ($cached !== $entry) {
                return $cached;
            }
        }
        return $entry;
    }

    /**
     * Prepare to save field
     *
     * @since 1.8.0
     *
     * @param mixed $entry Current value
     * @param array $field Field config
     * @param array $form Form config
     * @param int $entry_id The ID of the entry being saved
     * @return mixed
     */
    public function saveField($entry, $field, $form, $entry_id)
    {
        $key = $this->key($field, $form);
        if (isset($this->transientsUsed[$key])) {
            $this->container
                ->getTransientsApi()
                ->deleteTransient($this->transientsUsed[$key]);
            unset( $this->transientsUsed[$key]);
        }
        return $entry;
    }

    /**
     * Create the key for the array $this->transientsUsed
     *
     * @since 1.8.0
     *
     * @param array $field Field config
     * @param array $form Form config
     * @return string
     */
    protected function key($field, $form)
    {
        return $field['ID'] . $form['ID'];
    }
}