<script type="text/html" id="extensions-modal-tmpl">
{{#if extensions}}
	{{#each extensions}}
	<div {{#if slug}}class="panel_{{slug}}"{{/if}} style="margin: 10px; width: {{#if width}}{{width}}{{else}}200px{{/if}}; float: left; height: {{#if height}}{{height}}{{else}}200px{{/if}}; {{#if box}}overflow: auto; border: 1px solid rgba(0, 0, 0, 0.15); box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);{{/if}}{{#if background}} background:{{background}};{{/if}}{{#if color}} color:{{color}};{{/if}}position: relative;">
		{{#if banner}}<img src="{{banner}}" style="width:100%;vertical-align: top;">{{/if}}
		{{#if name}}<h2>{{name}}</h2>{{/if}}
		{{#if html}}<div style="margin: 0px; padding: 6px 7px;">{{{html}}}</div>{{/if}}
		{{#if buttons}}
		<div style="position: absolute; bottom: 0px; padding: 6px; background: none repeat scroll 0 0 rgba(0, 0, 0, 0.03); left: 0px; right: 0px; border-top: 1px solid rgba(0, 0, 0, 0.06);">
			{{#each buttons}}
				<a class="button {{#if class}}{{class}}{{/if}}" href="{{link}}" target="_blank" rel="nofollow">{{title}}</a>
			{{/each}}
		</div>
		{{/if}}
	</div>
	{{/each}}
{{else}}
{{#if message}}
	<div class="alert updated"><p>{{{message}}}</p></div>
{{else}}
	<div class="alert error"><p><?php echo __('Unable to connect or no extensions available.', 'caldera-forms'); ?></p></div>
{{/if}}
{{/if}}
</script>
<script type="text/html" id="nav-items-tmpl">
{{#if channels}}
	{{#each channels}}
	<li id="tab_extend_{{channel}}" 
	data-load-class="spinner" 
	data-group="main-nav" 
	data-callback="update_existing" 
	data-before="cf_clear_panel" 
	data-target="#form-extend-viewer" 
	data-error="extend_fail_notice" 
	{{#if content}}
		data-request="{{content}}"
	{{else}}
			{{#if url}}
				data-request="{{url}}"
			{{else}}
				data-request="<?php echo CFCORE_EXTEND_URL . '{{location}}/?version=' . CFCORE_VER; ?>"
			{{/if}}
			{{#if template}}
				data-template-url="{{template}}"
			{{else}}
				data-template="#extensions-modal-tmpl"
			{{/if}}
	{{/if}}
	class="ajax-trigger{{#if class}} {{class}}{{/if}}" {{#if attributes}}{{{attributes}}}{{/if}}
	><a href="#form-extend-viewer">{{name}}</a>
	</li>
	{{/each}}
{{/if}}
</script>
