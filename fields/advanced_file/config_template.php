<div class="caldera-config-group">
  <label role="presentation"></label>
  <div class="caldera-config-field">
    <label for="{{_id}}_attach"><input id="{{_id}}_attach" type="checkbox" class="field-config" name="{{_name}}[attach]" value="1" {{#if attach}}checked="checked"{{/if}}>
            <?php echo esc_html__('Attach to mailer', 'caldera-forms'); ?>
        </label>
  </div>
</div>

<div class="caldera-config-group" id="{{_id}}_image_with_preview_wrap">
  <label role="presentation"></label>
  <div class="caldera-config-field">
    <label for="{{_id}}_image_with_preview"><input id="{{_id}}_image_with_preview" type="checkbox" class="field-config" name="{{_name}}[image_with_preview]" value="1" {{#if image_with_preview}}checked="checked"{{/if}}>
            <?php echo esc_html__('Image with Preview', 'caldera-forms'); ?>
        </label>
  </div>
</div>

<div class="caldera-config-group" id="{{_id}}_allow_multiple_wrap">
  <label role="presentation"></label>
  <div class="caldera-config-field">
    <label for="{{_id}}_allow_multiple"><input id="{{_id}}_allow_multiple" type="checkbox" class="field-config" name="{{_name}}[multi_upload]" value="1" {{#if multi_upload}}checked="checked"{{/if}}>
            <?php echo esc_html__('Allow Multiple', 'caldera-forms'); ?>
        </label>
  </div>
</div>

<div class="caldera-config-group">
  <label role="presentation"></label>
  <div class="caldera-config-field">
    <label for="{{_id}}_media_library"><input id="{{_id}}_media_library" type="checkbox" class="field-config" name="{{_name}}[media_lib]" value="1" {{#if media_lib}}checked="checked"{{/if}}>
            <?php echo esc_html__('Add to Media Library', 'caldera-forms'); ?>
        </label>
  </div>
</div>

<div class="caldera-config-group" id="{{_id}}_allow_multiple_text_wrap">
  <label for="{{_id}}_allow_multiple_text">
        <?php echo esc_html__('Button Text', 'caldera-forms'); ?>
    </label>
  <div class="caldera-config-field">
    <input id="{{_id}}_allow_multiple_text" type="text" class="field-config" name="{{_name}}[multi_upload_text]" value="{{#if multi_upload_text}}{{multi_upload_text}}{{/if}}">
  </div>
</div>

<div class="caldera-config-group" id="{{_id}}_allowed_wrap">
  <label for="{{_id}}_allowed"><?php echo esc_html__('Allowed Types', 'caldera-forms'); ?></label>
  <div class="caldera-config-field">
    <input id="{{_id}}_allowed" type="text" class="field-config" name="{{_name}}[allowed]" value="{{allowed}}">
    <p class="description"><?php echo esc_html__('Comma separated eg. jpg,pdf,txt', 'caldera-forms'); ?></p>
  </div>
</div>


{{#script}}
  jQuery(function($){
    var allow_multiple_text_wrap = $('#{{_id}}_allow_multiple_text_wrap');
    var allow_multiple_check_wrap = $('#{{_id}}_allow_multiple_wrap');
    var allow_multiple_check = $('#{{_id}}_allow_multiple');
    var allowed_extensions = $('#{{_id}}_allowed_wrap');
    var image_with_preview_check = $('#{{_id}}_image_with_preview');

    allow_multiple_check.change(function(){
      if( $(this).prop('checked') ){
        allow_multiple_text_wrap.show();
        $('#{{_id}}_image_with_preview_wrap').hide();
      }else{
        $('#{{_id}}_image_with_preview_wrap').show();
        allow_multiple_text_wrap.hide();
      }
    });

    image_with_preview_check.change(function(){
        if( $(this).prop('checked') ){
          allow_multiple_text_wrap.show();
          allow_multiple_check_wrap.hide();
          allowed_extensions.hide();
        }else{
          allow_multiple_text_wrap.hide();
          allow_multiple_check_wrap.show();
          allowed_extensions.show();
        }
    });

    image_with_preview_check.trigger('change');
    allow_multiple_check.trigger('change');
  });
{{/script}}