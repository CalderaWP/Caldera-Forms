<?php


class Caldera_Forms_Admin_Privacy{
	private $fields_to_show_personally_identifying_question;

	public function add_personally_identifying_question($config, $type ){
		if($this->should_show_personally_identifying_question($type)){
			$idAttr =
			printf( '
				<div class="caldera-config-group entrylist-field">
					<label for="<?php echo esc_attr($id); ?>_entry_list"><?php echo esc_html__( \'Show in Entry List\', \'caldera-forms\' ); ?></label>
					<div class="caldera-config-field">
						<input type="checkbox" class="field-config field-checkbox" id="<?php echo esc_attr($id); ?>_entry_list" name="config[fields][<?php echo esc_attr($id); ?>][entry_list]" value="1" <?php if($entry_list === 1){ echo \'checked="checked"\'; }else{?>{{#if entry_list}}checked="checked"{{/if}}<?php } ?>>
					</div>
				</div>
			', );
		}
	}

	protected function should_show_personally_identifying_question( $type ){
		return in_array( $type, $this->fields_to_show_personally_identifying_question() );
	}

	protected function fields_to_show_personally_identifying_question(){
		if( ! $this->fields_to_show_personally_identifying_question ){
			$this->fields_to_show_personally_identifying_question = array_keys( Caldera_Forms_Fields::get_all() );
			foreach ( array(
				'button',
				'color_picker',
				'calculation',
				'html',
				'section_break',
					  ) as $field_type ){
				unset( $this->fields_to_show_personally_identifying_question[$field_type ] );
			}
		}

		return $this->fields_to_show_personally_identifying_question;
	}
}