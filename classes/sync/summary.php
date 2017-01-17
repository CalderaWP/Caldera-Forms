<?php

/**
 * Magic tag sync implementation for summary fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Sync_Summary extends Caldera_Forms_Sync_HTML {


	/**
	 * @inheritdoc
	 */
	protected function initial_set_default() {
		$this->default = $this->create_summary();
	}

	/**
	 * Create summary content
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function create_summary(){

		/**
		 * Element type to wrap Caldera Forms summary fields in
		 *
		 * @since 1.5.0
		 *
		 * @param string $type Element type ONLY. For example 'div' not '<div>'.
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		$summary_wrap = apply_filters( 'caldera_forms_summary_field_list_wrap', 'ul', $this->field, $this->form );

		/**
		 * Sprintf pattern for summary field items
		 *
		 * MUST have 2 %s
		 *
		 * @since 1.5.0
		 *
		 * @param string $summary_pattern The sprintf pattern for each item. With 2 %s -- no more, no less or errors will happen
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		$summary_pattern = apply_filters( 'caldera_forms_summary_field_pattern', '<li><span class="caldera-forms-summary-label">%s</span> <span class="caldera-forms-summary-value">%s</span></li>', $this->field, $this->form );

		$summary = '<' . $summary_wrap .' >';
		foreach ( Caldera_Forms_Forms::get_fields( $this->form, true ) as $_field ){
			if( $_field[ 'ID' ] == $this->field[ 'ID' ] || true === $this->dont_sync( Caldera_Forms_Field_Util::get_type( $_field, $this->form ) ) )  {
				continue;
			}

			$magic_tag = '%' . $_field[ 'slug' ] . '%';
			$summary .= sprintf( $summary_pattern, esc_html( $_field[ 'label' ] ), $magic_tag );
		}

		$summary .= '</' . $summary_wrap . '>';
		return $summary;
	}

	/**
	 * Check if type of field is a type NOT to sync to
	 *
	 * @since 1.5.0
	 *
	 * @param string $type Field type
	 *
	 * @return bool True if syncing is disallowed
	 */
	protected function dont_sync( $type ){
		if( in_array( $type, array(
			'button',
			'summary',
			'html',
			'section_break'
		))){
			return true;
		}

		return false;
	}
}