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
	<h3><?php echo __( 'Form Settings', 'caldera-forms'  ); ?></h3>
	<input type="hidden" name="config[cf_version]" value="<?php echo esc_attr( CFCORE_VER ); ?>">
	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Form Name', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="field-config required" name="config[name]" value="<?php echo $element['name']; ?>" style="width:500px;" required="required">
		</div>
	</div>
	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Shortcode', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<input type="text" id="cf-shortcode-preview" value="<?php echo esc_attr( '[caldera_form id="' . $element['ID'] . '"]' ); ?>" style="width: 500px; background: #efefef; box-shadow: none; color: #8e8e8e;" readonly="readonly">
		</div>
	</div>

	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Form Description', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<textarea name="config[description]" class="field-config" style="width:500px;" rows="5"><?php echo htmlentities( $element['description'] ); ?></textarea>
		</div>
	</div>

	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'State', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<label><input type="checkbox" class="field-config" name="config[form_draft]" value="1" <?php if(!empty($element['form_draft'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Deactivate / Draft', 'caldera-forms' ); ?></label>
		</div>
	</div>

	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Capture Entries', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<label><input type="radio" class="field-config" name="config[db_support]" value="1" <?php if(!empty($element['db_support'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Enable', 'caldera-forms' ); ?></label>
			<label><input type="radio" class="field-config" name="config[db_support]" value="0" <?php if(empty($element['db_support'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Disabled', 'caldera-forms' ); ?></label>
		</div>
	</div>

	<div class="caldera-config-group">
		<label>
			<?php esc_html_e('Show Entry View Page?', 'caldera-forms' ); ?>
		</label>
		<div class="caldera-config-field">
			<label><input type="radio" class="field-config pin-toggle-roles" name="config[pinned]" value="1" <?php if(!empty($element['pinned'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Enable', 'caldera-forms' ); ?></label>
			<label><input type="radio" class="field-config pin-toggle-roles" name="config[pinned]" value="0" <?php if(empty($element['pinned'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Disabled', 'caldera-forms' ); ?></label>
		</div>
		<p class="description">
			<?php esc_html_e('Create a sub-menu item of the Caldera Forms menu and a page to show entries for this form?','caldera-forms'); ?>
		</p>
	</div>

	<div id="caldera-pin-rules" <?php if(empty($element['pinned'])){ ?>style="display:none;"<?php } ?>>
		<div class="caldera-config-group">
			<label><?php echo esc_html__( 'View Entries', 'caldera-forms' ); ?> </label>
			<div class="caldera-config-field" style="max-width: 500px;">
				<label><input type="checkbox" id="pin_role_all_roles" class="field-config visible-all-roles" data-set="form_role" value="1" name="config[pin_roles][all_roles]" <?php if( !empty($element['pin_roles']['all_roles'])){ echo 'checked="checked"'; } ?>> <?php echo esc_html__( 'All'); ?></label>
				<hr>
				<?php
				global $wp_roles;
				$all_roles = $wp_roles->roles;
				$editable_roles = apply_filters( 'editable_roles', $all_roles);

				foreach($editable_roles as $role=>$role_details){
					if( 'administrator' === $role){
						continue;
					}
					?>
					<label><input type="checkbox" class="field-config form_role_role_check gen_role_check" data-set="form_role" name="config[pin_roles][access_role][<?php echo $role; ?>]" value="1" <?php if( !empty($element['pin_roles']['access_role'][$role])){ echo 'checked="checked"'; } ?>> <?php echo $role_details['name']; ?></label>
					<?php
				}

				?>
			</div>
		</div>
	</div>

	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Hide Form', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<label><input type="checkbox" class="field-config" name="config[hide_form]" value="1" <?php if(!empty($element['hide_form'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Enable', 'caldera-forms' ); ?>: <?php echo esc_html__( 'Hide form after successful submission', 'caldera-forms' ); ?></label>
		</div>
	</div>

	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Honeypot', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<label><input type="checkbox" class="field-config" name="config[check_honey]" value="1" <?php if(!empty($element['check_honey'])){ ?>checked="checked"<?php } ?>> <?php echo esc_html__( 'Enable', 'caldera-forms' ); ?>: <?php echo esc_html__( 'Place an invisible field to trick spambots', 'caldera-forms' ); ?></label>
		</div>
	</div>

	<div class="caldera-config-group" style="width:500px;">
		<label><?php echo esc_html__( 'Success Message', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<textarea class="field-config block-input magic-tag-enabled required" name="config[success]" required="required"><?php if (!empty($element['success'])) { echo esc_html( $element['success'] ); } else { echo esc_html__( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ); } ?></textarea>
		</div>
	</div>
	<div class="caldera-config-group">
		<label><?php echo esc_html__( 'Gravatar Field', 'caldera-forms' ); ?> </label>
		<div class="caldera-config-field">
			<select style="width:500px;" class="field-config caldera-field-bind" name="config[avatar_field]" data-exclude="system" data-default="<?php if(!empty($element['avatar_field'])){ echo $element['avatar_field']; } ?>" data-type="email">
				<?php
				if(!empty($element['avatar_field'])){ echo '<option value="'.$element['avatar_field'].'"></option>'; }
				?>
			</select>
			<p class="description"><?php echo esc_html__( 'Used when viewing an entry from a non-logged in user.','caldera-forms'); ?></p>
		</div>
	</div>

	<?php do_action('caldera_forms_general_settings_panel', $element); ?>
</div>

