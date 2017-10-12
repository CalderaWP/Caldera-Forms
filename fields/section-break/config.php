<?php
if( ! defined( 'ABSPATH' ) ){
    exit;
}
?>

<div class="caldera-config-group">
    <label for="{{_name}}[width]">
        <?php esc_html_e('Width', 'caldera-forms' ); ?>
    </label>
    <div class="caldera-config-field">
        <input type="number" id="{{_name}}[width]" min="1" max="100" class="block-input field-config magic-tag-enabled" name="{{_name}}[width]" value="{{width}}" aria-describedby="{{_name}}[width]-description"/>
    </div>
    <p class="description" id="{{_name}}[width]-description">
        <?php esc_html_e('Width of section break element, as a percentage.', 'caldera-forms' ); ?>
    </p>
</div>
