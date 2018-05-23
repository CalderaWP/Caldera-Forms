<?php


class Caldera_Forms_Entry_Fields implements \calderawp\CalderaContainers\Interfaces\Arrayable
{

    /**
     * The collected fields
     *
     * @since 1.7.0
     *
     * @var  Caldera_Forms_Entry_Field[] $fields
     */
    protected $fields;
    /**
     * The form config
     *
     * @since 1.7.0
     *
     * @var array
     */
    protected $form;

    /**
     * Caldera_Forms_Entry_Fields constructor.
     * @param array $form Form configuration
     * @param Caldera_Forms_Entry_Field[] $fields
     */
    public function __construct(array  $form, array  $fields = [] )
    {
        $this->form = $form;
        if( ! empty( $fields ) ){
            $this->set_fields_form_array( $fields );
        }
    }

    /** @inheritdoc */
    public function toArray()
    {
        if (empty($this->fields)) {
            return [];
        }
        $fields = [];
        /** @var Caldera_Forms_Entry_Field $field */
        foreach ($this->get_fields() as $field) {
            $fields[$field->field_id] = $field->to_array(false);
        }
        return $fields;
    }

    /**
     * Get the collection of fields
     *
     * @since 1.7.0
     *
     * @return Caldera_Forms_Entry_Field[]
     */
    public function get_fields(){
        return $this->fields;
    }

    /**
     * Check if a field is in collection
     *
     * @since 1.7.0
     *
     * @param string $field_id The field's ID
     * @return bool
     */
    public function has_field( $field_id ){
        return $this->form_has_field($field_id ) && isset( $this->fields[ $field_id ] );
    }

    /**
     * Get total number of field values in collection
     *
     * @since 1.7.0
     *
     * @return int
     */
    public function count(){
        return is_array( $this->fields ) ? count( $this->fields ) : 0;
    }

    /**
     * Add a field to collection
     *
     * @since 1.7.0
     *
     * @param Caldera_Forms_Entry_Field $field
     * @return $this
     */
    public function add_field( Caldera_Forms_Entry_Field $field ){
        if ($this->form_has_field($field->field_id)) {
            $this->fields[$field->entry_id] = $field;
        }
        return $this;
    }

    /**
     * Get a field from collection
     *
     * @since 1.7.0
     *
     * @param string $field_id Field ID (form config, not DB id column)
     * @return Caldera_Forms_Entry_Field
     * @throws Exception
     */
    public function get_field( $field_id ){
        if( $this->has_field( $field_id ) ){
            return $this->fields[ $field_id ];
        }
        throw new Exception( __( 'Field Not Found', 'caldera-forms' ) );
    }


    /**
     * Populate fields property from an array
     *
     * @since 1.7.0
     *
     * @param Caldera_Forms_Entry_Field[] $fields  Entry field objects to add
     */
    protected function set_fields_form_array($fields){
        foreach ( $fields as $field ){
            if( ! is_a( $field, Caldera_Forms_Entry_Field::class ) ){
                $field = $this->cast_to_field($field);
            }
            $this->add_field( $field );
        }
    }

    /**
     * Convert array to Caldera_Forms_Entry_Field
     *
     * @since 1.7.0
     *
     * @param array|object $field Field value
     * @return Caldera_Forms_Entry_Field
     */
    protected function cast_to_field($field){
        return new Caldera_Forms_Entry_Field((object)$field);
    }

    /**
     * Check if form has a field
     *
     * @since 1.7.0
     *
     * @param string $field_id Field ID (form config, not DB id column)
     * @return bool
     */
    protected function form_has_field($field_id){
        return ! empty( $this->form[ 'fields' ] ) && array_key_exists( $field_id, $this->form[ 'fields']  );
    }

}