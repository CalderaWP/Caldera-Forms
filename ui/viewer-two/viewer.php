<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
?>

<script type="text/x-template" id="caldera-forms-entries-tmpl">
	<div class="caldera-table">
		<table class="table table-striped">
			<thead>
			<tr>
				<th v-for="key in columns"
				    @click="sortBy(key)"
				    :class="{ active: sortKey == key }">
					{{ key.label | capitalize }}

				</th>
			</tr>
			</thead>
			<tbody>
			<tr v-if="! single" v-for="entry in filteredData">
				<td v-for="key in columns">

				<span v-if="key == 'Submitted'">
					{{entry[key.id]}}
				</span>
					<span v-else>
					{{entry[key.id]}}
				</span>
				</td>
				<td>
					<a href="#" role="button" v-on:click="showDetails(entry.id)" class="button cf-entry-view-details" title="<?php esc_attr_e( 'Click to view details', 'caldera-forms' ); ?>" :data-entry-id=entry.id>
						<?php esc_html_e( 'View', 'caldera-forms' ); ?>
					</a>
					<?php if( current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) ) { ?>
						<a href="#" role="button" v-on:click="delete(entry.id)" class="button cf-entry-delete" title="<?php esc_attr_e( 'Click to delete item', 'caldera-forms' ); ?>" :data-entry-id=entry.id>
							<?php esc_html_e( 'Delete', 'caldera-forms' ); ?>
						</a>
					<?php } ?>
				</td>
			</tr>
			</tbody>
		</table></div>
</script>


<div id="caldera-forms-entries">

	<form id="caldera-forms-entries-toolbar">
		<div class="caldera-config-field">
			<label for="caldera-forms-entries-toolbar-filter">
				<?php esc_html_e( 'Filter', 'caldera-forms' ); ?>
			</label>
			<input id="caldera-forms-entries-toolbar-filter" name="query" v-model="searchQuery">
		</div>
		<div class="caldera-config-field">
			<label for="caldera-forms-entries-toolbar-perpage">
				<?php esc_html_e( 'Entries Per Page', 'caldera-forms' ); ?>
			</label>
			<input id="caldera-forms-entries-toolbar-perpage" type="number" min="1" max="100" name="perpage" v-model="perPage">
		</div>
		<span class="pagination-links">
				<a href="#first" title="<?php esc_attr_e( 'Go to the first page', 'caldera-forms' ); ?>" data-page="first" class="first-page">«</a>
				<a href="#prev" title="<?php esc_attr_e( 'Go to the previous page', 'caldera-forms' ); ?>" data-page="prev" class="prev-page">‹</a>
				<input type="number" class="current-page" v-model="page"></input>
				<a href="#next" title="<?php esc_attr_e( 'Go to the next page', 'caldera-forms' ); ?>" data-page="next" class="next-page">›</a>
				<a href="#last" title="<?php esc_attr_e( 'Go to the last page', 'caldera-forms' ); ?>" data-page="last" class="last-page">»</a>
			</span>
	</form>
	<caldera-forms-entries
		:data="gridData"
		:columns="gridColumns"
		:filter-key="searchQuery">
	</caldera-forms-entries>
</div>
<div id="caldera-forms-entry-tmpl" class="caldera-table" aria-live="assertive" aria-hidden="true" style="display: none;visibility: hidden">

	<table class="table table-striped">
		<thead>

		<template>
			<tr>
				<td v-for="header in headers">
					{{header.label}}
				</td>
			</tr>

		</template>

		</thead>
		<tbody>
		<tr>
			<template v-for="field in fields">
				<th>{{data[field]}}</th>
			</template>
		</tr>
		</tbody>
	</table>
</div>