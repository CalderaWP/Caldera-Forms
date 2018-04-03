<?php
trait Caldera_Forms_Has_Data {
	use Caldera_Forms_Has_Mock_Form;
	/**
	 * Create a mock entry to test
	 *
	 * @since 1.6.0
	 * @since 1.4.0 In Caldera_Forms_Test_Case
	 */
	protected function create_entry( array $form = null ){
		if ( ! $form ) {
			$form = $this->mock_form;
		}
		$data = array();
		$i = 0;
		foreach( $form[ 'fields' ] as $field_id => $field_config ){
			if ( 1 == $i ) {
				$data[ $field_id ] = $field_id . '_' . rand();
			} else {
				$data[ $field_id ] = array(
					rand(),
					5 => rand(), rand(), 'batman'
				);
			}
			if( 0 == $i ){
				$i = 1;
			}else{
				$i = 0;
			}
		}

		$entry_id = Caldera_Forms_Save_Final::create_entry( $form, $data  );
		return array(
			'id' => $entry_id,
			'field_data' => $data,
			'form_id' => $form[ 'ID' ],
		);
	}

	/**
	 * Create submission data for mock submissions.
	 *
	 * Designed to be used to set $_POST for contact form tests or other mock submission requiring tests.
	 *
	 * @since 1.6.0
	 * @since 1.5.9 In Caldera_Forms_Test_Case
	 *
	 * @param null|string $form_id
	 * @param array $data
	 * @return array
	 */
	protected function submission_data( $form_id = null, array $data = array() ){
		if( ! $form_id ){
			$form_id = $this->mock_form_id;
		}

		$nonce = Caldera_Forms_Render_Nonce::create_verify_nonce( $form_id );

		$data = wp_parse_args( $data, array (
			'_cf_verify' => $nonce,
			'_wp_http_referer' => '/?page_id=4&preview=1&cf_preview=' . $form_id,
			'_cf_frm_id' => $form_id,
			'_cf_frm_ct' => '1',
			'cfajax' => $form_id,
			'_cf_cr_pst' => '4',
			'email' => '',
			'formId' => $form_id,
			'instance' => '1',
			'request' => site_url("/cf-api/$form_id"),
			'postDisable' => '0',
			'target' => '#caldera_notices_1',
			'loadClass' => 'cf_processing',
			'loadElement' => '_parent',
			'hiderows' => 'true',
			'action' => 'cf_process_ajax_submit',
			'template' => "#cfajax_$form_id-tmpl",
		) );

		return $data;
	}
}