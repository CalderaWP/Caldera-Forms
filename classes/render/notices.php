<?php

/**
 * Prepare notice content and classes sent tp browser
 *
 * Wrapper for multi-location filters
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Notices {

	public static function get_note_general_classes( array $form ){
		/**
		 * Filter notices to be returned to browser
		 *
		 * @since unknown
		 *
		 * @param array $classes Classes to use
		 * @param array $form Form config
		 */
		return apply_filters( 'caldera_forms_render_note_general_classes', array(
			'alert'
		), $form );
	}

	/**
	 * Prepare notices to retrun to browser
	 *
	 * @since 1.5.0
	 *
	 * @param array $notices Notices to display, by type
	 * @param array $form Form config
	 *
	 * @return array
	 */
	public static function prepare_notices( array  $notices = array(), array  $form ){
		/**
		 * Filter final HTML for notices
		 *
		 * @since unknown
		 *
		 * @param string $notice Notices HTML
		 * @param array $config Form config
		 */
		return apply_filters( 'caldera_forms_render_notices', $notices, $form);
	}

	/**
	 * Get, with filter, notification classes to use for errors/success messages, etc.
	 *
	 * @since 1.5.0
	 * @param array $note_general_classes
	 * @param array $form
	 *
	 * @return array
	 */
	public static function get_note_classes( array $note_general_classes, array  $form ){
		$note_classes = array(
			'success'	=> array_merge($note_general_classes, array(
				'alert-success'
			)),
			'error'	=> array_merge($note_general_classes, array(
				'alert-error'
			)),
			'info'	=> array_merge($note_general_classes, array(
				'alert-info'
			)),
			'warning'	=> array_merge($note_general_classes, array(
				'alert-warning'
			)),
			'danger'	=> array_merge($note_general_classes, array(
				'alert-danger'
			)),
		);

		/**
		 * Filter notice classes
		 *
		 * @since unkown
		 *
		 * @param array $note_classes Note classes to return
		 * @param array $form Form config
		 */
		return apply_filters( 'caldera_forms_render_note_classes', $note_classes, $form);


	}

	/**
	 * Create HTML string from notices
	 *
	 * @since 1.5.0
	 *
	 * @param array $notices Notices to display
	 * @param array $note_classes Notices classes to use
	 *
	 * @return string
	 */
	public static function html_from_notices( array $notices, array $note_classes ){
		$html = '';
		foreach($notices as $note_type => $notice){
			if(!empty($notice['note'])){
				$result = Caldera_Forms::do_magic_tags( $notice['note'] );
				$html .= '<div class=" '. implode(' ', $note_classes[$note_type]) . '">' . $result .'</div>';
			}
		}

		return $html;
	}
}