<?php

/**
 * Add auto-populate option for Easy Queries to select fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_APEasyQueries extends Caldera_Forms_Admin_APSetup {

	/**
	 * @inheritdoc
	 */
	public function add_type() {
		if( defined( 'CAEQ_PATH' ) ){
			printf( '<option value="easy-query"{{#is auto_type value="easy-query"}} selected="selected"{{/is}}>%s</option>', esc_html__( 'Easy Query', 'caldera-forms' ) );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function add_options() {
		if( ! defined( 'CAEQ_PATH' ) ){
			return;
		}
		$easy_queries = \calderawp\caeq\options::get_registry();
		?>
		<div class="caldera-config-group caldera-config-group-auto-easy-query auto-populate-type-panel" style="display:none;">
			<label for="cf-auto-populate-easy-queries">
				<?php esc_html_e( 'Easy Queries', 'caldera-forms' ); ?>
			</label>
			<div class="caldera-config-field">
				<select id="cf-auto-populate-easy-queries" class="block-input field-config" name="{{_name}}[easy-query]">";
					<?php
					foreach( $easy_queries as $easy_query ){
						printf(  '<option value="%s" {{#is easy-query value="%s"}}selected="selected"{{/is}}>%s</option>',
							esc_attr( $easy_query[ 'id' ] ), esc_attr( $easy_query[ 'id' ] ), esc_html__( $easy_query[ 'name' ] ));
					}

					?></select>

			</div>
		</div>
		<?php
	}

}