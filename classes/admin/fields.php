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
		$arg_fields = array(
			'description' => '',
			'block' => false,
			'magic' => false,
		);
		/** @var Caldera_Forms_Admin_Field $field */
		foreach ( $this->fields as $field ) {
			$args = wp_parse_args( $field->args, $arg_fields );
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
		', $this->label( $label_text, $field_name ), $this->input( $type, $field_name, $has_description, $args[ 'block' ], $args[ 'magic'] ), $description );
	}

	/**
	 * Generate HTML for a select group
	 *
	 * @since 1.5.1
	 *
	 * @param string $label_text The text of the label
	 * @param string $field_name The field name
	 * @param array $options Field options 'value' => 'label'
	 * @param array $args Additional args
	 *
	 * @return string
	 */
	protected function select_group( $label_text, $field_name, array  $options, array $args ){
		$description = $this->description( $field_name,  $args[ 'description'] );


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
			$this->select( $options, $field_name, (bool) $description ),
			$description
		);


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
	 * @param bool $block Is block input?
	 * @param bool $magic Is magic tag enabled?
	 *
	 * @return string
	 */
	protected function input( $type, $field_name, $has_description, $block, $magic ){
		$description_aria = $this->decription_aria_tag( $field_name, $has_description );
		$classes = 'field-config';
		if( $magic ){
			$classes .= ' magic-tag-enabled';
		}

		if( $block ){
			$classes .= ' block-input';
		}

		return sprintf('<input type="%s" id="%s" class="%s" name="%s" value="%s" data-config-type="%s" %s >',
			esc_attr( $type ),
			esc_attr( '{{_id}}_' . $field_name ),
			esc_attr( $classes ),
			esc_attr( '{{_name}}[' . $field_name . ']'),
			esc_attr( '{{' . $field_name . '}}' ),
			esc_attr( $field_name ),
			$description_aria
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
			return sprintf( '<p class="description" id="%s">%s</p>', esc_attr( $this->description_id_attr( $field_name ), esc_html( $description ) ) );
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
	protected function select( $options, $field_name, $has_description ){
		return sprintf( '
			<select class="%s" name="%s" id="%s" %s>
				%s
			</select>',
			esc_attr( 'field-config ' . $field_name . '_type_override' ),
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