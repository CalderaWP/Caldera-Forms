<?php


/**
 * Class Caldera_Forms_Field_Utm
 */
class Caldera_Forms_Field_Utm {

	public static function add_hooks(){
		/** Filters for UTM fields */
		add_filter( 'caldera_forms_field_attributes-utm', array( 'Caldera_Forms_Field_Utm', 'handle_attrs'), 1 );
		add_filter( 'caldera_forms_save_field_utm', array( 'Caldera_Forms_Field_Utm', 'handle_save' ), 10, 4 );
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
		$count = Caldera_Forms_Render_Util::get_current_form_count();
		$attrs[ 'type' ] = 'hidden';
		$attrs[ 'name' ] = str_replace( $count . '_', '', $attrs[ 'id' ] );

		return $attrs;
	}

	public static function view( $field_value, $field, $form ){
		$out = array();
		$pattern = '<li><strong>%s</strong>: %s</li>';
		foreach ( self::tags() as $tag ){
			if( ! empty( $field_value[ $tag ] ) ){
				$out[] = sprintf( $pattern, ucwords( $tag ), esc_html( $field_value[ $tag ] ) );
			}

		}

		if (  ! empty( $out ) ) {
			$field_value = '<ul>' . implode( ' ', $out ) . '</ul>';
		}

		return $field_value;
	}

	public static function handle_save(  $new_data, $field, $form, $entry_id ){
		foreach ( self::tags() as $tag ){
			$utm_field = self::config( $field, $tag );
			$value = '';
			if( isset( $_POST[ $utm_field[ 'ID' ] ] ) ){
				$value    = self::find_in_post( $utm_field );

			}

			$data = array(
				'entry_id' => $entry_id,
				'field_id' => $field[ 'ID' ],
				'slug'     => $field[ 'slug' ],
				'value'    =>  $value
			);

			Caldera_Forms_Entry_Field::insert( $data );

		}

		return $new_data;
	}

	public static function handler( $value, $field, $form ){
		$value = array();

		foreach ( self::tags() as $tag ){
			$utm_field = self::config( $field, $tag );
			$_value    = self::find_in_post( $utm_field );
			$value[ $tag ] = $_value;
		}

		return $value;

	}



	public static function config( $field, $tag ){
		$tag = 'utm_' . $tag;
		$utm_field_config = $field;
		$utm_field_config[ 'slug' ] = $field[ 'slug' ] . '_' . $tag;
		$utm_field_config[ 'ID' ] = $field[ 'ID' ] . '_' . $tag;
		$utm_field_config[ 'label' ] = $field[ 'label' ] . ': ' . $tag;
		$utm_field_config[ 'config' ][ 'default' ] = ( isset( $_GET[ $tag ] ) ?  Caldera_Forms_Sanitize::sanitize( $_GET[ $tag ] ) : '' );

		return $utm_field_config;
	}

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
	 * @param $utm_field
	 * @param $count
	 *
	 * @return array|mixed|object|string|void
	 */
	protected static function find_in_post( $utm_field ){
		$count = Caldera_Forms_Render_Util::get_current_form_count();
		$_value = '';
		$tag_key = $utm_field[ 'ID' ] . '_' . $count;
		if ( isset( $_POST[ $tag_key ] ) ) {
			$_value = Caldera_Forms_Sanitize::sanitize( $_POST[ $tag_key ] );
		}

		return $_value;
	}


}