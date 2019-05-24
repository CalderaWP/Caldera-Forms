		<?php
			$entry_perpage = get_option( '_caldera_forms_entry_perpage', 20 );
		?>
		<div class="caldera-entry-exporter" style="display:none;">
			<?php wp_nonce_field( 'cf_toolbar', 'cf_toolbar_actions' ); ?>
			<span class="cf-tools-row">
				<?php if( empty( $is_pinned ) ){ ?>
				<button id="cf_forms_toggle" type="button" class="button hide-forms" title="<?php esc_attr_e( 'Click to close entry viewer', 'caldera-forms' ); ?>" style="padding: 3px; margin-top: 1px; margin-right: 18px; color: rgb(143, 143, 143);">				
					<span class="dashicons dashicons-admin-collapse"></span>
					<span class="screen-reader-text">
						<?php esc_html_e( 'Close', 'caldera-forms' ); ?>
					</span>
				</button>
				<?php } ?>				
				<span class="toggle_option_preview">

					<button type="button" class="status_toggles button button-primary ajax-trigger" style="margin-top: 1px;"
						data-before="cf_set_limits"
						data-action="browse_entries"
						data-target="#form-entries-viewer"
						data-form=""
						data-template="#forms-list-alt-tmpl"
						data-load-class="spinner"
						data-active-class="button-primary"
						data-group="status_nav"
						data-callback="setup_pagination"
						data-page="1"
					    data-nonce="<?php echo esc_attr( wp_create_nonce( 'view_entries' ) ); ?>"
						data-status="active"
					    data-perpage="<?php echo esc_attr( Caldera_Forms_Entry_Viewer::entries_per_page() ); ?>"
					><?php esc_html_e('Active', 'caldera-forms'); ?> <span class="current-status-count"></span></button>
					<button type="button" class="status_toggles button ajax-trigger" style="margin-top: 1px; margin-right: 10px;"
						data-before="cf_set_limits"
						data-action="browse_entries"
						data-target="#form-entries-viewer"
						data-form=""
						data-template="#forms-list-alt-tmpl"
						data-load-class="spinner"
						data-active-class="button-primary"
						data-group="status_nav"
						data-callback="setup_pagination"
						data-page="1"
						data-perpage="<?php echo esc_attr( Caldera_Forms_Entry_Viewer::entries_per_page() ); ?>"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'view_entries' ) ); ?>"
				        data-status="trash"
					><?php echo esc_html_x( 'Trash', 'Status: In trash', 'caldera-forms' ); ?> <span class="current-status-count"></span></button>
				</span>
				<a href="" role="button" class="button caldera-forms-entry-exporter">
					<?php esc_html_e( 'Export All', 'caldera-forms' ); ?>
				</a>
				
			</span>
			<span class="cf-tools-row cf-tools-row-second">
				<label for="cf_bulk_action" class="screen-reader-text">
					<?php esc_html_e( 'Bulk Action For Entries', 'caldera-forms' ); ?>
				</label>
				<select id="cf_bulk_action" name="action" style="vertical-align: top;">
					<option selected="selected" value="">
						<?php esc_html_e( 'Bulk Actions', 'caldera-forms' ); ?>
					</option>
					<option value="export">
						<?php esc_html_e( 'Export Selected', 'caldera-forms' ); ?>
					</option>
					<option value="trash">
						<?php esc_html_e( 'Move to Trash', 'caldera-forms' ); ?>
					</option>
				</select>
				<button type="button" class="button cf-bulk-action">
					<?php esc_html_e( 'Apply', 'caldera-forms' ); ?>
				</button>
			</span>

		</div>

		<?php
		/**
		 * Runs at the end of the entries toolbar
		 *
		 * @since unknown
		 */
		do_action('caldera_forms_entries_toolbar');
		?>

