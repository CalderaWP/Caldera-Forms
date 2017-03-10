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
						<select class="calculation-operator-field calculation-operator-field-{{_id}} " data-exclude="system" data-default="{{field}}" style="max-width:229px;width:229px;"></select>
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





















