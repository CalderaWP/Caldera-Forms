<?php

/**
 * Created by PhpStorm.
 * User: josh
 * Date: 10/1/16
 * Time: 4:57 PM
 */
class Caldera_Forms_API_Response_Factory {

	public static function error_form_not_found(){
		return new Caldera_Forms_API_Error( 'form-not-found', __( 'Form not found', 'caldera-forms' ) );
	}

	public static function error_entry_not_found(){
		return new Caldera_Forms_API_Error( 'form-entry-not-found', __( 'Form entry not found', 'caldera-forms' ) );
	}

	public static function entry_data( $data, $total = null, $total_pages = false ){
		if( null === $total ){
			$total = count( $data );
		}

		$response =  new Caldera_Forms_API_Response( $data, 200, array() );
		$response->set_total_header( $total );
		if ( is_numeric( $total_pages ) ) {
			$response->set_total_pages_header( $total_pages );
		}

		return $response;
	}

}