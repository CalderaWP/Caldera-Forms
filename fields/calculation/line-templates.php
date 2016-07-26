<script type="text/html" id="calculator-group-tmpl">
	{{#each group}}

	{{#if operator}}
		
		<div class="caldera-config-group caldera-config-group-full calculation-group-connect calculation-group" style="text-align:center;">
			<select class="calculation-operator">
				<option value="+" {{#is operator value="+"}}selected="selected"{{/is}}>+</option>
				<option value="-" {{#is operator value="-"}}selected="selected"{{/is}}>&minus;</option>
				<option value="*" {{#is operator value="*"}}selected="selected"{{/is}}>&times;</option>
				<option value="/" {{#is operator value="/"}}selected="selected"{{/is}}>&divide;</option>
			</select>
		</div>

	{{else}}
	
		<div class="caldera-config-group caldera-config-group-full calculation-group">
			<div class="calculation-group-lines">
				{{#each lines}}
				<div class="calculation-group-line">
					<select class="calculation-operator">
						<option value="+" {{#is operator value="+"}}selected="selected"{{/is}}>+</option>
						<option value="-" {{#is operator value="-"}}selected="selected"{{/is}}>&minus;</option>
						<option value="*" {{#is operator value="*"}}selected="selected"{{/is}}>&times;</option>
						<option value="/" {{#is operator value="/"}}selected="selected"{{/is}}>&divide;</option>
					</select>
					<select class="calculation-operator-field caldera-field-bind" data-exclude="system" data-default="{{field}}" style="max-width:229px;width:229px;"></select>
					<button class="button remove-operator-line pull-right" type="button"><i class="icon-join"></i></button>
				</div>
				{{/each}}
			</div>
			<button type="button" class="button button-small calculation-add-line" style="margin-top: 12px;">Add Line</button>
		</div>

	{{/if}}

	{{/each}}
</script>
<script type="text/javascript">

function build_calc_structure(obj){	
	var config = {};
	if(obj.trigger.val().length){
		config = JSON.parse(obj.trigger.val());
		config.init = obj.trigger.data('init');

	}
	return config;
}
function init_calc_group(obj){
	obj.params.target.find('select').first().trigger('change');
	rebuild_field_binding();
}
function calc_add_group(obj){
	var groups 	= {
		lines	:	[
			{
				operator	: '+',
				field		: ''
			}
		]
	},
	out = {group : []};

	if(obj.target.find('.calculation-group').length){
		out.group.push({
			operator	: '+'
		});
	}
	
	out.group.push(groups);

	return out;
}
function build_calculations_formular(id, obj){
	var wrap	= jQuery('#'+id+'_operator_groups'),
		groups = wrap.find('.calculation-group'),
		formula = '',
		formular_input = jQuery('#' + id + '_formular'),
		config_input = jQuery('#' + id + '_config'),
		config = {
			group: []
		};

	groups.each(function(k,v){

		var lines = jQuery(v).find('.calculation-group-line'),
			connector = jQuery(v).find('.calculation-operator'),
			group	= {};

		if(lines.length){

			group.lines = [];
			// lines
			jQuery(v).find('.calculation-operator').first().prop('disabled', true).hide();
			jQuery(v).find('.calculation-operator-field').first().css({'maxWidth': '272px', 'width': '272px'});
			
			if(lines.length > 1){
				formula += ' ( ';
			}
				lines.each(function(l,b){

					var op = jQuery(b).find('.calculation-operator'),
						fi = jQuery(b).find('.calculation-operator-field'),
						line = {
							operator	: '+',
							field		: ''
						};

					if(fi.val()){
						if(op.length){
							if(op.prop('disabled') !== true){
								formula += op.val();
								line.operator = op.val();
							}
						}
						if(op.length){
							formula += fi.val();
							line.field = fi.val();
						}
					}
					group.lines.push(line);
				});

			if(lines.length > 1){
				formula += ' ) ';
			}
		}else if(connector.length){
			// connector
			formula += connector.val();
			group.operator = connector.val();
		}

		config.group.push(group);
	});
	if(typeof obj === 'undefined'){
		formular_input.val(formula);
		config_input.val(JSON.stringify(config));
	}
}
jQuery(function($){
	$('body').on('click', '.calculation-add-line', function(e){

		var clicked = $(this),
			lastline = clicked.prev().find('.calculation-group-line').last().clone();

		lastline.find('select').css({'width': "", 'maxWidth' : ""}).prop('disabled',false).show();
		lastline.appendTo(clicked.prev());
		lastline.find('select').first().trigger('change');

	});
	$('body').on('click', '.remove-operator-line', function(e){

		var clicked = $(this),
			row = clicked.parent(),
			group = clicked.closest('.caldera-config-group'),
			wrap = clicked.closest('.calculation-groups-wrap');

		row.remove();			
		if(!group.find('.calculation-group-line').length){
			if(group.prev().hasClass('calculation-group-connect')){
				group.prev().remove();
			}
			group.remove();

		}
		//calculation-formular
		var trigger = wrap.find('select:first');
		if(trigger.length){
			wrap.find('select:first').trigger('change');
		}else{
			wrap.next().val('');
		}
	});
})

</script>





















