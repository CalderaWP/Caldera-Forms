<?php echo $wrapper_before; ?>
<?php echo $field_before; ?>
<?php
$req_class = '';
$parsley_req = '';
$req_class = ' option-required';
$parsley_req = 'data-parsley-required="true" data-parsley-group="' . esc_attr( $field_id ) . '" data-parsley-multiple="' . esc_attr( $field_id ). '"';

$privacy_page_url = caldera_forms_privacy_policy_page_url();

if( !empty( $field['config']['title_attr'] ) ) {
    $title_attribute = $field['config']['title_attr'];
}else{
    $title_attribute = __('Privacy Policy Page', 'caldera-forms');
}
?>

<div class="checkbox-inline caldera-forms-consent-field">
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
        <p
            class="caldera-forms-consent-field-agreement"
        >
            <?php echo esc_textarea( $field['config']['agreement'] ); ?>
        </p>
        <a href="<?php echo esc_url( $privacy_page_url ); ?>"
           target="_blank"
                title="<?php echo esc_attr( $title_attribute ); ?>"
            class="caldera-forms-consent-field-linked_text"
            >
            <?php echo esc_textarea( $field['config']['linked_text'] ); ?>
        </a>
    </label>
    <span style="color:#ff0000;">*</span>
</div>

<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
