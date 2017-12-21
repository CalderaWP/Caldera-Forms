<?php


namespace calderawp\calderaforms\pro\api;
use calderawp\calderaforms\pro\attachments\attachments;
use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\exceptions\Exception;
use calderawp\calderaforms\pro\repository;


/**
 * Class message
 *
 * Representation of message in format API client likes
 *
 * @package calderawp\calderaforms\pro\api
 */
class message extends repository {

	/**
	 * API client
	 *
	 * @since 0.0.1
	 *
	 * @var client
	 */
	protected $client;

	/**
	 * Properties of messages
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $properites = [
		'layout',
		'pdf',
		'pdf_layout',
		'to',
		'reply',
		'cc',
		'bcc',
		'content',
		'subject',
		'entry_data',
		'entry_id',
		'files',
		'attachments'
	];

	/**
	 * Magic setter
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Property name
	 * @param mixed $value Value to set
	 */
	public function __set( $name, $value )
	{

		if( $this->allowed_key( $name )){
			$this->set( $name, $value );
		}
	}

	/**
	 * Create on remote API
	 *
	 * @since 0.0.1
	 *
	 * @param bool $send Send message now or delay?
	 * @param int $entry_id Local entry ID
	 *
	 * @return array|\WP_Error
	 */
	public function create( $send, $entry_id ){
		if( ! $this->client ){
			$this->client = new client( container::get_instance()->get_settings()->get_api_keys() );
		}


		return $this->client->create_message( $this, $send, $entry_id );

	}

	/**
	 * @inheritdoc
	 * @return message
	 * @throws Exception
	 */
	public function set( $key, $value ){
		if( $this->allowed_key( $key ) ){
			if( in_array( $key, array(
				'to',
				'reply',
				'cc',
				'bcc'
			)) ){
				throw new Exception( 'Must use add_recpient for to/reply/cc/bcc');

 			}

 			if( in_array( $key, [ 'attachments' ] ) ){
			    throw new Exception( 'Must use add__attachment for attachments');
		    }

			$this->items[ $key ] = $value;
		}

		return $this;

	}

	/**
	 * Add a to, reply, cc or bcc to object
	 *
	 * @since 0.1.1
	 *
	 * @param string $type Type to add
	 * @param string $email Email addresss
	 * @param string $name Optional. Name
	 *
	 * @return $this
	 */
	public function add_recipient( $type, $email, $name = '' ){
		if(  ! $this->allowed_key( $type ) ){
			return $this;
		}

		if( 'reply' == $type ){
			$this->items[ $type ] = [
				'email' => sanitize_email( $email ),
				'name' => $name
			];
		}else{
			if( empty( $this->items[ $type ] ) ){
				$this->items[ $type ] = [];
			}
			$this->items[ $type ][] = [
				'email' => sanitize_email( $email ),
				'name' => $name
			];
		}


		return $this;
	}

	/**
	 * Can this key be set
	 *
	 * @since 0.0.1
	 *
	 * @param string $key Key to check
	 *
	 * @return bool
	 */
	protected function allowed_key( $key ){
		return in_array( $key, $this->properites );
	}

	/** @inheritdoc */
	public function to_array(){
		$array = array();
		foreach ( $this->properites as $prop  ){
			if ( $this->has( $prop ) ) {
				$array[ $prop ] = $this->get( $prop );
			}else{
				$array[ $prop ] = false;
			}
		}


		return $array;
	}


	/**
	 * Add entry data in the correct forms
	 *
	 * @since 0.3.0
	 *
	 * @param int $entry_id ID of entry
	 * @param array $form Form Config
	 */
	public function add_entry_data( $entry_id, $form ){
		$e = new \Caldera_Forms_Entry( $form, $entry_id );
		$data = $e->get_entry()->to_array( false );
		$data[ 'fields' ] = [];

		$fields = $e->get_fields();
		if( ! empty( $fields ) ){
			/** @var \Caldera_Forms_Entry_Field $field */
			foreach ( $fields as $field ){
				$data[ 'fields' ][ $field->field_id ] = $field->to_array( false );
			}
		}


		$this->items[ 'entry_data' ] = $data;

	}

	/**
	 * Add an attachment
	 *
	 * @since 0.9.0
	 *
	 * @param $path
	 *
	 * @return message;
	 */
	public function add_attachment( $path )
	{
		if( ! file_exists( $path ) ){

		}else{
			$this->items[ 'attachments' ][] = esc_url_raw( caldera_forms_pro_file_request_url( $path ) );
		}

		return $this;

	}

}