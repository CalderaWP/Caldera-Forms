<?php
if( Caldera_Forms_Admin::is_revision_edit() ){
	printf( '<div class="notice"><p>%s</p></div>', esc_html__( 'Currently Viewing A Revision', 'caldera-forms' ) );
}

?>
<div id="caldera-forms-revisions"></div>
<span id="caldera-forms-revisions-spinner" class="spinner"></span>
<script type="text/html" id="tmpl--revisions">
	<div id="caldera-forms-revisions-list">
		{{#if revisions}}
		<fieldset>
			<legend>
				<?php esc_html_e( 'Choose Revision To Edit', 'caldera-forms' ); ?>
			</legend>

		{{#each revisions}}

				<div class="caldera-config-group">
					<label for="restore-{{id}}">
						<?php esc_html_e( 'Edit Revision:', 'caldera-forms' ); ?> {{id}}
					</label>
					<input type="radio" name="caldera-forms-revision" value="{{id}}" id="restore-{{id}}" data-edit="{{edit}}" />
				</div>

		{{/each}}
		</fieldset>

		<a href="#" id="caldera-forms-revision-go" class="button" class="notice notice-error" style="display: none;" aria-hidden="true" role="button">
			<?php esc_html_e( 'View Selected Revision', 'caldera-forms' ); ?>
		</a>
		{{else}}
		<?php esc_html_e( 'No Saved Revisions', 'caldera-forms' ); ?>
		{{/if}}
	</div>

</script>








