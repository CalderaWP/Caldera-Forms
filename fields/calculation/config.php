<div class="caldera-config-group">
	<label for="{{_id}}_element"><?php echo __('Element', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_element" type="text" class="input-block field-config" name="{{_name}}[element]" value="{{element}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_classes"><?php echo __('Classes', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_classes" type="text" class="input-block field-config" name="{{_name}}[classes]" value="{{classes}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_before"><?php echo __('Before', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_before" type="text" class="input-block field-config" name="{{_name}}[before]" value="{{before}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_after"><?php echo __('After', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_after" type="text" class="input-block field-config" name="{{_name}}[after]" value="{{after}}">
	</div>
</div>
<div class="caldera-config-group">
	
	<div class="caldera-config-field">
		<label><input id="{{_id}}_fixed" type="checkbox" class="field-config" name="{{_name}}[fixed]" value="1" {{#if fixed}}checked="checked"{{/if}}> <?php echo __('Money Format', 'caldera-forms-calculator'); ?></label>
	</div>
</div>

<div class="caldera-config-group caldera-config-group-full">
	<button type="button" class="button block-button add-operator-group ajax-trigger" 
	data-template="#calculator-group-tmpl" 
	data-target="#{{_id}}_operator_groups" 
	data-target-insert="append" 
	data-name="{{_name}}" 
	data-id="{{_id}}"
	data-request="calc_add_group"
	data-callback="init_calc_group"
	><?php echo __('Add Operator Group'); ?></button>
</div>
<br>
<div id="{{_id}}_operator_groups" class="calculation-groups-wrap"></div>
<input type="hidden" class="block-input field-config calculation-formular" name="{{_name}}[formular]" id="{{_id}}_formular" value="{{formular}}">
<input type="hidden" class="block-input field-config ajax-trigger" data-request="build_calc_structure" data-callback="{{_id}}_build_formula" data-init="{{_id}}_build_formula" data-event="none" data-autoload="true" data-template="#calculator-group-tmpl" data-target="#{{_id}}_operator_groups" name="{{_name}}[config]" id="{{_id}}_config" value="{{config}}">
<br><br><br>
{{#script}}
//<script>


function {{_id}}_build_formula(obj){

	build_calculations_formular('{{_id}}', obj);

};

jQuery('#{{_id}}_operator_groups').on('change', 'select', function(e){		
	{{_id}}_build_formula();
});


{{/script}}