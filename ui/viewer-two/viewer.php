<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
?>


<script type="text/html" id="caldera-forms-entry-tmpl">
	<div v-bind="{'data-remodal-id': entry.id }" class="caldera-forms-entry-viewer">
		<button data-remodal-action="close" class="remodal-close"  title="<?php esc_attr_e( 'Click To Close', 'caldera-forms' ); ?>" v-on:click="close" ></button>

		<div class="caldera-forms-entry-left">

		</div>
		<div class="caldera-forms-entry-right">
			<ul v-for="field in fields">
				<li class="entry-detail">
					<span class="label">{{field.label}}</span> {{ fieldValue( field.id, entry ) }}

				</li>
			</ul>
		</div>

	</div>
</script>

<div id="caldera-forms-entries">
	<div class="caldera-table">
		<table class="table table-striped">
			<thead>
				<tr>
					<th v-for="field in form.listFields">
						{{field.label}}
					</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(entry, id) in entries.entries">
					<td v-for="field in form.listFields">
						{{ fieldValue( field.id, entry ) }}
					</td>
					<td>
						<a class="button" role="button" href="#" title="<?php esc_html_e( 'View Entry Details', 'caldera-forms' ); ?>" @click="showSingle(id)" >
							<?php esc_html_e( 'Details', 'caldera-forms' ); ?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<button v-on:click="nextPage" class="caldera-forms-entry-viewer-next-button">
		<?php esc_html_e( 'Next', 'caldera-forms' ); ?>
	</button>
	<button v-on:click="prevPage" class="caldera-forms-entry-viewer-prev-button">
		<?php esc_html_e( 'Previous', 'caldera-forms' ); ?>
	</button>
	<div class="caldera-field-config">
		<label for="caldera-entry-viewer-2-per-page">
			<?php esc_html_e( 'Entries Per Page', 'caldera-forms' ); ?>
		</label>
		<input type="number" min="1" v-model="perPage" v-on:change="updatePerPage" id="caldera-entry-viewer-2-per-page">
	</div>
</div>
<div id="caldera-forms-entry"></div>

