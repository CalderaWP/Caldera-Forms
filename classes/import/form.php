<?php

/**
 * Class Caldera_Forms_Import_Form
 *
 * Prepares forms for importing
 *
 * @since 1.6.0
 */
class Caldera_Forms_Import_Form{

    /**
     * Form configuration
     *
     * @since 1.6.0
     *
     * @var array
     */
    protected $form;

    /**
     * Params for `Caldera_Forms_Sanitize::sanitize`
     *
     * @since 1.6.0
     *
     * @var array
     */
    protected $sanitize_params;

    /**
     * Has form been prepared yet?
     *
     * @since 1.6.0
     *
     * @var bool
     */
    protected $prepared;

    /**
     * Caldera_Forms_Import_Form constructor.
     *
     * @since 1.6.0
     *
     * @param array $form Form config
     * @param bool $trusted Optional. Should form be trusted? Default is false, do not trust.
     */
    public function __construct(array $form = [], $trusted = false ) {
        $this->prepared = false;
        $this->form = $form;
        $this->set_sanitize_params($trusted);
    }

    /**
     * Get prepared form
     *
     * Will cause form to be prepared, if not already completed.
     *
     * @since 1.6.0
     *
     * @return array
     */
    public function get_prepared_form(){
        if (! $this->prepared ) {
            $this->prepare_form();
        }
        return $this->form;
    }



    /**
     * Run all form preparation steps
     *
     * @since 1.6.0
     */
    protected function prepare_form(){
        $this->normalize_indexes();
        $this->sanitize_fields();
        $this->prepared = true;
    }

    /**
     * Sanitize all fields
     *
     * @since 1.6.0
     */
    protected function sanitize_fields(){
        if( ! empty( $this->form[ 'fields' ] ) ){
            foreach ( $this->form[ 'fields' ] as $field_id => &$field ){
                $field = $this->sanitize_field( $field );
            }
        }
    }

    /**
     * Sanitize one field
     *
     * @since 1.6.0
     *
     * @param array $field Field config
     * @return array
     */
    protected function sanitize_field( array  $field ){
        $field[ 'slug' ] = $this->sanitize_slug(  $field[ 'slug' ] );
        foreach ( array(
            'description',
            'caption'
                  ) as $index ){
            $field = $this->sanitize_by_index( $index, $field );
        }

        foreach (array(
            'default',
            'placeholder'
                 ) as $config_index) {
            if( isset( $field[ 'config' ][ $config_index ] ) ){
                $field[ 'config' ] = $this->sanitize_by_index( $config_index, $field[ 'config' ] );
            }
        }

        if( ! empty( $field[ 'config'][ 'option' ] ) && is_array( $field[ 'config'][ 'option' ] ) ){
            foreach ( $field[ 'config'][ 'option' ] as $option_id => $option ){
                foreach ( array(
                    'value',
                    'label',
                          ) as $index ){
                    $field[ 'config'][ 'option' ][ $option_id ] = $this->sanitize_by_index($index,  $field[ 'config'][ 'option' ][ $option_id ] );
                }
                $index = 'calc_value';
                $field[ 'config'][ 'option' ][ $option_id ][ $index ] = intval($this->sanitize_by_index($index,  $field[ 'config'][ 'option' ][ $option_id ]  ) );

            }
        }

        return $field;
    }

    /**
     * Sanitize one key of field config array
     *
     * @since 1.6.0
     *
     * @param string $index Index to sanitize
     * @param array $field Field config
     * @return array
     */
    protected function sanitize_by_index( $index, array $field ){
        if( isset( $field[ $index ] ) ){
            $field[ $index ] = Caldera_Forms_Sanitize::sanitize( $field[ $index ], $this->sanitize_params );
        }
        return $field;
    }


    /**
     * Remove all but lowercase alphanumeric charecters
     *
     * @since 1.6.0
     *
     * @param string $slug Slug to clean
     * @return string
     */
    protected function sanitize_slug($slug){
        return strtolower(preg_replace( "/[^a-zA-Z0-9]+/", "_", $slug));
    }

    /**
     * Makes sure form has all normal indexes
     *
     * @since 1.6.0
     */
    protected function normalize_indexes(){
        $this->form = wp_parse_args( $this->form, $this->get_defaults() );
    }

    /**
     * Get the default values for form configs
     *
     * @since 1.6.0
     *
     * @return array
     */
    public function get_defaults(){
        return array(
            'ID' => Caldera_Forms_Forms::create_unique_form_id(),
            'cf_version' => CFCORE_VER,
            'success' => 'Form has been successfully submitted. Thank you.',
            'db_support' => 1,
            'pinned' => 0,
            'hide_form' => 1,
            'check_honey' => 1,
            'avatar_field' => '',
            'form_ajax' => 1,
            'layout_grid' => array(
                'fields' => array(),
                'structure' => ''
            ),
            'fields' => array(),
            'conditional_groups' => array(),
            'page_names'=> array(),
            'settings' => array(
                'responsive' => array(
                    'break_point' => 'sm'
                )
            ),
            'processors' => array(),
            'name' => '',
            'mailer' => array(
                'on_insert' => 1
            ),
            'hidden' => 0,
            'form_draft' => 0,
            'type' => 'primary'
        );
    }

    /**
     * Set the sanitize_params property based on if input is trusted or not
     *
     * @since 1.6.0
     *
     * @param bool $trusted Is input trusted? If true, tags will not be stripped, if false all tags, including scripts are stripped.
     */
    protected function set_sanitize_params($trusted){
        if (false === $trusted) {
            $this->sanitize_params = array(
                'strip_tags' => true,
                'strip_scripts' => true,
            );
        } else {
            $this->sanitize_params = array(
                'strip_scripts' => true,
            );
        }
    }
}