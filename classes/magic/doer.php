<?php
/**
 * This class is the doer of magic -- parses magic tags
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Magic_Doer {

	/**
	 * Holds entry details prepared using self::magic_tag_meta_prepare()
	 *
	 * @since 1.5.0.6
	 *
	 * @var array
	 */
	protected static $entry_details;


	/**
	 * Parse field magic tags
	 *
	 * @since 1.5.0
	 *
	 * @param string $value Value that MIGHT need transformed
	 * @param int $entry_id Entry ID
	 * @param array $form Form config Field config
	 *
	 * @return mixed
	 */
	public static function do_field_magic( $value, $entry_id, $form ) {
		$matches = Caldera_Forms_Magic_Util::explode_field_magic( $value );

		$form = self::filter_form( $form, $entry_id );

		if ( ! empty( $matches[ 1 ] ) ) {
			if( ! is_array( $form  ) ){
				global  $form;
			}

			/**
			 * Early entry point for custom parsing of field magic tags
			 *
			 * Return a non-null value to prevent default parsing
			 *
			 * @since 1.5.0
			 *
			 * @param null $_value Value to return
			 * @param string|mixed $value Value being substituted.
			 * @param array $matches Result of preg_match
			 * @param int $entry_id Current entry ID
			 * @param array $form Current form ID
			 */
			$_value = apply_filters( 'caldera_forms_pre_do_field_magic', null, $value, $matches, $entry_id, $form );
			if( ! is_null( $_value ) ){
				return $_value;
			}

			foreach ( $matches[ 1 ] as $key => $tag ) {
				// check for parts
				$part_tags = explode( ':', $tag );
				if ( ! empty( $part_tags[ 1 ] ) ) {
					$tag = $part_tags[ 0 ];
				}
				$entry = Caldera_Forms::get_slug_data( $tag, $form, $entry_id );

				$field = false;
				if ( $entry !== null ) {
					$field = Caldera_Forms_Field_Util::get_field_by_slug( $tag, $form );
				}

				if( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
					$_value = self::magic_image( $field, $entry, $form );
					if( false !== $_value ){
						$value = $_value;
					}

					continue;


				}


				if( is_string( $entry ) ){
					if( ! empty( $field ) && ! empty( $part_tags[ 1 ] ) && $part_tags[ 1 ] == 'label' ) {
						$_entry = json_decode( $entry );
						if( is_object( $_entry ) ){
							$entry = $_entry;
						}
					}else{
						$entry = self::maybe_implode_opts( $entry );

					}
				}

				if ( ! empty( $field ) && ! empty( $part_tags[ 1 ] ) && $part_tags[ 1 ] == 'label' ) {
					if ( ! is_array( $entry ) ) {
						$entry = (array) $entry;
					}

					foreach ( (array) $entry as $entry_key => $entry_line ) {
						if ( ! empty( $field[ 'config' ][ 'option' ] ) ) {
							foreach ( $field[ 'config' ][ 'option' ] as $option ) {
								if ( $option[ 'value' ] == $entry_line ) {
									$entry[ $entry_key ] = $option[ 'label' ];
								}
							}
						}
					}
				}

				if ( is_array( $entry ) ) {

					if ( count( $entry ) === 1 ) {
						$entry = array_shift( $entry );
					} elseif ( count( $entry ) === 2 ) {
						$entry = implode( ', ', $entry );
					} elseif ( count( $entry ) > 2 ) {
						$last  = array_pop( $entry );
						$entry = implode( ', ', $entry ) . ', ' . $last;
					} else {
						$entry = null;
					}
				}

				$value = str_replace( $matches[ 0 ][ $key ], $entry, $value );
			}

			/**
			 * Change value of parse field magic tag
			 *
			 * @since 1.5.0
			 *
			 * @param string|mixed $value Value after parsing.
			 * @param array $matches Result of preg_match
			 * @param int $entry_id Current entry ID
			 * @param array $form Current form ID
			 */
			return apply_filters( 'caldera_forms_do_field_magic_value', $value, $matches, $entry_id, $form );


		}

		return $value;


	}


	/**
	 * Handles bracket type magic tags
	 *
	 * @since 1.5.0
	 *
	 * @param string $value Value to attempt to parse
	 * @param array $form Form config
	 * @param int $entry_id Entry ID
	 * @param array $magic_caller
	 * @param string $referrer
	 *
	 * @return string|void
	 */
	public static function do_bracket_magic( $value, $form, $entry_id, $magic_caller, $referrer ){
		global $processed_meta;

		/**
		 * 
		 */
		$form   = self::filter_form( $form, $entry_id );
		$magics = Caldera_Forms_Magic_Util::explode_bracket_magic( $value );
		if ( ! empty( $magics[ 1 ] ) ) {

			/**
			 * Early entry point for custom parsing of bracket magic tags
			 *
			 * Return a non-null value to prevent default parsing
			 *
			 * @since 1.5.0
			 *
			 * @param null $_value Value to return
			 * @param string|mixed $value Value being substituted.
			 * @param array $magics Result of preg_match
			 * @param int $entry_id Current entry ID
			 * @param array $form Current form ID
			 */
			$_value = apply_filters( 'caldera_forms_pre_do_bracket_magic', null, $value, $magics, $entry_id, $form );
			if( ! is_null( $_value ) ){
				return $_value;
			}

			foreach ( $magics[ 1 ] as $magic_key => $magic_tag ) {

				$magic = explode( ':', $magic_tag, 2 );

				if ( count( $magic ) == 2 ) {
					switch ( strtolower( $magic[ 0 ] ) ) {
						case 'get':
							if ( isset( $_GET[ $magic[ 1 ] ] ) ) {
								$magic_tag = Caldera_Forms_Sanitize::sanitize( $_GET[ $magic[ 1 ] ] );
							} else {
								// check on referer.
								if ( isset( $referrer[ 'query' ][ $magic[ 1 ] ] ) ) {
									$magic_tag = $referrer[ 'query' ][ $magic[ 1 ] ];
								} else {
									$magic_tag = null;
								}
							}
							break;
						case 'post':
							if ( isset( $_POST[ $magic[ 1 ] ] ) ) {
								$magic_tag = Caldera_Forms_Sanitize::sanitize( $_POST[ $magic[ 1 ] ] );
							} else {
								$magic_tag = null;
							}
							break;
						case 'request':
							if ( isset( $_REQUEST[ $magic[ 1 ] ] ) ) {
								$magic_tag = Caldera_Forms_Sanitize::sanitize( $_REQUEST[ $magic[ 1 ] ] );
							} else {
								$magic_tag = null;
							}
							break;
						case 'variable':
							if ( ! empty( $form[ 'variables' ][ 'keys' ] ) ) {
								foreach ( $form[ 'variables' ][ 'keys' ] as $var_index => $var_key ) {
									if ( $var_key == $magic[ 1 ] ) {
										if ( ! in_array( $magic_tag, $magic_caller ) ) {
											$magic_caller[] = $magic_tag;
											$magic_tag      = Caldera_Forms::do_magic_tags( $form[ 'variables' ][ 'values' ][ $var_index ], $entry_id, $magic_caller );
										} else {
											$magic_tag = $form[ 'variables' ][ 'values' ][ $var_index ];
										}
									}
								}
							}
							break;
						case 'date':
							$magic_tag = get_date_from_gmt( date( 'Y-m-d H:i:s' ), $magic[ 1 ] );
							break;
						case 'user':
							if ( is_user_logged_in() ) {
								$user = get_userdata( get_current_user_id() );
								if ( isset( $user->data->{$magic[ 1 ]} ) ) {
									$magic_tag = $user->data->{$magic[ 1 ]};
								} else {
									if ( strtolower( $magic[ 1 ] ) == 'id' ) {
										$magic_tag = $user->ID;
									} else {
										$magic_tag = get_user_meta( $user->ID, $magic[ 1 ], true );
									}
								}
							} else {
								$magic_tag = null;
							}
							break;
						case 'embed_post':
							global $post;

							if ( is_object( $post ) ) {
								if ( isset( $post->{$magic[ 1 ]} ) ) {
									$magic_tag = $post->{$magic[ 1 ]};
								} else {

									// extra post data
									switch ( $magic[ 1 ] ) {
										case 'permalink':
											$magic_tag = get_permalink( $post->ID );
											break;

									}

								}
							} else {
								$magic_tag = null;
							}
							break;
						case 'post_meta':
							global $post;

							if ( is_object( $post ) ) {
								$post_metavalue = get_post_meta( $post->ID, $magic[ 1 ] );
								if ( false !== strpos( $magic[ 1 ], ':' ) ) {
									$magic[ 3 ] = explode( ':', $magic[ 1 ] );
								}
								if ( empty( $post_metavalue ) ) {
									$magic_tag = null;
								} else {
									if ( empty( $magic[ 3 ] ) ) {
										$magic_tag = implode( ', ', $post_metavalue );
									} else {
										$outmagic = array();
										foreach ( $magic[ 3 ] as $subkey => $subvalue ) {
											foreach ( (array) $post_metavalue as $subsubkey => $subsubval ) {
												if ( isset( $subsubval[ $subvalue ] ) ) {
													$outmagic[] = $post_metavalue;
												}
											}
										}
										$magic_tag = implode( ', ', $outmagic );
									}
								}
							} else {
								$magic_tag = null;
							}
							break;
						case 'query_var' :
							$magic_tag = get_query_var($magic[ 1 ]);
							break;
					}
				} else {
					switch ( $magic_tag ) {
						case 'entry_id':
							$magic_tag = Caldera_Forms::get_field_data( '_entry_id', $form );
							if ( $magic_tag === null ) {
								// check if theres an entry
								if ( ! empty( $_GET[ 'cf_ee' ] ) ) {
									$entry = Caldera_Forms::get_entry_detail( $_GET[ 'cf_ee' ], $form );
									if ( ! empty( $entry ) ) {
										$magic_tag = $entry[ 'id' ];
									}
								}
							}
							break;
						case 'entry_token':
							$magic_tag = Caldera_Forms::get_field_data( '_entry_token', $form );
							break;
						case 'ip':

							$ip = $_SERVER[ 'REMOTE_ADDR' ];
							if ( ! empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
								$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
							} elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
								$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
							}

							$magic_tag = $ip;

							break;
						case 'ua':
							$magic_tag = $_SERVER[ 'HTTP_USER_AGENT' ];
							break;
						case 'summary':
							if ( ! empty( $form[ 'fields' ] ) ) {
								if ( ! isset( $form[ 'mailer' ][ 'email_type' ] ) || $form[ 'mailer' ][ 'email_type' ] == 'html' ) {

									$html    = true;
								} else {
									$html = false;
								}

								$magic_paser = new Caldera_Forms_Magic_Summary( $form, null );
								$magic_paser->set_html_mode( $html );
								$magic_tag = $magic_paser->get_tag();

							}
							break;
						case 'login_url' :
							$magic_tag = wp_login_url();
							break;
						case 'logout_url' :
							$magic_tag = wp_logout_url();
							break;
						case 'register_url' :
							$magic_tag = wp_registration_url();
							break;
						case 'lostpassword_url' :
							$magic_tag = wp_lostpassword_url();
							break;
						case 'current_url' :
							$magic_tag = urldecode( caldera_forms_get_current_url() );
							break;
					}

				}

				/**
				 * Change the filtered value of a bracket magic tag
				 *
				 * Can be used to make custom magic tags, but read https://calderaforms.com/?post_type=doc&p=40609 first
				 *
				 * @since unknown
				 *
				 * @param string $magic_tag Valuye after magic parsing
				 * @param string $match The matched tag
				 *
				 */
				$filter_value = apply_filters( 'caldera_forms_do_magic_tag', $magic_tag, $magics[ 0 ][ $magic_key ] );

				if ( ! empty( $form[ 'ID' ] ) ) {

					// split processor

					if ( ! empty( $magic[ 1 ] ) ) {
						if ( false !== strpos( $magic[ 1 ], ':' ) ) {
							$magic = array_reverse( explode( ':', $magic[ 1 ] ) );
						}
					}
					// check if its a process id or processor slug
					if ( empty( $processed_meta[ $form[ 'ID' ] ][ $magic[ 0 ] ] ) && ! empty( $form[ 'processors' ] ) ) {

						// if not a direct chec if theres a slug
						foreach ( $form[ 'processors' ] as $processid => $processor ) {
							if ( $processor[ 'type' ] === $magic[ 0 ] ) {
								if ( ! empty( $processed_meta[ $form[ 'ID' ] ][ $processid ] ) ) {
									$magic[ 0 ] = $processid;
									break;
								}
							}
						}
					}
					if ( ! empty( $processed_meta[ $form[ 'ID' ] ][ $magic[ 0 ] ] ) ) {

						if ( isset( $processed_meta[ $form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] ) ) {
							// direct fined
							$filter_value = implode( ', ', (array) $processed_meta[ $form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] );
						} else {
							foreach ( $processed_meta[ $form[ 'ID' ] ][ $magic[ 0 ] ] as $return_array ) {
								foreach ( $return_array as $return_line ) {
									if ( isset( $return_line[ $magic[ 1 ] ] ) ) {
										$filter_value = $return_line[ $magic[ 1 ] ];
									}
								}
							}
						}
					}
				}

				if ( $filter_value != $magics[ 1 ][ $magic_key ] ) {
					$value = str_replace( $magics[ 0 ][ $magic_key ], $filter_value, $value );
				}

			}

			/**
			 * Change value of parse bracket magic tag
			 *
			 * @since 1.5.0
			 *
			 * @param string|mixed $value Value after parsing.
			 * @param array $magics Result of preg_match
			 * @param int $entry_id Current entry ID
			 * @param array $form Current form ID
			 */
			return apply_filters( 'caldera_forms_do_field_bracket_value', $value, $magics, $entry_id, $form );
		}

		return $value;
	}


	/**
	 * Do magic tags for processors
	 *
	 * @since 1.5.6
	 *
	 * @param string $value String to parse on
	 * @param array $entry_details Prepared entry details.
	 *
	 * @return mixed
	 */
	public static function do_processor_magic( $value, $entry_details  ){
		if( empty( $entry_details[ 'meta' ] ) || empty( $entry_details[ 'meta' ][ 'processed' ] ) ){
			return $value;
		}

		$processed = $entry_details[ 'meta' ][ 'processed' ];
		$magics = Caldera_Forms_Magic_Util::explode_bracket_magic( $value );
		if( is_array( $magics ) && ! empty( $magics[1] ) ){
			foreach ( $magics[1] as $tag ){
				$parts = explode( ':', $tag );
				if( isset( $processed[ $parts[0] ]  ) && isset( $parts[1] ) && ! empty(  $processed[ $parts[0] ][ $parts[1] ] ) ){
					$value = str_replace( '{' . $tag . '}', $processed[ $parts[0] ][ $parts[1] ], $value );
				}


			}
		}

		return $value;
	}

	/**
	 * Prepare (if not already prepared) entry meta data
	 *
	 * @since 1.5.0.6
	 *
	 * @param int $entry_id
	 *
	 * @return array
	 */
	public static function magic_tag_meta_prepare( $entry_id ){
		global $processed_meta;		
		if( ! is_array( self::$entry_details ) )  {
			self::$entry_details = array();
		}


		if( ! isset( self::$entry_details[ $entry_id ] ) ) {
			$entry_details = Caldera_Forms::get_entry_detail( $entry_id );
			$this_form     = Caldera_Forms_Forms::get_form( $entry_details[ 'form_id' ] );
			if ( ! empty( $entry_details[ 'meta' ] ) ) {
				foreach ( $entry_details[ 'meta' ] as $meta_type => $meta_block ) {
					if ( ! empty( $meta_block[ 'data' ] ) ) {
						$entry_details[ 'meta' ][ 'processed' ][ $meta_type ] = array();
						foreach ( $meta_block[ 'data' ] as $meta_process_id => $proces_meta_data ) {
							foreach ( $proces_meta_data[ 'entry' ] as $process_meta_key => $process_meta_entry ) {
								$processed_meta[ $this_form[ 'ID' ] ][ $meta_process_id ][ $process_meta_key ] = $process_meta_entry[ 'meta_value' ];
								$entry_details[ 'meta' ][ 'processed' ][ $meta_type ][ $process_meta_key ]     = $process_meta_entry[ 'meta_value' ];
							}
						}
					}
				}
			}

			self::$entry_details[ $entry_id ] = $entry_details;

		}

		return self::$entry_details[ $entry_id ];

	}

	/**
	 * Implode "opts" -- IE checkboxes stored as "opts" as needed
	 *
	 * @since 1.5.0.7
	 *
	 * @param string $value Value to check and possibly convert
	 *
	 * @return string
	 */
	public static function maybe_implode_opts( $value ){
		if( is_string( $value ) && '{"opt' == substr( $value, 0, 5 ) ){
			$_value = json_decode( $value );
			if( is_object( $_value ) ){
				$value = implode( ', ', (array) $_value );
			}

		}

		return $value;

	}

	/**
	 * Create image magic
	 *
	 * @since 1.5.0.7
	 *
	 * @param array $field Field config
	 * @param array|null $form Form config
	 *
	 * @return bool|string Returns false if field is private.
	 */
	public static function magic_image( array $field, $url, array $form = null ){
		if( Caldera_Forms_Files::is_private( $field ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ){
			return false;
		}

		$form = self::filter_form( $form );

		/**
		 * Switch from link markup to image markup for imag magic tag
		 *
		 * @since 1.5.0.7
		 *
		 * @param bool $use_link If true link markup is used. If false image markup is used
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		$use_link = apply_filters( 'caldera_forms_magic_file_use_link', true, $field, $form );
		if( $use_link ){
			return sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_url( $url ) );
		}

		return sprintf( '<img src="%s" class="%s" />', esc_url( $url ), esc_attr( 'cf-image-magic-tag cf-image-magic-tag-' . $form[ 'ID' ]  ) );


	}

	/**
	 * Format calculation field
	 *
	 * @since 1.5.0.7
	 *
	 * @param array  $field Array field config
	 * @param string|int|float $value Field value
	 *
	 * @return string
	 */
	public static function calculation_magic( array $field, $value ){
		foreach ( array( 'before', 'after' ) as $config_field ) {
			if( ! isset( $field[ 'config' ][ $config_field ] ) || ! is_string( $field[ 'config' ][ $config_field ] ) ){
				$field[ 'config' ][ $config_field ] = '';
			}
		}
		return $field[ 'config' ][ 'before' ]  . $value . $field[ 'config' ][ 'after' ];
	}

	/**
	 * Pass form variable through filter and reset from global if needed
	 *
	 * @since 1.5.2
	 *
	 * @param array|string|null $form Form config or ID or null
	 *
	 * @return mixed|void
	 */
	protected static function filter_form( $form = null, $entry_id = null ){
		if( null === $form ){
			global  $form;
		}

		if( is_string( $form ) ){
			$form = Caldera_Forms_Forms::get_form( $form );
		}

		/**
		 * Filter form config before parsing magic tags
		 *
		 * @since 1.5.2
		 *
		 * @param array $form Form config
		 */
		$form = apply_filters( 'caldera_forms_magic_form', $form, $entry_id );

		return $form;

	}


}
