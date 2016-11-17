<?php

/**
 * Created by PhpStorm.
 * User: josh
 * Date: 11/16/16
 * Time: 7:16 PM
 */
class Caldera_Forms_Magic_Get {


	public static function all( $form ){
		/**
		 * Filter which Magic Tags are available in the form editor
		 *
		 *
		 * @since 1.3.2
		 *
		 * @param array $tags Array of magic registered tags
		 * @param array $form_id for which this applies.
		 */
		return = apply_filters( 'caldera_forms_get_magic_tags', array(), $form['ID'] );

	}
}