<?php
abstract class Caldera_Forms_Entry_Object {
	public function __set( $property, $value ){
		if( method_exists( $this, $property . '_set'  ) ){
			return call_user_func( $property . '_set', $value );
		}elseif (  ( is_string( $value ) ||  is_numeric( $value ) ) && property_exists( $this, $property ) ){
			$this->$property = $value;
			return true;
		}else{
			return false;
		}

	}

	protected function apply_filter( $property, $value ){
		return apply_filters( "caldera_forms_entry_{$property}" , $value, $this );
	}



	public function __get( $property ){
		if( property_exists( $this, $property ) ){
			return $this->apply_filter( $property,  $this->$property );
		}else{
			return null;
		}
	}


	public function to_array(){
		$vars = get_object_vars(  $this );
		$vars = array_filter( $vars, function ( $var ) {
			return ! is_null( $var );
		} );

		return $vars;
	}
}
