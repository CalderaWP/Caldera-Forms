<?php
/**
 * Helper functions for magic tag parsing
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Magic_Util {

	/**
	 * Find field based on field magic tags
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_magic
	 * @param array $form Optional. Form config.
	 *
	 * @return bool|mixed|void
	 */
	public static function find_field( $field_magic, $form = array() ) {
		if( empty( $form ) ){
			global  $form;
		}


		$part_tags = self::split_tags( $field_magic );
		if ( ! empty( $part_tags[1] ) ) {
			$tag = $part_tags[0];

		}else{
			$tag = $field_magic;

		}

		return Caldera_Forms_Field_Util::get_field_by_slug( $tag, $form );

	}

	/**
	 * Prepare a magic tag that uses brackets
	 *
	 * @since 1.5.0
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function explode_bracket_magic( $value ){
		preg_match_all( "/\{(.+?)\}/", $value, $matches );
		return $matches;
	}

	/**
	 * Prepare a magic tag that uses % %
	 *
	 * @since 1.5.0
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function explode_field_magic( $value ){
		$regex = "/%([a-zA-Z0-9_:]*)%/";

		preg_match_all( $regex, $value, $matches );
		return $matches;
	}

	/**
	 * Split tags by colon
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_magic
	 *
	 * @return array
	 */
	public static function split_tags( $field_magic ) {
		$part_tags = explode( ':', $field_magic );

		return $part_tags;
	}


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

		if ( ! empty( $matches[ 1 ] ) ) {
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


	public static function do_bracket_magic(){
		$magics = Caldera_Forms_Magic_Util::explode_bracket_magic( $value );
		if ( ! empty( $magics[ 1 ] ) ) {
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
							if ( ! empty( $this_form[ 'variables' ][ 'keys' ] ) ) {
								foreach ( $this_form[ 'variables' ][ 'keys' ] as $var_index => $var_key ) {
									if ( $var_key == $magic[ 1 ] ) {
										if ( ! in_array( $magic_tag, $magic_caller ) ) {
											$magic_caller[] = $magic_tag;
											$magic_tag      = self::do_magic_tags( $this_form[ 'variables' ][ 'values' ][ $var_index ], $entry_id, $magic_caller );
										} else {
											$magic_tag = $this_form[ 'variables' ][ 'values' ][ $var_index ];
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
					}
				} else {
					switch ( $magic_tag ) {
						case 'entry_id':
							$magic_tag = self::get_field_data( '_entry_id', $this_form );
							if ( $magic_tag === null ) {
								// check if theres an entry
								if ( ! empty( $_GET[ 'cf_ee' ] ) ) {
									$entry = self::get_entry_detail( $_GET[ 'cf_ee' ], $this_form );
									if ( ! empty( $entry ) ) {
										$magic_tag = $entry[ 'id' ];
									}
								}
							}
							break;
						case 'entry_token':
							$magic_tag = self::get_field_data( '_entry_token', $this_form );
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
							if ( ! empty( $this_form[ 'fields' ] ) ) {
								if ( ! isset( $this_form[ 'mailer' ][ 'email_type' ] ) || $this_form[ 'mailer' ][ 'email_type' ] == 'html' ) {

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


					}
				}

				$filter_value = apply_filters( 'caldera_forms_do_magic_tag', $magic_tag, $magics[ 0 ][ $magic_key ] );

				if ( ! empty( $this_form[ 'ID' ] ) ) {

					// split processor

					if ( ! empty( $magic[ 1 ] ) ) {
						if ( false !== strpos( $magic[ 1 ], ':' ) ) {
							$magic = array_reverse( explode( ':', $magic[ 1 ] ) );
						}
					}
					// check if its a process id or processor slug
					if ( empty( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] ) && ! empty( $this_form[ 'processors' ] ) ) {

						// if not a direct chec if theres a slug
						foreach ( $this_form[ 'processors' ] as $processid => $processor ) {
							if ( $processor[ 'type' ] === $magic[ 0 ] ) {
								if ( ! empty( $processed_meta[ $this_form[ 'ID' ] ][ $processid ] ) ) {
									$magic[ 0 ] = $processid;
									break;
								}
							}
						}
					}
					if ( ! empty( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] ) ) {

						if ( isset( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] ) ) {
							// direct fined
							$filter_value = implode( ', ', (array) $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] );
						} else {
							foreach ( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] as $return_array ) {
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
		}
	}


}