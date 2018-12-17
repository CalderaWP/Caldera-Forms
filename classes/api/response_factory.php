<?php


/**
 * Class Caldera_Forms_API_Response_Factory
 */
class Caldera_Forms_API_Response_Factory {

    /**
     * @return Caldera_Forms_API_Error
     */
    public static function error_form_not_found(){
		return new Caldera_Forms_API_Error( 'form-not-found', __( 'Form not found', 'caldera-forms' ) );
	}

    /**
     * @since 1.8.0
     *
     * @return Caldera_Forms_API_Error
     */
    public static function error_form_not_created(){
        return new Caldera_Forms_API_Error( 'form-not-created', __( 'Form not created', 'caldera-forms' ) );
    }

    /**
     * @return Caldera_Forms_API_Error
     */
    public static function error_entry_not_found(){
		return new Caldera_Forms_API_Error( 'form-entry-not-found', __( 'Form entry not found', 'caldera-forms' ) );
	}

    /**
     * @param $data
     * @param null $total
     * @param bool $total_pages
     * @return Caldera_Forms_API_Response
     */
    public static function entry_data($data, $total = null, $total_pages = false ){
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