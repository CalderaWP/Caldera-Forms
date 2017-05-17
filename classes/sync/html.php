<?php
/**
 * Magic tag sync implementation for HTML fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Sync_HTML  extends Caldera_Forms_Sync_Sync {

	/**
	 * Bind field list as field data-field attrs
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $attr_binds;

	/**
	 * Quoted bind field list
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $bind_fields;

	/**
	 * Prefix for template and content ID attributes
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $template_prefix = 'html-content-';

	/**
	 * Get the date-field formatted binds
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_attr_binds(){
		if ( is_array( $this->attr_binds ) ) {
			return $this->attr_binds;
		} else {
			return array();
		}
	}

	/**
	 * Get ID of the template
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function template_id(){
		return $this->template_prefix . $this->field_base_id . '-tmpl';
	}

	/**
	 * Get ID of the content attribute
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function content_id(){
		return $this->template_prefix . $this->field_base_id;
	}

	/**
	 * Get the quoted binds list
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_bind_fields(){
		if( is_array( $this->bind_fields ) ){
			return $this->bind_fields;
		}else{
			return array();
		}
	}

	/**
	 * @inheritdoc
	 * @since 1.5.0
	 */
	protected function add_bind( $key_id ) {
		$this->binds[] = $key_id;
		$type = Caldera_Forms_Field_Util::get_type( $key_id, $this->form );
		if( 'calculation' == $type ){
			$this->attr_binds[] = '[data-calc-field="'.$key_id . '_' . $this->current_form_count.'"]';

		}else{
			$this->attr_binds[] = '[data-field="'.$key_id.'"]';
		}

		$this->bind_fields[] = '"'.$key_id.'"';
	}




}