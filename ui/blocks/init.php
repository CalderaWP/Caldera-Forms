<?php
/**
 * Block: Caldera Forms
 */
/**
 * This code is super-copypasta of Ahmad Awais Gutenberg Boilerplate: https://github.com/ahmadawais/Gutenberg-Boilerplate/tree/master/block/02-basic-esnext
 *
 * Hi Ahmad
 * https://AhmdA.ws/GutenbergBoilerplate
 */



/** Hooks for Gutenberg */
add_action( 'enqueue_block_editor_assets', 'caldera_forms_enqueue_block_assets');
add_action( 'init', 'caldera_forms_register_block');

/**
 * Enqueue the block's assets for the editor.
 *
 * @uses "enqueue_block_editor_assets" action
 * @since 1.5.8
 */
function caldera_forms_enqueue_block_assets() {
	Caldera_Forms_Render_Assets::enqueue_script( 'blocks', array( 'wp-blocks', 'wp-i18n', 'wp-element' ) );

    caldera_forms_print_cf_forms_var('blocks');


}

/**
 * Print to DOM the CF_FORM variable
 *
 * @since 1.7.0
 *
 * @param string $script_handle Handle of script to use with wp_localize_script()
 */
function caldera_forms_print_cf_forms_var($script_handle){
    $form_options = array();
    $forms = Caldera_Forms_Forms::get_forms(true);
    $forms = array_reverse($forms);
    foreach ($forms as $form) {
        $form_options[] = array(
            'name' => esc_html($form['name']),
            'formId' => esc_attr($form['ID']),
            'ID' => esc_attr($form['ID'])
        );
    }

    wp_localize_script(
        Caldera_Forms_Render_Assets::make_slug($script_handle),
        'CF_FORMS',
        array(
            'forms' => $form_options
        )
    );
}


/**
 * Render a Caldera Forms block
 *
 * @since 1.5.8
 *
 * @param array $atts
 * @return string|void
 */
function caldera_forms_render_cform_block($atts ) {
    if( ! empty( $atts[ 'formId' ] ) ){
        return Caldera_Forms::render_form(
            array(
                'ID' => caldera_forms_very_safe_string( $atts[ 'formId' ] )
            )
        );
    }


}

/**
 * Register blocks
 *
 * @uses "init"
 *
 * @since 1.5.8
 */
function caldera_forms_register_block(){
    if( ! function_exists( 'register_block_type' ) ){
        return;
    }
    register_block_type( 'calderaforms/cform', array(
        'render_callback' => 'caldera_forms_render_cform_block',
        'attributes' => array(
            'formId' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ) );
}




