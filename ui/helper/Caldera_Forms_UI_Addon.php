<?php
/**
 * Create markup for add-on config fields.
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */
class Caldera_Forms_UI_Addon {

	/**
	 * Output field markup form an array fo field args
	 *
	 * @since 1.2.4
	 *
	 * @param array $fields Array of args to pass to self::config_field()
	 *
	 * @return bool|string The Markup or false if invalid.
	 */
	public static function config_fields( $fields ) {
		$out = '';
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			foreach( $fields as $args ) {
				if ( ! empty( $args ) ) {
					$out .= self::config_field( $args );
				}

			}

		}

 		return $out;
	}

	/**
	 * Create markup for a processor config field.
	 *
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $id Field ID (REQUIRED)
	 *     @type string $label Label for field (REQUIRED)
	 *     @type bool $magic If field is magic tag enabled. Default is true.
	 *     @type bool $block Use block style, Default is true
	 *     @type string $type Field type. Default is "text"
	 *     @type array|string $extra_class Additional classes to apply.
	 *     @type string $desc Extra description to add to markup.
	 * }
	 *
	 * @return string|void HTML markup if input is valid. Void if not.
  	 */
	public static function config_field( $args ) {
		if ( ! is_array( $args ) || ! isset( $args[ 'label' ] ) || ! isset( $args[ 'id' ] ) ) {
			return;

		}

		$defaults = array(
			'label' => '',
			'magic' => true,
			'block' => true,
			'type'  => 'text',
			'extra_class' => array(),
			'required' => false,
			'desc' => false,
			'allow_types' => false,
		);

		$args = wp_parse_args( $args, $defaults );


		/**
		 * Filter arguments for field markup
		 *
		 * @since 1.2.4
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'caldera_forms_addon_config_field_args', $args );

		if ( 'checkbox' == $args[ 'type'] ) {
			$args[ 'block' ] = false;
			$args[ 'magic' ] = false;
		}

		if ( is_string( $args[ 'extra_classes']) ) {
			$args[ 'extra_classes' ] = array( $args[ 'extra_classes' ] );
		}

		if( $args[ 'block' ] ) {
			$args[ 'extra_classes'][] = 'block-input';
		}

		if ( $args[ 'magic'] ) {
			$args[ 'extra_classes' ][] = 'magic-tag-enabled';
		}

		if ( $args[ 'required' ] ) {
			$args[ 'extra_classes' ][] = 'required';
		}

		$args[ 'extra_classes' ][] = 'field-config';

		$classes = implode( ' ', $args[ 'extra_classes' ] );
		$id = $args[ 'id' ];

		$desc = false;
		if ( $args[ 'desc' ] ) {
			$desc = sprintf( '<p class="description">%1s</p>', esc_html( $args[ 'desc' ] ) );
		}

		$allow_types = '';
		if ( $args[ 'allow_types' ] ) {
			if ( is_string( $args[ 'allow_type' ] ) ) {
				$allow_types = sprintf( 'allow="%1s"', esc_attr( $args[ 'allow_types' ] ) );
			}elseif ( is_array( $args[ 'allow_types' ] ) ){
				$allow_types = sprintf( 'allow="%1s"', implode( ',', $args[ 'allow_types' ] ) );
			}else{
				$allow_types = '';
			}
		}

		$field = sprintf( '
		<div class="caldera-config-group">
			<label for="%1s">
				%2s
			</label>
			<div class="caldera-config-field">
				<input type="%3s" class="%4s" id="%5s" name="{{_name}}[%6s]" value="%7s" %8s >
			</div>
			%9s
		</div>',
			esc_attr( $id ),
			$args[ 'label' ],
			$args[ 'type' ],
			$classes,
			esc_attr( $id ),
			esc_attr( $id ),
			'{{' . esc_attr( $id ) . '}}',
			$allow_types,
			$desc
		);



		return $field;

	}


}
