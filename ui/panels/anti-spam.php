<?php
/**
 * Anti-spam settings panel
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
$element = $form = Caldera_Forms_Forms::get_form( esc_attr( $_GET[ 'edit' ] ) );
if (empty($element['antispam'])) {
    $element['antispam'] = array();
}
if (empty($element['antispam']['enable'])) {
    $element['antispam']['enable'] = '';
}
if (empty($element['antispam']['sender_email'])) {
    $element['antispam']['sender_email'] = '';
}
if (empty($element['antispam']['sender_name'])) {
    $element['antispam']['sender_name'] = '';
}
$cf_pro_active = caldera_forms_pro_is_active();
?>
<div id="anti-spam-settings-panel">
    <h3>
        <?php esc_html_e('AntiSpam Settings', 'caldera-forms'); ?>
    </h3>

    <div class="caldera-config-group">
        <fieldset>
            <legend>
                <?php esc_html_e('Basic', 'caldera-forms'); ?>
            </legend>
            <div class="caldera-config-field">
                <input
                        id="cf-honey"
                        type="checkbox"
                        class="field-config"
                        name="config[check_honey]"
                        value="1" <?php if (!empty($element['check_honey'])){ ?>checked="checked"<?php } ?>
                        aria-describedby="cf-honey-desc"
                />
                <label for="cf-honey">
                    <?php esc_html_e('Enable', 'caldera-forms'); ?>
                </label>

                <p class="description" id="cf-honey-desc">
                    <?php esc_html_e('Uses an anti-spam honeypot', 'caldera-forms'); ?>
                </p>
            </div>
        </fieldset>
    </div>
    <div class="caldera-config-group">
        <fieldset>
            <legend>
                <?php esc_html_e('Advanced', 'caldera-forms'); ?>
            </legend>
            <div class="caldera-config-field">
                <input
                        id="cf-pro-anti-spam"
                        type="checkbox"
                        class="field-config"
                        name="config[antispam][enable]"
                        value="1"
                        <?php if ($cf_pro_active && !empty($element['antispam']['enable'])){ ?>checked="checked"<?php } ?>
                        <?php if (!$cf_pro_active) { ?>disabled<?php } ?>
                />
                <label for="cf-pro-anti-spam">
                    <?php esc_html_e('Enable'); ?>
                </label>
                <p class="description" id="cf-pro-anti-spam-desc">
                    <?php
                    esc_html_e('Uses Caldera Forms Pro for spam scan and email address blacklist check.',
                        'caldera-forms');
                    if (!$cf_pro_active) {
                        esc_html_e('Requires Caldera Forms Pro', 'caldera-forms');
                    }
                    ?>
                </p>

            </div>
        </fieldset>
    </div>

    <div class="caldera-config-group" id="caldera-anti-spam-settings-wrap">
        <div class="caldera-config-group">
            <label for="cf-pro-anti-spam-sender-name">
                <?php echo __('Sender Name', 'caldera-forms'); ?>
            </label>
            <div class="caldera-config-field">
                <input
                        type="text"
                        id="cf-pro-anti-spam-sender-name"
                        class=" field-config magic-tag-enabled"
                        name="config[antispam][sender_name]"
                        value="<?php echo esc_attr($element['antispam']['sender_name']); ?>"
                        aria-describedby="cf-pro-anti-spam-sender-name-desc"
                />

                <p
                        id="cf-pro-anti-spam-sender-name-desc"
                        class="description"
                >
                    <?php esc_html_e('Field with the form submitter\'s name.', 'caldera-forms'); ?>
                </p>
            </div>
        </div>
        <div class="caldera-config-group">
            <label for="cf-pro-anti-spam-sender-name-email">
                <?php echo __('Email', 'caldera-forms'); ?>
            </label>
            <div class="caldera-config-field">
                <input
                        type="text"
                        id="cf-pro-anti-spam-sender-name-email"
                        class="field-config magic-tag-enabled caldera-field-bind"
                        name="config[antispam][sender_email]"
                        value="<?php echo esc_attr($element['antispam']['sender_email']); ?>"
                        aria-describedby="cf-pro-anti-spam-sender-name-email-desc" ,
                />
                <p
                        id="cf-pro-anti-spam-sender-name-email-desc"

                        class="description"
                >
                    <?php esc_html_e('Field with the form submitter\'s email address', 'caldera-forms'); ?>
                </p>
            </div>
        </div>
    </div>

</div>

<script>
    jQuery(function ($) {
        var $wrap = $('#caldera-anti-spam-settings-wrap');
        var $enable = $('#cf-pro-anti-spam');
        var $inputs = $wrap.find( 'input' );
        var hideShow = function () {
            if ($enable.prop('checked') && !$enable.prop('disabled')) {
                $wrap
                    .show()
                    .attr('aria-hidden', false);
                $inputs.prop('required', true)
                    .addClass('required');

            } else {
                $wrap
                    .hide()
                    .attr('aria-hidden', true);
                $inputs
                    .prop('required', false)
                    .removeClass('required');
            }
        };

        $enable.change(function () {
            hideShow();
        });

        hideShow();
    });
</script>




