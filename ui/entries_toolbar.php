		<div class="caldera-entry-exporter" style="display:none;">

			<span class="toggle_option_preview" style="">
				<button type="button" class="status_toggles button button-primary ajax-trigger" style="margin-top: 1px;"
					data-action="browse_entries"
					data-target="#form-entries-viewer"
					data-form=""
					data-template="#forms-list-alt-tmpl"
					data-load-class="spinner"
					data-active-class="button-primary"
					data-group="status_nav"
					data-callback="setup_pagination"
					data-page="1"
					data-status="active"
				>Active <span class="current-status-count"></span></button>
				<button type="button" class="status_toggles button ajax-trigger" style="margin-top: 1px; margin-right: 10px;"
					data-action="browse_entries"
					data-target="#form-entries-viewer"
					data-form=""
					data-template="#forms-list-alt-tmpl"
					data-load-class="spinner"
					data-active-class="button-primary"
					data-group="status_nav"
					data-callback="setup_pagination"
					data-page="1"
					data-status="trash"
				>Trash <span class="current-status-count"></span></button>
			</span>


			<a href="" class="button caldera-forms-entry-exporter"><?php echo __('Export Entries', 'caldera-forms'); ?></a>

			<select id="cf_bulk_action" name="action">
			</select>
			<button type="button" class="button cf-bulk-action"><?php echo __('Apply', 'caldera-forms'); ?></button>

		</div>

		<?php do_action('caldera_forms_entries_toolbar'); ?>
		<div class="tablenav caldera-table-nav" style="display:none;">
			<div class="tablenav-pages">
				<span class="displaying-num"></span>
				<span class="pagination-links">
					<a href="#first" title="Go to the first page" data-page="first" class="first-page">«</a>
					<a href="#prev" title="Go to the previous page" data-page="prev" class="prev-page">‹</a>
					<span class="paging-input"><input type="text" size="1" name="paged" title="Current page" class="current-page"> of <span class="total-pages"></span></span>
					<a href="#next" title="Go to the next page" data-page="next" class="next-page">›</a>
					<a href="#last" title="Go to the last page" data-page="last" class="last-page">»</a>
				</span>
			</div>
		</div>
		