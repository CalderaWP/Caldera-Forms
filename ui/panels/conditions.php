<?php
// conditional groups template
$element['conditional_groups']['magic'] = $magic_tags['system']['tags'];
if( !empty( $element['conditional_groups']['fields'] ) ){
	unset( $element['conditional_groups']['fields'] );
}
?>

<button style="width:250px;" id="new-conditional" class="button" type="button">
    <?php _e( 'Add Conditional Group', 'caldera-forms' ); ?>
</button>

<input type="hidden" name="_magic" value="<?php echo esc_attr( json_encode( $magic_tags['system']['tags'] ) ); ?>">
<input type="hidden" id="cf-conditions-db" name="config[conditional_groups]" value="<?php echo esc_attr( json_encode( $element['conditional_groups'] ) ); ?>" 
class="ajax-trigger"
data-event="rebuild-conditions"
data-request="#cf-conditions-db"
data-type="json"
data-template="#conditions-tmpl"
data-target="#caldera-forms-conditions-panel"
data-autoload="true"
>
<div id="caldera-forms-conditions-panel"></div>
<script type="text/html" id="conditions-tmpl">
	<input type="hidden" name="_open_condition" value="{{_open_condition}}">
	<div class="caldera-editor-conditions-panel" style="margin-bottom: 32px;">
		<ul class="active-conditions-list">
			{{#each conditions}}
				<li class="caldera-condition-nav {{#is @root/_open_condition value=id}}active{{/is}} caldera-forms-condition-group condition-point-{{id}}" style="">
					<input type="hidden" name="conditions[{{id}}][id]" value="{{id}}">
					{{#if name}}			
						{{#is @root/_open_condition not=id}}
							<input type="hidden" name="conditions[{{id}}][name]" value="{{name}}">
							<input type="hidden" name="conditions[{{id}}][type]" value="{{type}}">
							{{#if fields}}
								<input type="hidden" name="conditions[{{id}}][fields]" value="{{json fields}}">
							{{/if}}
							{{#if group}}
								<input type="hidden" name="conditions[{{id}}][group]" value="{{json group}}">
							{{/if}}

						{{/is}}
						<a data-open-group="{{id}}" style="cursor:pointer;"><span id="condition-group-{{id}}">{{name}}</span> <span class="condition-line-number"></span></a>
					{{else}}
						<input type="text"
                               name="conditions[{{id}}][name]"
                               value="{{name}}"
                               data-new-condition="{{id}}"
                               class="condition-new-group-name"
                               placeholder="<?php echo esc_attr( 'New Group Name', 'caldera-forms'); ?>" style="width:100%;">
						{{#script}}
							jQuery('[data-new-condition]').focus();
						{{/script}}
					{{/if}}
				</li>

			{{/each}}
		</ul>
	</div>

	{{#find conditions @root/_open_condition}}
		<div class="caldera-editor-condition-config caldera-forms-condition-edit" style="margin-top: -27px; width:auto;">
			{{#if name}}
				<div class="condition-point-{{id}}" style="width: 550px; float: left;">
					<div class="caldera-config-group">
						<label for="{{id}}_lable"><?php _e( 'Name', 'caldera-forms' ); ?></label>
						<div class="caldera-config-field">
							<input
                                    type="text"
                                    name="conditions[{{id}}][name]"
                                    id="condition-group-name-{{id}}"
                                    data-sync="#condition-group-{{id}}"
                                    value="{{name}}"
                                    required
                                    class="required block-input condition-group-name"
                            />
						</div>
					</div>
					
					<div class="caldera-config-group">
						<label for="{{id}}_lable"><?php _e( 'Type', 'caldera-forms' ); ?></label>
						<div class="caldera-config-field">
							<select
                                    name="conditions[{{id}}][type]"
                                    data-live-sync="true"
                                    class="condition-group-type"
                            >
								<option value=""></option>
								<option value="show" {{#is type value="show"}}selected="selected"{{/is}}><?php _e('Show', 'caldera-forms'); ?></option>
								<option value="hide" {{#is type value="hide"}}selected="selected"{{/is}}><?php _e('Hide', 'caldera-forms'); ?></option>
								<option value="disable" {{#is type value="disable"}}selected="selected"{{/is}}><?php _e('Disable', 'caldera-forms'); ?></option>
							</select>
							{{#if type}}
								<button
                                        type="button"
                                        data-add-group="{{id}}"
                                        class="pull-right button button-small condition-group-add-lines">
                                    <?php echo __('Add Conditional Line', 'caldera-forms'); ?>
                                </button>
							{{/if}}
						</div>
					</div>
					{{#each group}}
						{{#unless @first}}
							<span style="display: block; margin: 0px 0px 8px;"><?php _e( 'or', 'caldera-forms' ); ?></span>
						{{/unless}}
						<div class="caldera-condition-group caldera-condition-lines">
						{{#each this}}
							<div class="caldera-condition-line condition-line-{{@key}}">
								<input type="hidden" name="conditions[{{../../id}}][group][{{parent}}][{{@key}}][parent]" value="{{parent}}">
								<span style="display:inline-block;">{{#if @first}}
									<?php _e( 'if', 'caldera-forms' ); ?>
								{{else}}
									<?php _e( 'and', 'caldera-forms' ); ?>
								{{/if}}</span>
								<input type="hidden" name="conditions[{{../../../id}}][fields][{{@key}}]" value="{{field}}" id="condition-bound-field-{{@key}}" data-live-sync="true">
								<select
                                    class="condition-line-field"
                                    style="max-width:120px;vertical-align: inherit;"
                                    name="conditions[{{../../id}}][group][{{parent}}][{{@key}}][field]"
                                    data-sync="#condition-bound-field-{{@key}}"
                                >
									<option></option>
									<optgroup label="<?php _e('Fields', 'caldera-forms'); ?>">
									{{#each @root/fields}}
										<option value="{{ID}}" {{#is ../field value=ID}}selected="selected"{{/is}} {{#is conditions/type value=../../../id}}disabled="disabled"{{/is}}>{{label}} [{{slug}}]</option>
									{{/each}}
									</optgroup>
									<?php /*<optgroup label="System Tags">
									{{#each @root/magic}}
										<option value="{{this}}" {{#is ../field value=this}}selected="selected"{{/is}}>{{this}}</option>
									{{/each}}
									</optgroup>*/ ?>
								</select>
								<select
                                    class="condition-line-compare"
                                    style="max-width:110px;vertical-align: inherit;"
                                    name="conditions[{{../../id}}][group][{{parent}}][{{@key}}][compare]"
                                >
									<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}><?php _e( 'is', 'caldera-forms' ); ?></option>
									<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}><?php _e( 'is not', 'caldera-forms' ); ?></option>
									<option value="greater" {{#is compare value="greater"}}selected="selected"{{/is}}><?php _e( 'is greater than', 'caldera-forms' ); ?></option>
									<option value="smaller" {{#is compare value="smaller"}}selected="selected"{{/is}}><?php _e( 'is less than', 'caldera-forms' ); ?></option>
									<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}><?php _e( 'starts with', 'caldera-forms' ); ?></option>
									<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}><?php _e( 'ends with', 'caldera-forms' ); ?></option>
									<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}><?php _e( 'contains', 'caldera-forms' ); ?></option>
								</select>
								<span data-value="" class="caldera-conditional-field-value" style="padding: 0 12px 0; display:inline-block; width:200px;">
								{{#find @root/fields field}}
									{{#if config/option}}
										<select style="width:165px;vertical-align: inherit;" name="conditions[{{../../../../id}}][group][{{../../parent}}][{{@key}}][value]">
											<option></option>
											{{#each config/option}}
												<option value="{{@key}}" {{#is ../../../value value=@key}}selected="selected"{{/is}}>{{label}}</option>
											{{/each}}
										</select>
									{{else}}
										<input type="text" class="magic-tag-enabled block-input" name="conditions[{{../../../../id}}][group][{{../../parent}}][{{@key}}][value]" value="{{../../value}}" {{#unless ../../field}}placeholder="<?php echo esc_html__( 'Select field first', 'caldera-forms' ); ?>" disabled=""{{/unless}}>
									{{/if}}
								{{else}}
									<input type="text" class="magic-tag-enabled block-input" name="conditions[{{../../../../id}}][group][{{../parent}}][{{@key}}][value]" value="{{../value}}" {{#unless ../field}}placeholder="<?php echo esc_html__( 'Select field first', 'caldera-forms' ); ?>" disabled=""{{/unless}}>
								{{/find}}
								</span>
								<button
                                    class="caldera-condition-line-remove button
                                     pull-right"
                                    data-remove-line="{{@key}}"
                                    type="button"
                                >
                                    <i class="icon-join"></i>
                                </button>
							</div>
						{{/each}}
						<div style="margin: 12px 0 0;">
                            <button
                                class="button button-small condition-group-add-line"
                                type="button"
                            >
                                <?php _e( 'Add Condition', 'caldera-forms' ); ?>
                            </button>
                        </div>
						</div>
					{{/each}}

					<button style="margin: 12px 0 12px;" type="button" class="block-input button" data-confirm="<?php echo esc_attr( __('Are you sure you want to remove this condition?', 'caldera forms') ); ?>" data-remove-group="{{id}}"><?php _e( 'Remove Condition', 'caldera-forms' ); ?></button>
				</div>
				<div style="float: left; width: 288px; padding-left: 12px;">
				{{#if @root/fields}}
					<h4 style="border-bottom: 1px solid rgb(191, 191, 191); margin: 0px 0px 6px; padding: 0px 0px 6px;"><?php _e('Applied Fields', 'caldera-forms'); ?></h4>
					<p class="description"><?php _e('Select the fields to apply this condition to.', 'caldera-forms' ); ?></p>
					{{#each @root/fields}}

						<label style="display: block; margin-left: 20px;{{#find ../../fields ID}}opacity:0.7;{{/find}}"><input style="margin-left: -20px;" type="checkbox" data-bind-condition="#field-condition-type-{{ID}}" value="{{../id}}" {{#is conditions/type value=../id}}checked="checked"{{else}}{{#find @root/conditions conditions/type}}disabled="disabled"{{/find}}{{/is}} {{#find ../../fields ID}}disabled="disabled"{{/find}}>{{label}} [{{slug}}]</label>
						
					{{/each}}
				{{/if}}
				</div>
			{{/if}}
		</div>
	{{/find}}

</script>
