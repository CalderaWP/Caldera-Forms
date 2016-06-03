<?php

class Caldera_Forms_Mail_Keys {


	protected $keys;

	protected $key_types = array(
		'sendgrid',
		'caldera'
	);

	protected $option;

	protected static $instance;

	protected function __construct(){
		$this->get_saved();
	}

	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;

	}

	protected function get_saved(){
		$this->keys = get_option( $this->option, array() );
		if( ! is_array( $this->keys ) ){
			$this->keys = array();
		}
	}

	public function add_key( $key, $type, $save = true  ){
		if( $this->allowed_type( $type ) ){
			$this->keys[ $type ] = $key;
		}

		if( $save ){
			$this->save();
		}
	}

	public function get_key( $type ){
		if( isset( $this->keys[ $type ]  ) ){
			return $this->keys[ $type ];
		}
	}

	public function save(){
		if( is_array( $this->keys   ) ){
			return update_option( $this->option, $this->keys  );
		}

	}

	protected function allowed_type( $type ){
		return in_array( $type, $this->key_types  );
	}

}
