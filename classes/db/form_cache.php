<?php


class Caldera_Forms_DB_Form_Cache implements Caldera_Forms_DB_Form_Interface
{

    protected $db;

    /**
     * @var array
     */
    protected $cache;

    public function __construct(Caldera_Forms_DB_Form_Interface $db)
    {
        $this->db = $db;
        $this->cache = [];
    }

    public function get_all($primary = true)
    {
        if( ! $primary ){
            return $this->db->get_all(false);
        }

       if( empty($this->cache )){
           $this->cache = $this->db->get_all($primary);
       }
        return $this->cache;


    }

    public function get_by_form_id($form_id, $primary_only = true)
    {
        if (!$primary_only) {
            return $this->db->get_by_form_id($form_id, false);
        }
        return $this->get($form_id);
    }

    public function create(array $data)
    {

        return $this->db->create($data);

    }

    public function update(array $data)
    {
        return $this->db->update($data);
    }

    public function delete_by_form_id($form_id)
    {
        $deleted = $this->db->delete_by_form_id($form_id );
        if( $deleted && $this->has($form_id)){
            unset($this->cache[$form_id]);
        }
        return $deleted;
    }

    public function delete($ids)
    {
        $this->db->delete($ids);
    }

    protected function has($id)
    {
        return !empty($this->cache) && isset($this->cache[$id]);
    }

    protected function get($id)
    {
        if( $this->has($id)){
            return $this->cache[$id];
        }
        $form = $this->db->get_by_form_id($id);
        if( $form ){
            $this->cache[$id] = $form;
        }
        return $form;
    }


}