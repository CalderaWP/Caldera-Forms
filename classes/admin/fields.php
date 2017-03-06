<?php

/**
 * Admin UI field generator
 *
 * Most fo the time, should not be used directly, instead use Caldera_Forms_Admin_UI class
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Admin_Fields {

	/**
	 * Array of Caldera_Forms_Admin_Field objects
	 *
	 * @since 1.5.1
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Caldera_Forms_Admin_Fields constructor.
	 *
	 * @since 1.5.1
	 *
	 * @param array $fields Array of Caldera_Forms_Admin_Field objects
	 */
	public function __construct( array $fields = array() ){
		$this->fields = $fields;

	}

	/**
	 * Add a Caldera_Forms_Admin_Field field
	 *
	 * @since 1.5.1
	 *
	 * @param Caldera_Forms_Admin_Field $field
	 */
	public function add_field( Caldera_Forms_Admin_Field  $field ){
		$this->fields[] = $field;
	}

	/**
	 * Reset object
	 *
	 * @since 1.5.1
	 */
	public function reset(){
		$this->fields = array();
	}

	/**
	 * Generate HTML
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function html(){
		$html = '';

		/** @var Caldera_Forms_Admin_Field $field */
		foreach ( $this->fields as $field ) {
			$args = $field->args;
			switch ( $field->type ){
				case 'text':
					$html .= $this->input_group( $field->type, $field->label, $field->name, $args );
					break;
				case 'select' :
					$html .= $this->select_group( $field->label, $field->name, $field->options, $args );
					break;
			}
		}

		return $html;
	}


	/**
	 * Generate HTML for an input group
	 *
	 * @since 1.5.1
	 *
	 * @param string $type For input attribute
	 * @param string $label_text The text of the label
	 * @param string $field_name The field name
	 * @param array $args Additional args
	 *
	 * @return string
	 */
	protected function input_group( $type, $label_text, $field_name, array $args  ){
		$has_description = false;
		$description = $this->description( $field_name,  $args[ 'description'] );
		if( ! empty( $description ) ){
			$has_description = true;
		}

		return sprintf( '
			<div class="caldera-config-group">
				%s
				<div class="caldera-config-field">
					%s
				</div>
				%s
			</div>
		', $this->label( $label_text, $field_name ), $this->input( $type, $field_name, $has_description, $args ), $description );
	}

	/**
	 * Generate HTML for a select group
	 *
	 * @since 1.5.1
	 *
	 * @param string $label_text The text of the label
	 * @param string $field_name The field name
	 * @param array|string $options Array of field options 'value' => 'label' or handlebars markup for dynamic
	 * @param array $args Additional args
	 *
	 * @return string
	 */
	protected function select_group( $label_text, $field_name,  $options, array $args ){
		$description = $this->description( $field_name,  $args[ 'description'] );
		if( is_array( $options ) ){
			$this->select( $options, $field_name, (bool) $description, $args );
		}


		return sprintf( '
			<div class="caldera-config-group">
				%s
				<div class="caldera-config-field">
					%s
				</div>
				%s
			</div>
		',
			$this->label( $label_text, $field_name ),
			$options,
			$description
		);


	}

	protected function checkbox_group( $label_text, array $options, $field_name, array $args ){

		$description = $this->description( $field_name, $args[ 'description' ] );
		return sprintf( '
			<div class="caldera-config-group">
				<fieldset>
					<legend>
						%s
					</legend>
					<div class="caldera-config-field">
						%s
					</div>
					%s
				</fieldset>
			</div>', esc_html( $label_text ), $this->checkboxes( $options, $field_name ), $description
		);
	}

	protected function checkboxes(array $options, $field_name ){
		$out = array();
		foreach ( $options as $option_name => $label ){
			$out[] = $this->checkbox( $field_name, $option_name ) . $this->label( $label, $field_name );
		}

		return implode( "\\n", $out );

	}

	protected function checkbox( $field_name, $option_name ){
		return sprintf( '<input id="{{_id}}_media_library" type="checkbox" class="field-config" name="%s" value="1" {{#if %s}}checked="checked"{{/if}}>', esc_attr(  '{{_id}}_' . $field_name ), esc_attr( '{{_name}}[' . $field_name . ']' ), $option_name );
	}

	/**
	 * Generate HTML for label
	 *
	 * @since 1.5.1
	 *
	 * @param string $label_text The text of the label
	 * @param string $field_name The field name
	 *
	 * @return string
	 */
	protected function label( $label_text, $field_name ){
		return sprintf(
			'<label for="%s">
				%s
			</label>'
		, esc_attr( '{{_id}}_' . $field_name ), esc_html( $label_text ));
	}

	/**
	 * Create input markup
	 *
	 * @since 1.5.1
	 *
	 * @param string $type For input attribute
	 * @param string $field_name Field name
	 * @param bool $has_description
	 * @param array $args Additional args
	 *
	 * @return string
	 */
	protected function input( $type, $field_name, $has_description, $args ){
		$description_aria = $this->decription_aria_tag( $field_name, $has_description );
		$classes = 'field-config';
		if( true == $args[ 'magic'] ){
			$classes .= ' magic-tag-enabled';
		}

		if( true == $args[ 'block'] ){
			$classes .= ' block-input';
		}

		if( ! empty( $args[ 'classes' ] ) ){
			$classes .= ' ' . $args[ 'classes' ];
		}

		$attrs = '';
		if( ! empty( $args[ 'attrs' ] ) ){
			$attrs = caldera_forms_escape_field_attributes_array( $args[ 'attrs' ] );
		}

		return sprintf('<input type="%s" id="%s" class="%s" name="%s" value="%s" data-config-type="%s" %s >',
			esc_attr( $type ),
			esc_attr( '{{_id}}_' . $field_name ),
			esc_attr( $classes ),
			esc_attr( '{{_name}}[' . $field_name . ']'),
			esc_attr( '{{' . $field_name . '}}' ),
			esc_attr( $field_name ),
			$description_aria . ' ' . $attrs
		);

	}

	/**
	 * Get description ID attribute
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name Field Name
	 *
	 * @return string
	 */
	protected function description_id_attr( $field_name ){
		return '{{_id}}_' . $field_name  .'-description';
	}

	/**
	 * Create markup for description
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name The field name
	 * @param string $description The actual description text
	 *
	 * @return string
	 */
	protected function description( $field_name, $description ){
		if ( ! empty( $description ) ) {
			return sprintf( '<p class="description" id="%s">%s</p>', esc_attr( $this->description_id_attr( $field_name ) ), esc_html( $description ) );
		}
		return '';
	}

	/**
	 * Create a select element
	 *
	 * @since 1.5.1
	 *
	 * @param array $options The options
	 * @param string $field_name The field name
	 * @param bool $has_description If field has a description?
	 *
	 * @return string
	 */
	protected function select( $options, $field_name, $has_description, array $args ){
		$classes = 'field-config ' . $field_name . ' ' . $args[ 'classes'];
		if( true == $args[ 'block' ] ){
			$classes .= ' block-input';
		}
		return sprintf( '
			<select class="%s" name="%s" id="%s" %s>
				%s
			</select>',
			esc_attr( $classes ),
			esc_attr(  '{{_id}}-', $field_name ),
			esc_attr( '{{_name}}['. $field_name . ']' ),
			$this->options( $options, $field_name ),
			$description_aria = $this->decription_aria_tag( $field_name, $has_description )
		);
	}

	/**
	 * Create markup for select options
	 *
	 * @since 1.5.1
	 *
	 * @param array $options The options
	 * @param string $field_name The field name
	 *
	 * @return string
	 */
	protected function options( array  $options, $field_name ){
		$out = array();
		$pattern = '<option {{#is %s value="%s"}}selected="selected"{{/is}}value="%s">%s</option>';
		foreach ( $options as $value => $label ){
			$out[] = sprintf( $pattern, $field_name, $value, $value, $label );
		}

		return implode( "\\n", $out );

	}

	/**
	 * Create aria-describedby markup
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name The field name
	 * @param bool $has_description If field has a description?
	 *
	 * @return string
	 */
	protected function decription_aria_tag( $field_name, $has_description ){
		if ( $has_description ) {
			$description_aria = 'aria-describedby="' . $this->description_id_attr( $field_name ) . '"';

			return $description_aria;
		} else {
			$description_aria = '';

			return $description_aria;
		}
	}
}