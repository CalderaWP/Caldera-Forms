<?php


class Caldera_Forms_Admin_Panel {


	/**
	 * Get config for a form admin panel by name
	 *
	 * @since 1.5.0
	 *
	 * @param string $panel Admin panel name
	 *
	 * @return bool|array Panel config if foudn or false
	 */
	public static function get_panel( $panel ){
		$panels = self::get_panels();
		if( array_key_exists( strtolower( $panel ), $panels[ 'form_layout' ][ 'tabs' ] ) ){
			return $panels[ 'form_layout' ][ 'tabs' ][ $panel ];
		}
		return false;
	}

	/**
	 * Get panel HTML
	 *
	 * @since 1.5.0
	 *
	 * @param array $panel Panel config
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function panel_html( array $panel, array  $form ){
		ob_start();
		$element = Caldera_Forms_Forms::get_form( $form );
		include $panel[ 'canvas' ];
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Get form editor admin panel setup
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function get_panels(){

		/**
		 * Filter form editor admin panel setups
		 *
		 * @since unknown
		 *
		 * @param array $panels Panel config
		 */
		return apply_filters( 'caldera_forms_get_panel_extensions', array() );

	}

}