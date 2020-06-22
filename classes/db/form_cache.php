<?php


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

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
       if( method_exists( $this->db, $name ) ){
           return call_user_func([$this->db,$name],$arguments);
       }
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
        $this->cache[$data['form_id']] = $data;
        return $this->db->create($data);

    }

    public function update(array $data)
    {
        $this->cache[$data['form_id']] = $data;
        return $this->db->update($data);
    }

    public function delete_by_form_id($form_id)
    {
        unset($this->cache[$form_id]);

      return $this->db->delete_by_form_id($form_id );

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