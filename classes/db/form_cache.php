<?php


class Caldera_Forms_DB_Form_Cache implements Caldera_Forms_DB_Form_Interface
{

    protected $db;

    /**
     * @var array
     */
    protected $cache;
    public function __construct( Caldera_Forms_DB_Form_Interface $db)
    {
        $this->db = $db;
        $this->cache = [];
    }

    public function get_all($primary = true)
    {
        // TODO: Implement get_all() method.
    }

    public function get_by_form_id($form_id, $primary_only = true)
    {
        // TODO: Implement get_by_form_id() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    public function delete_by_form_id($form_id)
    {
        // TODO: Implement delete_by_form_id() method.
    }

    public function delete($ids)
    {
        // TODO: Implement delete() method.
    }
}