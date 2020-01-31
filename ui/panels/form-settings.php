<?php
/**
 * Form Settings panel
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
?>
<div style="display: none;" class="caldera-editor-body caldera-config-editor-panel " id="settings-panel">
	<h3>
		<?php esc_html_e( 'Form Settings', 'caldera-forms' ); ?>
	</h3>

	<input type="hidden" name="config[cf_version]" value="<?php echo esc_attr( CFCORE_VER ); ?>">

	<div class="caldera-config-group">
		<label for="cf-form-name">
			<?php esc_html_e( 'Form Name', 'caldera-forms' ); ?>
		</label>
		<div class="caldera-config-field">
			<input id="cf-form-name"type="text" class="field-config required" name="config[name]" value="<?php echo $element[ 'name' ]; ?>" style="width:500px;" required="required">
		</div>
	</div>

	<div class="caldera-config-group">
		<label for="cf-shortcode-preview">
			<?php echo esc_html__( 'Shortcode', 'caldera-forms' ); ?>
		</label>
		<div class="caldera-config-field">
			<input type="text" id="cf-shortcode-preview" value="<?php echo esc_attr( '[caldera_form id="' . $element[ 'ID' ] . '"]' ); ?>" style="width: 500px; background: #efefef; box-shadow: none; color: #8e8e8e;" readonly="readonly">
		</div>
	</div>

	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Scroll To Top On Submit', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="scroll_top-enable">
					<input id="scroll_top-enable" type="radio" class="field-config" name="config[scroll_top]" value="1" <?php if ( ! empty( $element[ 'scroll_top' ] ) ){ ?>checked="checked"<?php } ?> aria-describedby="scroll_top-disable-description">
					<?php esc_html_e( 'Enable', 'caldera-forms' ); ?>
					<p class="description" id="scroll_top-disable-description">
						<?php esc_html_e( 'When form is submitted, scroll page to form message.', 'caldera-forms' ); ?>
					</p>
				</label>
				<label for="scroll_top-disable">
					<input id="scroll_top-disable" type="radio" class="field-config" name="config[scroll_top]" value="0" <?php if ( empty( $element[ 'scroll_top' ] ) ){ ?>checked="checked"<?php } ?> aria-describedby="scroll_top-enable-description">
					<?php esc_html_e( 'Disable', 'caldera-forms' ); ?>
					<p class="description" id="scroll_top-enable-description">
						<?php esc_html_e( 'When form is submitted, do not scroll page.', 'caldera-forms' ); ?>
					</p>
				</label>
			</div>
		</fieldset>
	</div>

	<div class="caldera-config-group" style="width:500px;">
		<label for="cf-success-message">
			<?php esc_html_e( 'Success Message', 'caldera-forms' ); ?>
		</label>
		<div class="caldera-config-field">
			<textarea id="cf-success-message" class="field-config block-input magic-tag-enabled required" name="config[success]" required="required" aria-describedby="cf-success-message-description"><?php if ( ! empty( $element[ 'success' ] ) ) {
					esc_html_e( $element[ 'success' ] );
				} else {
					esc_html_e( 'Form has been successfully submitted. Thank you.', 'caldera-forms' );
				} ?>
			</textarea>
		</div>
		<p class="description" id="cf-success-message-description">
			<?php esc_html_e( 'Message to show after form is submitted.', 'caldera-forms' ); ?>
		</p>
	</div>


	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Capture Entries', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="db_support-enable">
					<input id="db_support-enable" type="radio" class="field-config" name="config[db_support]" value="1" <?php if ( ! empty( $element[ 'db_support' ] ) ){ ?>checked="checked"<?php } ?>>
					<?php esc_html_e( 'Enable', 'caldera-forms' ); ?>
				</label>
				<label for="db_support-disable">
					<input id="db_support-disable" type="radio" class="field-config" name="config[db_support]" value="0" <?php if ( empty( $element[ 'db_support' ] ) ){ ?>checked="checked"<?php } ?>>
					<?php esc_html_e( 'Disable', 'caldera-forms' ); ?>
				</label>
			</div>
		</fieldset>
	</div>

    <div class="caldera-config-group">
        <label id="caldera-forms-label-delete-all-entries" for="caldera-forms-delete-entries-field">
            <?php esc_html_e( 'Delete Saved Entries', 'caldera-forms' ); ?>
        </label>
        <div
            id="caldera-forms-delete-entries-field"
            class="caldera-config-field"
        >
            <a
                href="#"
                class="button"
                id="caldera-forms-delete-all-form-entries"
                aria-describedby="caldera-forms-delete-entries-description"
                <?php //a used as button because that's the only way the JavaScript will work ?>
                role="button"
            >
                <?php esc_html_e('Delete All Saved Entries', 'caldera-forms'); ?>
            </a>
            <div
                 id="caldera-forms-confirm-delete-all-form-entries"
                 style="display: none;"
            >
                <p>
                    <?php esc_html_e('Are you sure you want to delete all the entries saved for this form ?', 'caldera-forms'); ?>
                </p>
                <button
                    id="caldera-forms-yes-confirm-delete-all-form-entries"
                    class="button"
                >
                    <?php esc_html_e('Yes', 'caldera-forms'); ?>
                </button>
                <button
                        id="caldera-forms-no-confirm-delete-all-form-entries"
                        class="button"
                >
                    <?php esc_html_e( 'No', 'caldera-forms'); ?>
                </button>
                <span id="caldera-forms-delete-entries-spinner" class="spinner"></span>
            </div>
            <p
                class="description"
                id="caldera-forms-delete-entries-description"
            >
                <?php esc_html_e( 'Delete all the entries saved for this form. This can NOT be undone.', 'caldera-forms' ); ?>
            </p>
        </div>
    </div>

	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Create sub-menu entry viewer', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="pin-toggle-roles-enable">
					<input  id="pin-toggle-roles-enable" type="radio" class="field-config pin-toggle-roles" name="config[pinned]" value="1"  <?php if ( ! empty( $element[ 'pinned' ] ) ){ ?>checked="checked"<?php } ?> aria-describedby="pin-toggle-roles-description">
					<?php esc_html_e( 'Enable', 'caldera-forms' ); ?>
				</label>
				<label for="pin-toggle-roles-disable">
					<input id="pin-toggle-roles-disable" type="radio" class="field-config pin-toggle-roles" name="config[pinned]" value="0" <?php if ( empty( $element[ 'pinned' ] ) ){ ?>checked="checked"<?php } ?> aria-describedby="pin-toggle-roles-description">
					<?php  esc_html_e( 'Disable', 'caldera-forms' ); ?>
				</label>
			</div>
			<p class="description" id="pin-toggle-roles-description">
				<?php esc_html_e( 'Creates a sub-menu item of the Caldera Forms menu and a page to show entries for this form.', 'caldera-forms' ); ?>
			</p>
		</fieldset>
	</div>



	<div id="caldera-pin-rules" <?php if ( empty( $element[ 'pinned' ] ) ){ ?>style="display:none;"<?php } ?>>
		<div class="caldera-config-group">
			<fieldset>
				<legend>
					<?php echo esc_html__( 'View Entries', 'caldera-forms' ); ?>
				</legend>
				<div class="caldera-config-field" style="max-width: 500px;">
					<label for="pin_role_all_roles">
						<input type="checkbox" id="pin_role_all_roles" class="field-config visible-all-roles"
						       data-set="form_role" value="1"
						       name="config[pin_roles][all_roles]" <?php if ( ! empty( $element[ 'pin_roles' ][ 'all_roles' ] ) ) {
							echo 'checked="checked"';
						} ?>>
						<?php esc_html_e( 'All', 'caldera-forms' ); ?>
					</label>
					<hr>
					<?php

					$editable_roles = caldera_forms_get_roles();

					foreach ( $editable_roles as $role => $role_details ) {
						if ( 'administrator' === $role ) {
							continue;
						}
						$id = 'cf-pin-role-' . $role;
						?>
						<label for="<?php echo esc_attr( $id ); ?>">
							<input id="<?php echo esc_attr( $id ); ?>" type="checkbox"
							       class="field-config form_role_role_check gen_role_check"
							       data-set="form_role" name="config[pin_roles][access_role][<?php echo $role; ?>]"
							       value="1" <?php if ( ! empty( $element[ 'pin_roles' ][ 'access_role' ][ $role ] ) ) {
								echo 'checked="checked"';
							} ?>>
							<?php echo esc_html( $role_details[ 'name' ] ); ?>
						</label>
						<?php
					}

					?>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'State', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="cf-forms-state">
					<input type="checkbox" id="cf-forms-state" class="field-config" name="config[form_draft]" value="1" <?php if ( ! empty( $element[ 'form_draft' ] ) ){ ?>checked="checked"<?php } ?>>
					<?php esc_html_e( 'Deactivate / Draft', 'caldera-forms' ); ?>
				</label>
			</div>
		</fieldset>
	</div>

	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Hide Form', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="cf-hide-form">
					<input id="cf-hide-form" type="checkbox" class="field-config" name="config[hide_form]" value="1" <?php if ( ! empty( $element[ 'hide_form' ] ) ){ ?>checked="checked"<?php } ?>>
					<?php echo esc_html__( 'Enable', 'caldera-forms' ); ?>
					: <?php echo esc_html__( 'Hide form after successful submission', 'caldera-forms' ); ?>
				</label>
			</div>
		</fieldset>
	</div>

	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Honeypot', 'caldera-forms' ); ?>
			</legend>
			<div class="caldera-config-field">
				<label for="cf-honey">
					<input
							id="cf-honey"
							type="checkbox"
							class="field-config"
							name="config[check_honey]"
							value="1" <?php if (!empty($element['check_honey'])){ ?>checked="checked"<?php } ?>
							aria-describedby="cf-honey-desc"
					/>
				
					<?php esc_html_e('Enable', 'caldera-forms'); ?>
					: <?php esc_html_e('Uses an anti-spam honeypot', 'caldera-forms'); ?>
                </label>
			</div>
		</fieldset>
	</div>


	<div class="caldera-config-group">
        <label for="cf-gravatar-field">
            <?php esc_html_e( 'Gravatar Field', 'caldera-forms' ); ?>
        </label>
        <div class="caldera-config-field">
            <select id="cf-gravatar-field" aria-describedby="cf-gravatar-field-description" style="width:500px;" class="field-config caldera-field-bind" name="config[avatar_field]"
                    data-exclude="system" data-default="<?php if ( ! empty( $element[ 'avatar_field' ] ) ) {
                echo $element[ 'avatar_field' ];
            } ?>" data-type="email">
                <?php
                if ( ! empty( $element[ 'avatar_field' ] ) ) {
                    echo '<option value="' . $element[ 'avatar_field' ] . '"></option>';
                }
                ?>
            </select>
            <p class="description" id="cf-gravatar-field-description">
                <?php esc_html_e( 'Used when viewing an entry from a non-logged in user.', 'caldera-forms' ); ?>
            </p>
        </div>
    </div>

	<?php

	/**
	 * Runs at the bottom of the general settings panel
	 *
	 * @since unknown
	 *
	 * @param array $element Form config
	 */
	do_action( 'caldera_forms_general_settings_panel', $element );
	?>
</div>
