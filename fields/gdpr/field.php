<?php echo $wrapper_before; ?>
<?php echo $field_before; ?>
<?php
$req_class = '';
$parsley_req = '';
$req_class = ' option-required';
$parsley_req = 'data-parsley-required="true" data-parsley-group="' . esc_attr( $field_id ) . '" data-parsley-multiple="' . esc_attr( $field_id ). '"';
if( function_exists( 'caldera_forms_privacy_policy_page_url' ) ) {
    $privacy_page_url = caldera_forms_privacy_policy_page_url();
} else {
    $privacy_page_url = '';
}
?>

    <div class="checkbox-inline">
    <input <?php echo $parsley_req; ?>
            type="checkbox" data-label="<?php echo esc_attr($field['label']); ?>"
            data-field="<?php echo esc_attr($field_base_id); ?>"
            id="<?php echo esc_attr($field_id); ?>"
            class="<?php echo $field_id . $req_class; ?>"
            name="<?php echo esc_attr($field_name); ?>"
            value="1"
            data-type="checkbox"
            data-checkbox-field="<?php echo esc_attr($field_id); ?>"
    >
    <label for="<?php echo esc_attr( $field_id); ?>"
           class="caldera-forms-gdpr-field-label"
           style="display:inline; margin-left: 0.5rem;"
    >
        <?php
            if( ! array_key_exists( 'hide_label', $field ) ){
                echo esc_html($field['label']);
            }
        ?>
        <a href="<?php echo esc_url( $privacy_page_url ); ?>"
           target="_blank"
           title="<?php esc_html_e('Privacy policy Page', 'Caldera Forms'); ?>"
           style="display: inline;"
        ><?php echo esc_textarea( $field['config']['linked_text'] ); ?></a>
    </label>
    <span style="color:#ff0000;">*</span>
</div>

<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
