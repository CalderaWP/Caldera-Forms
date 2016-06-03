<?php

/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Caldera_Forms_Mail_Data {

	protected $mail = array(
		'recipients'  => array(),
		'headers' => array(),
		'subject' => array(),
		'message' => ''
	);

	protected $form;

	protected $entry_id;

	protected $data;

	protected $headers;

	protected $recipients;

	public function __construct( array  $form, $entry_id, array $data ) {
		$this->form = $form;
		$this->entry_id = $entry_id;
		$this->data = $data;

	}

	public function get_mail(){
		return $this->mail;
	}

	public function get_recipients(){
		return $this->recipients;
	}

	public function add_header( $header, $value ){
		$this->mail->headers[] = sprintf( '% : %s', $header, $value );
		$this->headers[ $header ] = $value;
	}

	public function get_header( $header ){
		if( isset( $this->headers[ $header ] ) ){
			return $this->headers[ $header ];
		}
	}

	protected function create_mail(){

		$this->set_sender();
		$this->set_summary();

		$this->mail[ 'subject' ] = Caldera_Forms::do_magic_tags( $this->form[ 'mailer' ][ 'email_subject' ] );


		if ( isset( $this->form[ 'mailer' ][ 'bcc_to' ] ) ) {
			$this->add_header( 'Bcc', Caldera_Forms::do_magic_tags( $this->form[ 'mailer' ][ 'bcc_to' ] ) );
		}

		if ( isset( $this->form[ 'mailer' ][ 'reply_to' ] ) ) {
			$reply_to = trim( $this->form[ 'mailer' ][ 'reply_to' ] );
			if ( ! empty( $reply_to ) ) {
				$reply_to = Caldera_Forms::do_magic_tags( '<' . $reply_to . '>' );
			}else{
				$reply_to = get_option( 'admin_email' );
			}

			$this->add_header( 'Reply-To', $reply_to );
		}

		if ( ! empty( $this->form[ 'mailer' ][ 'recipients' ] ) ) {
			$this->recipients = Caldera_Forms::do_magic_tags( $this->form[ 'mailer' ][ 'recipients' ] );
			$this->mail[ 'recipients' ] = explode( ',', $this->recipients );
		} else {
			$this->mail[ 'recipients' ][] = $this->recipients = get_option( 'admin_email' );

		}


		list( $submission, $labels ) = $this->create_message();
		$this->create_csv( $labels, $submission );


		if ( empty( $this->mail ) ) {
			return;
		}

	}

	protected function set_sender() {
		$sendername = esc_html__( 'Caldera Forms Notification', 'caldera-forms' );
		if ( ! empty( $this->form[ 'mailer' ][ 'sender_name' ] ) ) {
			$sendername = $this->form[ 'mailer' ][ 'sender_name' ];
			if ( false !== strpos( $sendername, '%' ) ) {
				$isname = Caldera_Forms::get_slug_data( trim( $sendername, '%' ), $this->form );
				if ( ! empty( $isname ) ) {
					$sendername = $isname;
				}
			}

		}


		if ( empty( $this->form[ 'mailer' ][ 'sender_email' ] ) ) {
			$sendermail = get_option( 'admin_email' );
		} else {
			$sendermail = $this->form[ 'mailer' ][ 'sender_email' ];
			if ( false !== strpos( $sendermail, '%' ) ) {
				$ismail = Caldera_Forms::get_slug_data( trim( $sendermail, '%' ), $this->form );
				if ( is_email( $ismail ) ) {
					$sendermail = $ismail;
				}
			}
		}

		$this->add_header( 'From', $sendername . ' <' . $sendermail . '>' );
	}

	protected function set_summary() {
// use summary
		if ( empty( $this->form[ 'mailer' ][ 'email_message' ] ) ) {
			$this->form[ 'mailer' ][ 'email_message' ] = "{summary}";
		}

		if ( ! isset( $this->form[ 'mailer' ][ 'email_subject' ] ) ) {
			$this->form[ 'mailer' ][ 'email_subject' ] = $this->form[ 'name' ];
		}

		$this->mail->message = stripslashes( $this->form[ 'mailer' ][ 'email_message' ] ) . "\r\n";
	}

	/**
	 * @return array
	 */
	protected function create_message(  ) {
		$this->mail[ 'message' ] = Caldera_Forms::do_magic_tags( $this->mail[ 'message' ] );

		if ( ! isset( $this->form[ 'mailer' ][ 'email_type' ] ) || $this->form[ 'mailer' ][ 'email_type' ] == 'html' ) {
			$this->add_header( 'Content-type', 'text/html' );
			$this->mail[ 'message' ] = wpautop( $this->mail[ 'message' ] );
		}

		// get tags
		preg_match_all( "/%(.+?)%/", $this->mail[ 'message' ], $hastags );
		if ( ! empty( $hastags[ 1 ] ) ) {
			foreach ( $hastags[ 1 ] as $tag_key => $tag ) {
				$tagval = Caldera_Forms::get_slug_data( $tag, $this->form );
				if ( is_array( $tagval ) ) {
					$tagval = implode( ', ', $tagval );
				}
				$this->mail[ 'message' ] = str_replace( $hastags[ 0 ][ $tag_key ], $tagval, $this->mail[ 'message' ] );
			}
		}

		// ifs
		preg_match_all( "/\[if (.+?)?\](?:(.+?)?\[\/if\])?/", $this->mail[ 'message' ], $hasifs );
		if ( ! empty( $hasifs[ 1 ] ) ) {
			// process ifs
			foreach ( $hasifs[ 0 ] as $if_key => $if_tag ) {

				$content = explode( '[else]', $hasifs[ 2 ][ $if_key ] );
				if ( empty( $content[ 1 ] ) ) {
					$content[ 1 ] = '';
				}
				$vars = shortcode_parse_atts( $hasifs[ 1 ][ $if_key ] );
				foreach ( $vars as $varkey => $varval ) {
					if ( is_string( $varkey ) ) {
						$var = Caldera_Forms::get_slug_data( $varkey, $this->form );
						if ( in_array( $varval, (array) $var ) ) {
							// yes show code
							$this->mail[ 'message' ] = str_replace( $hasifs[ 0 ][ $if_key ], $content[ 0 ], $this->mail[ 'message' ] );
						} else {
							// nope- no code
							$this->mail[ 'message' ] = str_replace( $hasifs[ 0 ][ $if_key ], $content[ 1 ], $this->mail[ 'message' ] );
						}
					} else {
						$var = Caldera_Forms::get_slug_data( $varval, $this->form );
						if ( ! empty( $var ) ) {
							// show code
							$this->mail[ 'message' ] = str_replace( $hasifs[ 0 ][ $if_key ], $content[ 0 ], $this->mail[ 'message' ] );
						} else {
							// no code
							$this->mail[ 'message' ] = str_replace( $hasifs[ 0 ][ $if_key ], $content[ 1 ], $this->mail[ 'message' ] );
						}
					}
				}
			}

		}


		$submission = array();
		foreach ( $this->data as $field_id => $row ) {
			if ( $row === null || ! isset( $this->form[ 'fields' ][ $field_id ] ) ) {
				continue;
			}

			$key = $this->form[ 'fields' ][ $field_id ][ 'slug' ];
			if ( is_array( $row ) ) {
				if ( ! empty( $row ) ) {
					$keys = array_keys( $row );
					if ( is_int( $keys[ 0 ] ) ) {
						$row = implode( ', ', $row );
					} else {
						$tmp = array();
						foreach ( $row as $linekey => $item ) {
							if ( is_array( $item ) ) {
								$item = '( ' . implode( ', ', $item ) . ' )';
							}
							$tmp[] = $linekey . ': ' . $item;
						}
						$row = implode( ', ', $tmp );
					}
				} else {
					$row = null;
				}
			}
			$this->mail[ 'message' ] = str_replace( '%' . $key . '%', $row, $this->mail[ 'message' ] );
			$this->mail[ 'subject' ] = str_replace( '%' . $key . '%', $row, $this->mail[ 'subject' ] );

			$submission[] = $row;
			$labels[]     = $this->form[ 'fields' ][ $field_id ][ 'label' ];
		}

		// final magic
		$this->mail[ 'message' ] = Caldera_Forms::do_magic_tags( $this->mail[ 'message' ] );
		$this->mail[ 'subject' ] = Caldera_Forms::do_magic_tags( $this->mail[ 'subject' ] );

		return array( $submission, $labels );
	}

	/**
	 * @param $labels
	 * @param $submission
	 */
	protected function create_csv( $labels, $submission ) {
// CSV
		if ( ! empty( $this->form[ 'mailer' ][ 'csv_data' ] ) ) {
			ob_start();
			$df = fopen( "php://output", 'w' );
			fputcsv( $df, $labels );
			fputcsv( $df, $submission );
			fclose( $df );
			$csv     = ob_get_clean();
			$csvfile = wp_upload_bits( uniqid() . '.csv', null, $csv );
			if ( isset( $csvfile[ 'file' ] ) && false == $csvfile[ 'error' ] && file_exists( $csvfile[ 'file' ] ) ) {
				$this->mail[ 'attachments' ][] = $csvfile[ 'file' ];
			}
		}
	}


}
