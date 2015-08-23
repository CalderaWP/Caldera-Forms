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
class Caldera_Forms_Processor_UI {

	/**
	 * Output field markup form an array fo field args
	 *
	 * @since 1.3.0
	 *
	 * @param array $fields Array of args to pass to self::config_field()
	 * @param null|string|int Optional. If null, all fields (except those with print args set to false will be printed). Can be used to print a group of fields based on the value of the print arg.
	 *
	 * @return bool|string The Markup or false if invalid.
	 */
	public static function config_fields( $fields, $print_group = null ) {
		$out = '';
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			foreach( $fields as $args ) {
				if ( ! empty( $args ) ) {
					if ( isset( $args[ 'print' ] ) && false ==  $args[ 'print' ] ){
						continue;
					}

					if ( ! is_null( $print_group ) ) {
						if ( isset( $args[ 'print' ]  ) || $print_group != $args[ 'print' ] ) {
							continue;

						}

					}

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
	 *     @type string $type Field type. Default is "text". Options: simple|checkbox|advanced|dropdown
	 *     @type array|string $extra_class Additional classes to apply.
	 *     @type string $desc Extra description to add to markup.
	 *     @type bool|string|array $allow_types Type(s) of fields that are allowed to bind to this or false to allow all.
	 *     @type bool|string|array $exclude Type(s) of fields that are NOT allowed to bind to this or false to not exclude any.
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
			'extra_classes' => array(),
			'required' => false,
			'desc' => false,
			'allow_types' => false,
			'exclude' => false,
			'print' => true
		);

		$args = wp_parse_args( $args, $defaults );


		/**
		 * Filter arguments for field markup
		 *
		 * @since 1.3.0
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'caldera_forms_addon_config_field_args', $args );

		$input_type = 'simple';

		if ( 'checkbox' == $args[ 'type'] ) {
			$args[ 'block' ] = false;
			$args[ 'magic' ] = false;
			$input_type = 'checkbox';
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
		$id = trim( $args['id'] );

		$desc = false;
		if ( $args[ 'desc' ] ) {
			$desc = sprintf( '<p class="description">%1s</p>', esc_html( $args[ 'desc' ] ) );
		}

		$allow_types = '';
		if ( $args[ 'allow_types' ] ) {
			$input_type = 'advanced';
		}

		$required = $args[ 'required' ];
		if ( $required ) {
			$required = 'required';
		}

		$field = sprintf( '
		<div class="caldera-config-group">
			<label for="%1s">
				%2s
			</label>
			<div class="caldera-config-field">
				%3s
			</div>
			%4s
		</div>',
			esc_attr( $id ),
			$args[ 'label' ],
			self::input( $input_type, $args, $id, $classes, $required ),
			$desc
		);

		return $field;

	}

	/**
	 * Create the input for proccesor config field.
	 *
	 * @since 1.3.0
	 *
	 * @param string $type The type of input. This is NOT The input type. Options are simple|checkbox|advanced|dropdown
	 * @param array $args Field args
	 * @param string $id ID attribute
	 * @param string $classes Class attribute.
	 * @param bool|string $required If is required or not
	 *
	 * @return string HTML markup for input
	 */
	public static function input( $type, $args, $id, $classes, $required ) {
		switch( $type ) {
			case 'checkbox' == $type :
				$field = sprintf( '<input type="%1s" class="%2s" id="%3s" name="{{_name}}[%4s]" %5s>',
					$args[ 'type' ],
					$classes,
					esc_attr( $id ),
					esc_attr( $id ),
					sprintf( '{{#if %s}}checked{{/if}}', esc_attr( $id ) )
				);
				break;
			case 'advanced' :
				if ( $required ) {
					$required = "true";
				}else{
					$required = "false";
				}

				if ( is_string( $args[ 'allow_types' ] ) ) {
					$allow_types = $args[ 'allow_types' ] ;
				}elseif ( is_array( $args[ 'allow_types' ] ) ){
					$allow_types = implode( ',', $args[ 'allow_types' ] );
				}else{
					$allow_types = 'all';
				}

				if ( is_string( $args[ 'exclude' ] ) ) {
					$excludes = $args[ 'exclude' ] ;
				}elseif ( is_array( $args[ 'exclude' ] ) ){
					$excludes = implode( ',', $args[ 'exclude' ] );
				}else{
					$excludes = 'all';
				}



				$field = sprintf( '{{{_field slug="%1s" type="%2s" exclude="%3s" required="%4s"}}}',
					esc_attr( $id ),
					$allow_types,
					$excludes,
					$required
				);
			break;
			case 'dropdown' :
				if ( isset( $args[ 'options' ] ) ){
					foreach( $args[ 'options' ] as $value => $label ) {
						$options[] = sprintf( '<option value="%1s" {{#is %2s value="%3s"}}selected{{/is}}>%4s</option>',
							esc_attr( $value ),
							esc_attr( $id ),
							esc_attr( $value ),
							esc_html( $label )
						);
					}

					$field = sprintf( '<select class="%1s" id="%2s" name="{{_name}}[%3s]" >%4s,</select>',
						$classes,
						esc_attr( $id ),
						esc_attr( $id ),
						implode( "/n", $options )
					);
				}
				break;
			default :
				$field = sprintf( '<input type="%1s" class="%2s" id="%3s" name="{{_name}}[%4s]" value="%5s" %6s>',
					$args[ 'type' ],
					$classes,
					esc_attr( $id ),
					esc_attr( $id ),
					'{{' . esc_attr( $id ) . '}}',
					$required
				);
			break;
		}

		return $field;

	}

	/**
	 * Helper function to place a notice in processor config, if SSL required and not in use.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Name of add-on.
	 *
	 * @return string
	 */
	public static function ssl_notice( $name ) {
		if ( is_ssl() ) {
			return;
		}

		return sprintf( '<div class="error"><p>%1s</p></div>', __( sprintf( '%1s requires a valid SSL certificate. It will only function in test mode unless SSL/HTTPS is used. Your site does not appear to be using the secure HTTPS protocol.', $name ), 'caldera-forms' ) );

	}


}
