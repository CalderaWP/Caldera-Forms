<script type="text/html" id="calculator-group-tmpl">
	{{#each group}}
		{{#if operator}}
			<div class="caldera-config-group caldera-config-group-full calculation-group-connect calculation-group" style="text-align:center;" id="op-group-{{line-group}}">
				<select class="calculation-operator" data-op-id="{{op-id}}" data-group="{{line-group}}">
					<option value="+" {{#is operator value="+"}}selected="selected"{{/is}}>+</option>
					<option value="-" {{#is operator value="-"}}selected="selected"{{/is}}>&minus;</option>
					<option value="*" {{#is operator value="*"}}selected="selected"{{/is}}>&times;</option>
					<option value="/" {{#is operator value="/"}}selected="selected"{{/is}}>&divide;</option>
				</select>
			</div>

		{{else}}

			<div class="caldera-config-group caldera-config-group-full calculation-group">
				<div class="calculation-group-lines" data-group="{{@key}}" id="calculation-group-{{@key}}">
					{{#each lines}}
					<div class="calculation-group-line"  data-line="{{line}}" data-group="{{line-group}}">
						<select class="calculation-operator calculation-operator-line" data-line="{{line}}" data-group="{{line-group}}">
							<option value="+" {{#is operator value="+"}}selected="selected"{{/is}}>+</option>
							<option value="-" {{#is operator value="-"}}selected="selected"{{/is}}>&minus;</option>
							<option value="*" {{#is operator value="*"}}selected="selected"{{/is}}>&times;</option>
							<option value="/" {{#is operator value="/"}}selected="selected"{{/is}}>&divide;</option>
						</select>
						<select class="calculation-operator-field" data-exclude="system" data-default="{{field}}" style="max-width:229px;width:229px;" data-line="{{line}}" data-group="{{line-group}}"></select>
						<button class="button remove-operator-line calculation-remove-line pull-right" type="button"><i class="icon-join"></i></button>
					</div>
					{{/each}}
				</div>
				<button type="button" class="button button-small calculation-add-line"  style="margin-top: 12px;" data-group="{{@key}}">
					<?php esc_html_e( 'Add Line', 'caldera-forms' ); ?>
				</button>
			</div>

		{{/if}}

	{{/each}}
</script>




















