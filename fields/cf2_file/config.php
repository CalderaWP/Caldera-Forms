<div class="caldera-config-group">
    <label for="{{_id}}_allow_multiple_text">
        <?php echo esc_html__('Button Text', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <input id="{{_id}}_allow_multiple_text" type="text" class="field-config" name="{{_name}}[multi_upload_text]"
               value="{{#if multi_upload_text}}{{multi_upload_text}}{{/if}}">
    </div>
</div>

<div class="caldera-config-group">
    <label role="presentation"></label>
    <div class="caldera-config-field">
        <label for="{{_id}}_attach"><input id="{{_id}}_attach" type="checkbox" class="field-config"
                                           name="{{_name}}[attach]" value="1" {{#if attach}}checked="checked" {{/if}}>
			<?php echo esc_html__('Attach to Mailer', 'caldera-forms'); ?>
        </label>
    </div>
</div>

<div class="caldera-config-group">
    <label role="presentation"></label>
    <div class="caldera-config-field">
        <label for="{{_id}}_allow_multiple"><input id="{{_id}}_allow_multiple" type="checkbox" class="field-config"
                                                   name="{{_name}}[multi_upload]" value="1" {{#if
                                                   multi_upload}}checked="checked" {{/if}}>
			<?php echo esc_html__('Allow Multiple', 'caldera-forms'); ?>
        </label>
    </div>
</div>

<div class="caldera-config-group">
    <label role="presentation"></label>
    <div class="caldera-config-field">
        <label for="{{_id}}_media_library"><input id="{{_id}}_media_library" type="checkbox" class="field-config"
                                                  name="{{_name}}[media_lib]" value="1" {{#if
                                                  media_lib}}checked="checked" {{/if}}>
			<?php echo esc_html__('Add to Media Library', 'caldera-forms'); ?>
        </label>
    </div>
</div>


<div class="caldera-config-group">
    <label for="{{_id}}_allowed"><?php echo esc_html__('Allowed Types', 'caldera-forms'); ?></label>
    <div class="caldera-config-field">
        <input
            id="{{_id}}_allowed"
            type="text"
            class="field-config"
            name="{{_name}}[allowed]"
            value="{{allowed}}"
            aria-describedby="{{_id}}_allowed-desc"
        >
        <p class="description" id="{{_id}}_allowed-desc">
            <?php echo esc_html__('Comma separated eg. jpg,pdf,txt', 'caldera-forms'); ?>
        </p>
    </div>
</div>

<div class="caldera-config-group">
    <label for="{{_id}}_max_upload"><?php echo esc_html__('Max Upload Size', 'caldera-forms'); ?></label>
    <div class="caldera-config-field">
        <input
                id="{{_id}}_max_upload"
                type="number"
                min="0"
                step="8"
                class="field-config"
                name="{{_name}}[max_upload]"
                value="{{max_upload}}"
                aria-describedby="{{_id}}_max_upload-desc"

        >
        <p class="description" id="{{_id}}_max_upload-desc" >
            <?php echo esc_html__('Max file size in bytes. If 0, any file size is allowed.', 'caldera-forms'); ?>
        </p>
    </div>
</div>


<div class="caldera-config-group">
    <label role="presentation"></label>
    <div class="caldera-config-field">
        <label for="{{_id}}_use_image_previews"><input id="{{_id}}_use_image_previews" type="checkbox" class="field-config"
                                                  name="{{_name}}[use_image_previews]" value="1" {{#if
                                                       use_image_previews}}checked="checked" {{/if}}>
            <?php echo esc_html__('Use image previews', 'caldera-forms'); ?>
        </label>
    </div>
</div>


<div class="caldera-config-group" id="{{_id}}_previews_height_wrap">
    <label for="{{_id}}preview_height">
		<?php echo esc_html__('Image Preview Height', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <input
                id="{{_id}}preview_height"
                type="number"
                min="0"
                steps="1"
                max="128"
                class="field-config"
                name="{{_name}}[preview_height]"
                value="{{#if preview_height}}{{preview_height}}{{/if}}">
    </div>

</div>


<div class="caldera-config-group" id="{{_id}}_previews_width_wrap">
    <label for="{{_id}}preview_width">
		<?php echo esc_html__('Image Preview Width', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <input
                id="{{_id}}preview_width"
                type="number"
                min="0"
                steps="1"
                max="128"
                class="field-config"
                name="{{_name}}[preview_width]"
                value="{{#if preview_width}}{{preview_width}}{{/if}}">
    </div>
</div>



{{#script}}
jQuery(function($){

//Image previews sizes
$('#{{_id}}_use_image_previews').change(function(){
    if( $(this).prop('checked') ){
        $('#{{_id}}_previews_height_wrap, #{{_id}}_previews_width_wrap').show();
    }else{
        $('#{{_id}}_previews_height_wrap, #{{_id}}_previews_width_wrap').hide();
    }
});
$('#{{_id}}_use_image_previews').trigger('change');


});
{{/script}}
