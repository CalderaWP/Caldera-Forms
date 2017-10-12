<?php


/**
 * Class Caldera_Forms_Field_Utm
 */
class Caldera_Forms_Field_Utm {

	/**
	 * Add hooks for UTM fields
	 *
	 * @since 1.5.2
	 */
	public static function add_hooks(){
		add_filter( 'caldera_forms_field_attributes-utm', array( 'Caldera_Forms_Field_Utm', 'handle_attrs'), 1 );
		add_filter( 'caldera_forms_view_field_utm', array( 'Caldera_Forms_Field_Utm', 'view' ), 10, 3 );

	}

	/**
	 * Set the type HTML attribute to hidden for UTM fields
	 *
	 * @since 1.5.2
	 *
	 * @uses "caldera_forms_field_attributes-utm" filter
	 *
	 * @param array $attrs Field attributes
	 *
	 * @return array
	 */
	public static function handle_attrs( $attrs ){
		$count = self::get_count();
		$attrs[ 'type' ] = 'hidden';
		$attrs[ 'name' ] = str_replace( $count . '_', '', $attrs[ 'id' ] );

		return $attrs;
	}

	/**
	 * Format field view for utm fields
	 *
	 * @since 1.5.2
	 *
	 * @uses "caldera_forms_view_field_utm" filter
	 *
	 * @param mixed $field_value Saved value
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function view( $field_value, $field, $form ){
		if( ! is_array( $field_value ) ){
			return $field_value;
		}

		$out = array();
		$pattern = '<li><strong>%s</strong>: %s</li>';
		foreach ( self::tags() as $tag ){
			if( ! empty( $field_value[ $tag ] ) ){
				$out[] = sprintf( $pattern, ucwords( $tag ), esc_html( $field_value[ $tag ] ) );
			}

		}

		if (  ! empty( $out ) ) {
			$field_value = '<ul class="caldera-forms-utm-field-view">' . implode( ' ', $out ) . '</ul>';
		}

		return $field_value;
	}

	/**
	 * Save handler for UTM tag field
	 *
	 * @since 1.5.2
	 *
	 * @param mixed $value Saved value
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return array
	 */
	public static function handler( $value, $field, $form ){
		$_value = array();

		foreach ( self::tags() as $tag ){
			$utm_field = self::config( $field, $tag );
			$__value    = self::find_in_post( $utm_field );
			if (  ! empty( $__value ) ) {
				$_value[ $tag ] = $__value;
			}
		}

		if( ! empty( $_value ) ){
			$value = $_value;
		}

		return $value;

	}


	/**
	 * Creates config for individual tag fields
	 *
	 * @since 1.5.2
	 *
	 * @param array $field Field config
	 * @param string $tag Which tag
	 *
	 * @return array
	 */
	public static function config( $field, $tag ){
		$tag = 'utm_' . $tag;
		$utm_field_config = $field;
		$utm_field_config[ 'slug' ] = $field[ 'slug' ] . '_' . $tag;
		$utm_field_config[ 'ID' ] = $field[ 'ID' ] . '_' . $tag;
		$utm_field_config[ 'label' ] = $field[ 'label' ] . ': ' . $tag;
		$utm_field_config[ 'config' ][ 'default' ] = ( isset( $_GET[ $tag ] ) ?  Caldera_Forms_Sanitize::sanitize( $_GET[ $tag ] ) : '' );

		return $utm_field_config;
	}

	/**
	 * Array of types of UTM tags
	 *
	 * @since 1.5.2
	 *
	 * @return array
	 */
	public static function tags(){
		return  array(
			'source',
			'medium',
			'campaign',
			'term',
			'content',
		);
	}

	/**
	 * Find in POST data
	 *
	 * @since 1.5.2
	 *
	 * @param array $utm_field Field config
	 *
	 * @return string
	 */
	protected static function find_in_post( $utm_field ){
		$_value = '';
		$tag_key = $utm_field[ 'ID' ] . '_' . self::get_count();
		if ( isset( $_POST[ $tag_key ] ) ) {
			$_value = Caldera_Forms_Sanitize::sanitize( $_POST[ $tag_key ] );
		}

		if( ! is_string( $_value ) ){
			return '';
		}

		return $_value;
	}

	/**
	 * Get current form count
	 *
	 * @since 1.5.2
	 *
	 * @return int
	 */
	protected static function get_count(){
		$count = Caldera_Forms_Render_Util::get_current_form_count();
		if ( 1 > $count ) {
			$count = 1;

			return $count;
		}

		return $count;
	}


}