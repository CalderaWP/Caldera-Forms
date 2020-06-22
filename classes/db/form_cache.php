<?php

/**
 * Class Caldera_Forms_DB_Form_Cache
 *
 * A proxy for \Caldera_Forms_DB_Form that acts as cache
 */
class Caldera_Forms_DB_Form_Cache implements Caldera_Forms_DB_Form_Interface
{

    protected $db;

    /**
     * @var array
     */
    protected $cache;

    /**
     * Caldera_Forms_DB_Form_Cache constructor.
     * @param Caldera_Forms_DB_Form_Interface $db
     */
    public function __construct(Caldera_Forms_DB_Form_Interface $db)
    {
        $this->db = $db;
        $this->cache = [];
    }

    /** @inheritDoc */
    public function __call($name, $arguments)
    {
        //Method not implemented here? Proxy to actual database API
        if (method_exists($this->db, $name)) {
            return call_user_func([$this->db, $name], $arguments);
        }
    }

    /** @inheritDoc */
    public function get_all($primary = true)
    {
        if (!$primary) {
            return $this->db->get_all(false);
        }

        if (empty($this->cache)) {
            $this->cache = $this->db->get_all($primary);
        }
        return $this->cache;


    }

    /** @inheritDoc */
    public function get_by_form_id($form_id, $primary_only = true)
    {
        if (!$primary_only) {
            return $this->db->get_by_form_id($form_id, false);
        }
        return $this->get($form_id);
    }

    /** @inheritDoc */
    public function create(array $data)
    {
        $this->cache[$data['form_id']] = $data;
        return $this->db->create($data);

    }

    /** @inheritDoc */
    public function update(array $data)
    {
        $this->cache[$data['form_id']] = $data;
        return $this->db->update($data);
    }

    /** @inheritDoc */
    public function delete_by_form_id($form_id)
    {
        unset($this->cache[$form_id]);

        return $this->db->delete_by_form_id($form_id);

    }

    /** @inheritDoc */
    public function delete($ids)
    {
        $this->db->delete($ids);
    }

    /**
     * Does cache contain a form?
     *
     * @param string $id Form ID
     * @return bool
     * @since 1.9.1
     *
     */
    protected function has($id)
    {
        return !empty($this->cache) && isset($this->cache[$id]);
    }

    /**
     * Get form from cache or database
     *
     * @param string $id Form ID
     * @return array|bool
     * @since 1.9.1
     *
     */
    protected function get($id)
    {
        if ($this->has($id)) {
            return $this->cache[$id];
        }
        $form = $this->db->get_by_form_id($id);
        if ($form) {
            $this->cache[$id] = $form;
        }
        return $form;
    }


}