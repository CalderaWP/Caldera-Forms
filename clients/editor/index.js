var baldrickTriggers;
import {bindBaldrickTriggers} from "../functions/bindBaldrickTriggers";

/* contains edit.js, layout-grid.js, processors.js */
function new_conditional_group({trigger}) {
    const id 	  	=	trigger.data('id');
    const lineid 	=	`cl${Math.round(Math.random() * 18746582734)}`;
    const rowid	=	`rw${Math.round(Math.random() * 98347598345)}`;

    const group 	=	[
        {
            id		:	rowid,
            type	:	trigger.data('type'),
            lines	:	[
                {
                    id	:	lineid
                }
            ]
        }
    ];


    return {group, id};
}
function new_conditional_line({trigger}) {
    const id 	  	=	trigger.data('id');
    const rowid 	=	trigger.data('group');
    const type 	=	trigger.data('type');
    const lineid	=	`cl${Math.round(Math.random() * 18746582734)}`;

    const line 	=	{
        id,
        type,
        lineid,
        rowid,
        name	:	`config[${type}][${id}][conditions][group][${rowid}][${lineid}]`
    };

    return line;
}

function build_conditions_config({trigger}) {
    const config = JSON.parse(trigger.val());
    config.id = trigger.data('id');
    return config;

}

/**
 * Pre compile all Handelbars templates
 */
function pre_compile_templates(){
    const pretemplates = jQuery('.cf-editor-template');
    for( let t = 0; t < pretemplates.length; t++){
        compiled_templates[pretemplates[t].id] = Handlebars.compile( pretemplates[t].innerHTML );
    }

}

let cfAdminAJAX;
if( 'object' == typeof  CF_ADMIN ){
    cfAdminAJAX = CF_ADMIN.adminAjax;
} else {
    //yolo
    cfAdminAJAX = ajaxurl;
}

/**
 * Get a compiled Handlebars template or the fallback template
 * @param template
 * @returns {*}
 */
function get_compiled_template( template ) {
    if( 'object' !=  typeof compiled_templates ){
        pre_compile_templates();
    }
    return compiled_templates[ `${template}_tmpl`] ? compiled_templates[ `${template}_tmpl`] : compiled_templates.noconfig_field_templ;

}


jQuery(document).ready($ => {
    baldrickTriggers = bindBaldrickTriggers($,cfAdminAJAX);
    const $spinner = $('#save_indicator');

    $( '#caldera-forms-restore-revision' ).on( 'click', function(e){
        e.preventDefault();
        const $el = $(this);
        $spinner.addClass('loading');

        $.post({
            url: 'admin.php?page=caldera-forms',
            data:{
                cf_edit_nonce: $( '#cf_edit_nonce' ).val(),
                form: $el.data( 'form' ),
                cf_revision: $( '#form_db_id_field' ).val(),
                restore: true
            },
            success() {
                window.location = $el.data( 'edit-link' );
            }
        })
    });
    $('.caldera-header-save-button').baldrick({
        method			:	'POST',
        request			:	'admin.php?page=caldera-forms',
        before(el, e) {
            e.preventDefault();

            if(!check_required_bindings()){
                return false;
            }

            $spinner.addClass('loading');
            if( typeof tinyMCE !== 'undefined'){
                tinyMCE.triggerSave();
            }

            const data_fields		= $('.caldera-forms-options-form').formJSON();
            if( data_fields.conditions ){
                data_fields.config.conditional_groups = { conditions : data_fields.conditions };
            }
            $(el).data('cf_edit_nonce', data_fields.cf_edit_nonce);
            $(el).data('_wp_http_referer', data_fields._wp_http_referer);
            $(el).data('sender', 'ajax');
            $( document ).trigger( 'cf.presave', {
                config: data_fields.config
            });
            $(el).data('config', JSON.stringify(data_fields.config));

            return true;

        },
        callback({data}) {

            if( false === data ){
                const notice = $('.updated_notice_box');

                notice.stop().animate({top: 0}, 200, () => {
                    setTimeout( () => {
                        notice.stop().animate({top: -75}, 200);
                    }, 2000);
                });

                cf_revisions_ui();
            }
        },
        complete(obj) {

            $('.wrapper-instance-pane .field-config').prop('sabled', false);

        }
    });

    /*
     *	Build the fieltypes config
     *	configs are stored in the .caldera-config-field-setup field within the parent wrapper
     *
     */
    function build_fieldtype_config(el){
        const select 			= $(el);
        const val = select.val();
        const parent			= select.closest('.caldera-editor-field-config-wrapper');
        const target			= parent.find('.caldera-config-field-setup');
        let template = get_compiled_template(val);
        let config = parent.find('.field_config_string').val();
        let current_type = select.data('type');


        parent.find('.caldera-config-group').show();

        select.addClass('field-initialized');

        // Be sure to load the fields preset when switching back to the initial field type.
        if(config.length && current_type === select.val() ){
            config = JSON.parse(config);
        }else{
            // default config
            config = fieldtype_defaults[`${select.val()}_cfg`];
        }

        // build template
        if(!config){
            config = {};
        }

        config._id = select.data('field');
        config._name = `config[fields][${select.data('field')}][config]`;


        template = $('<div>').html( template( config ) );

        // send to target
        target.html( template.html() );

        // check for init function
        if( typeof window[`${select.val()}_init`] === 'function' ){
            window[`${select.val()}_init`](select.data('field'), target);
        }

        // remove not supported stuff
        if(fieldtype_defaults[`${select.val()}_nosupport`]){

            if(fieldtype_defaults[`${select.val()}_nosupport`].includes('hide_label')){
                parent.find('.hide-label-field').hide().find('.field-config').prop('checked', false);
            }
            if(fieldtype_defaults[`${select.val()}_nosupport`].includes('caption')){
                parent.find('.caption-field').hide().find('.field-config').val('');
            }
            if(fieldtype_defaults[`${select.val()}_nosupport`].includes('required')){
                parent.find('.required-field').hide().find('.field-config').prop('checked', false);
            }
            if(fieldtype_defaults[`${select.val()}_nosupport`].includes('custom_class')){
                parent.find('.customclass-field').hide().find('.field-config').val('');
            }
            if(fieldtype_defaults[`${select.val()}_nosupport`].includes('entry_list')){
                parent.find('.entrylist-field').hide().find('.field-config').prop('checked', false);
            }
        }

        // seup options
        parent.find('.toggle_show_values').trigger('change');

        if( !$('.caldera-select-field-type').not('.field-initialized').length){
            if(!core_form){
                core_form = jQuery('.caldera-forms-options-form');
            }

            // build previews
            if(!core_form.hasClass('builder-loaded')){

                const fields = $('.caldera-select-field-type.field-initialized');
                for( let f = 0; f < fields.length; f++){
                    build_field_preview( $(fields[f]).data('field') );
                }
                core_form.addClass('builder-loaded');
            }else{
                build_field_preview( select.data('field') );
            }
            $('.caldera-header-save-button').prop('disabled', false);
            rebuild_field_binding();
            baldrickTriggers();
        }
        if( $('.color-field').length ){
            $('.color-field').wpColorPicker({
                change(obj) {

                    const trigger = $(this);


                    if( trigger.data('ev') ){
                        clearTimeout( trigger.data('ev') );
                    }
                    trigger.data('ev', setTimeout( () => {
                        trigger.trigger('record');
                    },200) );
                    if( trigger.data('target') ){
                        $( trigger.data('target') ).css( trigger.data('style'), trigger.val() );
                        $( trigger.data('target') ).val( trigger.val() );
                    }

                }
            });
        }
    }

    function build_field_preview(id){
        const panel           = $(`#${id}`);
        const select			= panel.find('.caldera-select-field-type');
        const val             = select.val();
        const preview_parent	= $(`.layout-form-field[data-config="${id}"]`);
        const preview_target	= preview_parent.find('.field_preview');
        const preview			= $(`#preview-${val}_tmpl`).html();
        const template 		= get_compiled_template( `preview-${val}` );
        const config			= {'id': id};
        const data_fields		= panel.find('.field-config');
        const objects			= [];

        data_fields.each((k, v) => {
            const field 		= $(v);
            const basename 	= field.prop('name').split(`[${id}]`)[1].substr(1);
            const name		= basename.substr(0, basename.length-1).split('][');
            const value 		= ( field.is(':checkbox,:radio') ? field.filter(':checked').val() : field.val() );
            let lineconf 	= {};

            for(let i = name.length-1; i >= 0; i--){

                if(i === name.length-1){
                    lineconf[name[i]] = value;
                }else{
                    const newobj = lineconf;
                    lineconf = {};
                    lineconf[name[i]] = newobj;
                }
            }
            $.extend(true, config, lineconf);
        });

        preview_target.html( template(config) );
        preview_parent.removeClass('button');

        $('.preview-field-config').prop('disabled', true);
    }

    // switch active group
    function switch_active_group(id){
        const fields_panel	= $('.caldera-editor-fields-panel');
        const groups_panel	= $('.caldera-editor-groups-panel');
        const group_navs		= $('.caldera-group-nav');
        const group_line		= $(`[data-group="${id}"]`);
        const group_name		= group_line.find('.caldera-config-group-name');
        const group_slug		= group_line.find('.caldera-config-group-slug');
        const group_desc		= group_line.find('.caldera-config-group-desc');
        const group_admin		= group_line.find('.caldera-config-group-admin');
        const group_name_edit	= $('.active-group-name');
        const group_slug_edit	= $('.active-group-slug');
        const group_desc_edit	= $('.active-group-desc');
        const group_admin_edit= $('.active-group-admin');
        const field_lists		= $('.caldera-editor-fields-list ul');
        const group_repeat	= group_line.find('.caldera-config-group-repeat');
        const repeat_button	= $('.repeat-config-button');
        const group_settings	= $('.caldera-editor-group-settings');
        const deleter 		= $('.caldera-config-group-remove');
        let group_field_lists;

        // remove any hdden fields
        $('.new-group-input').remove();
        $('.new-field-input').remove();


        // remove current active group
        group_navs.removeClass('active');

        // show fields panel
        fields_panel.show();

        // hide all groups
        field_lists.hide();

        // remove active field
        field_lists.removeClass('active').find('li.active').removeClass('active');
        field_lists.hide();

        // set active group
        group_line.addClass('active');

        // hide delete button or show
        group_field_lists = $('.caldera-editor-fields-list ul.active li');

        if(group_field_lists.length){
            // has fields
            deleter.hide();
        }else{
            deleter.show();
        }


        // hide all field configs
        $('.caldera-editor-field-config-wrapper').hide();

        // show groups fields
        group_line.show();

        // set group name edit field
        group_name_edit.val(group_name.val());

        // set group slug edit field
        group_slug_edit.val(group_slug.val());

        // set group slug edit field
        group_desc_edit.val(group_desc.val());

        // set group admin edit field
        if(group_admin.val() === '1'){
            group_admin_edit.prop('checked', true);
        }else{
            group_admin_edit.prop('checked', false);
        }




        // is repeatable
        if(group_repeat.val() === '1'){
            repeat_button.addClass('field-edit-open');
        }else{
            repeat_button.removeClass('field-edit-open');
        }
    }

    // tabs button
    $('body').on('click', '.toggle_processor_event input', function(e){
        const clicked = $(this);
        const parent = clicked.closest( '.wrapper-instance-pane' );
        const settings = parent.find('.caldera-config-processor-setup');
        const notice = parent.find('.caldera-config-processor-notice');


        if( clicked.is(':checked') ){
            clicked.parent().addClass('activated');
            clicked.parent().find('.is_active').show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
            clicked.parent().find('.not_active').hide().attr( 'aria-hidden', true ).css( 'visibility', 'hidden' );
        }else{
            clicked.parent().removeClass('activated');
            clicked.parent().find('.is_active').hide().attr( 'aria-hidden', true ).css( 'visibility', 'hidden' );
            clicked.parent().find('.not_active').show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
        }

        // check if all are selected
        if( parent.find('.toggle_processor_event .activated').length ){
            settings.slideDown(100);
            notice.slideUp(100);
        }else{
            settings.slideUp(100);
            notice.slideDown(100);
        }
    });
    $('body').on('click', '.toggle_option_tab > a', function(e){
        e.preventDefault();
        const clicked = $(this);
        const panel = $(clicked.attr('href'));
        const tabs = clicked.parent().find('a');
        const panels = clicked.closest('.caldera-editor-config-wrapper').find('.wrapper-instance-pane');

        tabs.removeClass('button-primary');

        panels.hide();
        panel.show();
        clicked.addClass('button-primary');
        $('.caldera-conditionals-usetype').trigger('change');
    });


    // Change Field Type
    $('.caldera-editor-body').on('change', '.caldera-select-field-type', function(e){
        // push element to config function
        build_fieldtype_config(this);
    });

    // build group navigation
    $('.caldera-editor-body').on('click', '.caldera-group-nav a', function(e){

        // stop link
        e.preventDefault();

        //switch group
        switch_active_group( $(this).attr('href').substr(1) );

    });

    // build field navigation
    $('.caldera-editor-body').on('click', '.caldera-editor-fields-list a', function(e){
        // stop link
        e.preventDefault();

        const clicked 		= $(this);
        const field_config	= $( clicked.attr('href') );

        // remove any hdden fields
        $('.new-group-input').remove();
        $('.new-field-input').remove();


        // remove active field
        $('.caldera-editor-fields-list li.active').removeClass('active');

        // mark active
        clicked.parent().addClass('active');

        // hide all field configs
        $('.caldera-editor-field-config-wrapper').hide();

        // show field config
        field_config.show();
    });

    // bind show group config panel
    $('.caldera-editor-body').on('click', '.group-config-button', function(e){
        const clicked = $(this);
        const group_settings	= $('.caldera-editor-group-settings');
        const parent = clicked.closest('.caldera-editor-fields-panel');
        const deleter = $('.caldera-config-group-remove');

        // check if children
        if(parent.find('.caldera-field-line').length){
            // has fields
            deleter.hide();
        }else{
            deleter.show();
        }

        if(clicked.hasClass('field-edit-open')){
            // show config
            group_settings.slideUp(100);
            clicked.removeClass('field-edit-open');
        }else{
            // hide config
            group_settings.slideDown(100);
            clicked.addClass('field-edit-open');
        }
    });
    $('.caldera-editor-body').on('keydown', '.field-config', function(e){
        if($(this).is('textarea')){
            return;
        }
        if(e.which === 13){
            e.preventDefault();
        }
    });
    // field label bind
    $('.caldera-editor-body').on('change', '.field-label', function({type}) {
        const field 		= $(this).closest('.caldera-editor-field-config-wrapper').prop('id');
        field_line	= $(`[data-field="${field}"]`),
            field_title	= $(`#${field} .caldera-editor-field-title, .layout-form-field.field-edit-open .layout_field_name`),
            slug		= $(`#${field} .field-slug`);

        field_line.find('a').html( `<i class="icn-field"></i> ${this.value}` );
        field_title.text( this.value );
        if(type === 'change'){
            slug.trigger('change');
        }
        rebuild_field_binding();
    });


    // rename group
    $('.caldera-editor-body').on('change', '.active-group-name', function(e){
        e.preventDefault();
        const active_group		= $('.caldera-group-nav.active');
        const group				= active_group.data('group');
        const group_name			= active_group.find('.caldera-config-group-name');
        const group_label			= active_group.find('span');

        // check its not blank
        if(e.type === 'focusout' && !this.value.length){
            this.value = `Group ${parseInt( active_group.index() ) + 1}`;
        }


        group_name.val(this.value);
        group_label.text(this.value);
    });
    // rename group slug
    $('.caldera-editor-body').on('change', '.active-group-slug', function(e){
        e.preventDefault();

        const active_group		= $('.caldera-group-nav.active');
        const group				= active_group.data('group');
        const group_name			= active_group.find('.caldera-config-group-name').val();
        const group_slug			= active_group.find('.caldera-config-group-slug');
        const group_label			= active_group.find('span');
        let slug_sanitized		= this.value.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase();

        // check its not blank
        if(e.type === 'focusout' && !this.value.length){
            slug_sanitized = group_name.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase();
        }

        group_slug.val(slug_sanitized);
        this.value = slug_sanitized;
    });
    // rename group description
    $('.caldera-editor-body').on('change', '.active-group-desc', function(e){
        e.preventDefault();

        const active_group		= $('.caldera-group-nav.active');
        const group				= active_group.data('group');
        const group_desc			= active_group.find('.caldera-config-group-desc');

        group_desc.val(this.value);
    });

    // set group admin
    $('.caldera-editor-body').on('change', '.active-group-admin', function(e){
        e.preventDefault();

        const active_group		= $('.caldera-group-nav.active');
        const group				= active_group.data('group');
        const group_name			= active_group.find('.caldera-config-group-name').val();
        const group_admin			= active_group.find('.caldera-config-group-admin');
        const group_label			= active_group.find('span');
        const slug_sanitized		= this.value.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase();

        // check its not blank
        if($(this).prop('checked')){
            group_admin.val(1);
            active_group.addClass('is-admin');
        }else{
            group_admin.val(0);
            active_group.removeClass('is-admin');
        }
    });

    // set repeatable
    $('.caldera-editor-body').on('click', '.repeat-config-button', function(e){
        e.preventDefault();
        const active_group		= $('.caldera-group-nav.active');
        const group				= active_group.data('group');
        const icon				= active_group.find('a .group-type');
        const group_repeat		= active_group.find('.caldera-config-group-repeat');
        const clicked				= $(this);

        if(clicked.hasClass('field-edit-open')){
            // set static
            group_repeat.val('0');
            icon.removeClass('icn-repeat').addClass('icn-folder');
            clicked.removeClass('field-edit-open');
        }else{
            // set repeat
            group_repeat.val('1');
            icon.addClass('icn-repeat').removeClass('icn-folder');
            clicked.addClass('field-edit-open');
        }
    });

    // bind delete field
    $('.caldera-editor-body').on('click', '.delete-field', function(){
        const clicked = $(this);
        const field	= clicked.closest('.caldera-editor-field-config-wrapper').prop('id');

        if(!confirm(clicked.data('confirm'))){
            return;
        }
        // remove config
        $(`#${field}`).remove();
        // remove options
        $(`option[value="${field}"]`).remove();
        $(`[data-bind="${field}"]`).remove();

        // remove field
        delete current_form_fields[field];

        $(`[data-config="${field}"]`).slideUp(200, function(){
            const line = $(this);

            // remove line
            line.remove();
            rebuild_field_binding();
            $(document).trigger('field.removed');
        });
    });


    // bind add new group button
    $('.caldera-editor-body').on('click', '.add-new-group,.add-field', function(){

        const clicked		= $(this);

        // remove any hdden fields
        $('.new-group-input').remove();
        $('.new-field-input').remove();

        if( clicked.hasClass( 'add-field' ) ){
            const field_input = $('<input type="text" class="new-field-input block-input">');
            field_input.appendTo( $('.caldera-editor-fields-list ul.active') ).focus();
        }else{
            const group_input = $('<input type="text" class="new-group-input block-input">');
            group_input.appendTo( $('.caldera-editor-groups-panel') ).focus();
        }

    });

    // dynamic group creation
    $('.caldera-editor-body').on('change keypress', '.new-group-input', function(e){
        if(e.type === 'keypress'){
            if(e.which === 13){
                e.preventDefault();
            }else{
                return;
            }
        }


        const group_name 	= this.value;
        const input		= $(this);
        const wrap		= $('.caldera-editor-groups-panel ul');
        const field_list	= $('.caldera-editor-fields-list');
        let new_templ;
        let new_group;

        if( !group_name.length ){
            // no name- just remove the input
            input.remove();
        }else{
            new_templ = Handlebars.compile( $('#caldera_group_line_templ').html() );
            new_group = {
                "id"	:	group_name.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase(),
                "name"	:	group_name,
            };

            // place new group line
            wrap.append( new_templ( new_group ) );

            // create field list
            const new_list = $(`<ul data-group="${new_group.id}">`).hide();

            // place list in fields list
            new_list.appendTo( field_list );

            // init sorting


            // remove input
            input.remove();

            // swtich to new group
            switch_active_group( new_group.id );
        }
    });

    // dynamic field creation
    $('.caldera-editor-body').on('change keypress', '.new-field-input', function(e){
        if(e.type === 'keypress'){
            if(e.which === 13){
                e.preventDefault();
            }else{
                return;
            }
        }


        const new_name 	= this.value;
        const input		= $(this);
        const wrap		= input.parent();
        const field_conf	= $('.caldera-editor-field-config');
        let new_templ;
        let new_conf_templ;
        let new_field;
        const deleter = $('.caldera-config-group-remove');

        if( !new_name.length ){
            // no name- just remove the input
            input.remove();
        }else{
            // hide delete group
            deleter.hide();
            // field line template
            new_templ = Handlebars.compile( $('#caldera_field_line_templ').html() );
            // field conf template
            new_conf_templ = Handlebars.compile( $('#caldera_field_config_wrapper_templ').html() );

            new_field = {
                "id"	:	new_name.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase(),
                "label"	:	new_name,
                "slug"	:	new_name.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase(),
                "group"	:	$('.caldera-group-nav.active').data('group')
            };

            const field = $(new_templ( new_field ));

            // place new field line
            field.appendTo( wrap );
            // pance new conf template
            field_conf.append( new_conf_templ( new_field ) );

            // load field
            field.find('a').trigger('click');

            // remove input
            input.remove();

        }
    });

    // bind slug editing to keep clean
    $('.caldera-editor-body').on('change', '.field-slug', function({type}) {
        if(this.value.length){
            this.value = this.value.split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase();
        }else{
            if(type === 'change'){
                this.value = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-label').val().split(' ').join('_').split('-').join('_').replace(/[^a-z0-9_]/gi, '').toLowerCase();
            }
        }
    });

    // bind add group button
    $('.caldera-editor-body').on('click', '.caldera-add-group', function(e){
        const clicked 	= $(this);
        const group		= clicked.data('group');
        const template	= $(`#${group}_panel_tmpl`).html();

        clicked.parent().parent().append(template);
    });
    // bind remove group button
    $('.caldera-editor-body').on('click', '.caldera-config-group-remove', e => {

        const group = $('.active-group-slug').val();

        $(`[data-group="${group}"]`).hide(0, function(){
            $(this).remove();
            const navs = $('.caldera-group-nav');

            if(navs.length){
                navs.first().find('a').trigger('click');
            }else{
                $('.caldera-editor-fields-panel').hide();
            }
        });

    });

    $('body').on('click', '.set-current-field', function(e){

        e.preventDefault();

        const clicked = $(this);

        $(`#${clicked.data('field')}_type`).val(clicked.data('type')).trigger('change');

        $(`#${clicked.data('field')}_lable`).focus()

        $('#field_setup_baldrickModalCloser').trigger('click');


    });

    $('.caldera-editor-body').on('change record', '.field-config', function(e){
        const field 	= $(this);
        const parent 	= field.closest('.caldera-editor-field-config-wrapper');

        if(!current_form_fields){
            return;
        }
        //
        if(field.prop('id') === `${parent.prop('id')}_lable`){
            // update field bind label
            current_form_fields[parent.prop('id')].label = this.value;
        }
        if(field.prop('id') === `${parent.prop('id')}_slug`){
            // update field bind slug
            current_form_fields[parent.prop('id')].slug = this.value;
        }
        if(field.prop('id') === `${parent.prop('id')}_type`){
            // update field bind type
            current_form_fields[parent.prop('id')].type = this.value;
        }

        if(parent.length){
            build_field_preview(parent.prop('id'));
        }
    });

    $('.caldera-editor-body').on('focus', '.caldera-field-bind', function({type}) {
        const field = $(this);
        const value = this.value;

        if(type && type === 'focusin'){
            field.removeClass('bound_field').addClass('reload-binding');
            rebind_field_bindings();
            this.value = value;
            return;
        }
    });

    $('.caldera-editor-body').on('change', '.caldera-conditional-field-set', function(e){
        const field = $(this);
        const field_compare = field.parent().find('.compare-type');
        const type = field.data('condition');
        const pid = field.data('id');
        const name = `config[${type}][${pid}][conditions][group][${field.data('row')}][${field.data('line')}]`;
        const lineid = field.data('line');
        const target = $(`#${lineid}_value`);
        const curval = target.find('.caldera-conditional-value-field').first();
        let field_wrapper;
        let is_button;
        let options_wrap = [];

        const field_id = this.value;
        if( field_id.substr(0,1) !== '{' ){
            field_wrapper = $(`#${field_id}`);
            is_button = field_wrapper.find( '.field-button-type' );
            options_wrap = field_wrapper.find('.caldera-config-group-toggle-options');
        }
        if(field.hasClass('.bind_init')){
            field.addClass('bound_triggered');
        }
        // check if a value is present


        if( curval.length){
            if(curval.val().length){
                target.data('value', curval.val());
            }
        }else if( 0 === target.val() ){
            target.data('value', 0 );
        }else if( '0' === target.val() ){
            target.data('value', '0' );
        }
        field_compare.show();
        if(options_wrap.length){
            const options_rows = options_wrap.find('.toggle_option_row');
            var out = `<select name="${name}[value]" class="caldera-processor-value-bind caldera-conditional-value-field" data-field="${field_id}" style="max-width: 220px; width: 220px;">`;
            out += '<option value=""></option>';

            options_rows.each((k, v) => {
                const label = $(v).find('.toggle_label_field');
                const value = label.data('option');
                let sel = '';

                if(target.data('value')){
                    if(target.data('value').toString() === value || target.data('value').toString() === $(v).find('.toggle_value_field').val() ){
                        sel = ' selected="selected"';
                    }
                }

                out += `<option value="${value}"${sel}>${label.val()}</option>`;
            })

            out += '</select>';
        }else if( is_button && is_button.val() == 'button' ) {

            field_compare.val('is').hide();


            out = `<select name="${name}[value]" class="caldera-processor-value-bind caldera-conditional-value-field" data-field="${field_id}" style="max-width: 220px; width: 220px;">`;
            out += `<option value="click" ${target.data( 'value' ) === 'click' ? 'selected="selected"' : ''}>Click</option>`;
            out += `<option value="dblclick" ${target.data( 'value' ) === 'dblclick' ? 'selected="selected"' : ''}>Double Click</option>`;
            out += '</select>';

        }else{
            out = `<input name="${name}[value]" type="text" class="caldera-conditional-value-field magic-tag-enabled" value="${target.data('value') ? target.data('value') : ''}" style="max-width: 205px;">`;
        }


        target.html(out);
        init_magic_tags();
    });


    $('.caldera-editor-body').on('change', '.caldera-conditionals-usetype', function(e){

        const select = $(this);

        if( this.value === 'show' || this.value === 'hide' || this.value === 'disable' || this.value === 'use' || this.value === 'not' ){
            $(`#${select.data('id')}_condition_group_add`).show();
            $(`#${select.data('id')}_conditional_wrap`).show();
        }else{
            $(`#${select.data('id')}_condition_group_add`).hide();
            $(`#${select.data('id')}_conditional_wrap`).hide();
        }

    });
    // conditionals
    $('.caldera-editor-body').on('click', '.remove-conditional-line', function(e){
        e.preventDefault();
        const clicked = $(this);
        const line = clicked.closest('.caldera-condition-line');
        const group = clicked.closest('.caldera-condition-group');

        line.remove();
        if(!group.find('.caldera-condition-line').length){
            group.remove();
        }
    });


    // load fist  group
    $('.caldera-group-nav').first().find('a').trigger('click');

    // toggle set values
    $('.caldera-editor-body').on('change', '.toggle_show_values', function(e){
        const $clicked = $(this);
        const $wrap =  $clicked.closest('.caldera-config-group-toggle-options');
        const $labelInputs = $wrap.find('.toggle_label_field');
        const $valueInputs = $wrap.find('.toggle_value_field, .toggle_calc_value_field');
        const $valueLabels = $wrap.find( '.option-setting-label-for-value' );
        const $labelLabel = $wrap.find( '.option-setting-label-for-label' );
        const $optionGroupControl = $wrap.find( '.option-group-control' );

        const inputCss = {
            width: '100%',
            display: 'inline',
            float: 'left',
        };

        if( ! $clicked.prop( 'checked' ) ){
            $valueInputs.hide().attr( 'aria-hidden', true );
            $valueLabels.hide().attr( 'aria-hidden', true );
            $labelInputs.css('width', 245);
            $labelLabel.css( 'display', 'inline' );


        }else{
            $valueInputs.show().css(inputCss).attr( 'aria-hidden', false );
            $labelInputs.show().css(inputCss).attr( 'aria-hidden', false );
            $valueLabels.show().css({
                display: 'inline-block'
            });
            $labelLabel.css( 'display', 'inline' );
        }


        $labelInputs.trigger('toggle.values');
        init_magic_tags();
    });

    // autopopulate
    $('.caldera-editor-body').on('change', '.auto-populate-type', function(){
        $(this).closest('.wrapper-instance-pane').find('.auto-populate-options').trigger('change');
    });
    $('.caldera-editor-body').on('change', '.auto-populate-options', function(){
        const clicked 	= $(this);
        const wrap		= clicked.closest('.wrapper-instance-pane');
        const manual		= wrap.find('.caldera-config-group-toggle-options');
        const autotype_wrap 	= wrap.find('.caldera-config-group-auto-options');
        const autotype		= autotype_wrap.find('.auto-populate-type');

        autotype_wrap.find('.auto-populate-type-panel').hide();

        if(clicked.prop('checked')){
            manual.hide();
            autotype_wrap.show();
        }else{
            manual.show();
            autotype_wrap.hide();
        }

        autotype_wrap.find(`.caldera-config-group-auto-${autotype.val()}`).show();
    });

    $('body').on('change', '.pin-toggle-roles', function(){
        const clicked = $(this);
        const roles = $('#caldera-pin-rules');

        if( clicked.val() === '1' ){
            roles.show();
        }else{
            roles.hide();
        }
    });

    $('body').on('click', '.magic-tag-init', function(e){
        const clicked = $(this);
        const input = clicked.prev();

        input.focus().trigger('init.magic');
    });
    // show magic tag autocompletes
    $('body').on('keyup  focus select click init.magic', '.magic-tag-enabled', function(e){
        init_magic_tags();
        const input = $(this);
        const wrap = input.parent();
        let fieldtype = ( wrap.closest('.wrapper-instance-pane').find('.caldera-select-field-type').val() ? wrap.closest('.wrapper-instance-pane').find('.caldera-select-field-type').val() : 'hidden' );
        let tags = wrap.find('.magic-tags-autocomplete');
        let list = tags.find('ul');
        let stream = this.value;
        const tag = [];
        let type_instances = [];
        let current_tag = '';
        let start = this.selectionStart;
        let end = this.selectionEnd;

        if( tags.length && tags.data('focus') ){
            e.preventDefault();
            return;
        }

        //reset typed tag
        input.data('tag','');
        if(this.selectionEnd > this.selectionStart){
            current_tag = this.value.substr(this.selectionStart, ( this.selectionEnd - this.selectionStart) );
        }else{

            if( ( e.type === 'select' || e.type === 'keyup' ) && ( e.which !== 40 && e.which !== 38 && e.which !== 39 && e.which !== 37 ) ){

                for( start=this.selectionStart; start > 0; start--){

                    var ch = stream.substr(start-1,1);

                    if(ch === ' ' || ch === "\n" || ( ( ch === '%' || ch === '}' ) && this.selectionStart === start ) ){
                        break;
                    }
                }
                for( end=this.selectionStart; end < stream.length; end++){

                    var ch = stream.substr(end,1);

                    if(ch === ' ' || ch === "\n" || ( ( ch === '%' || ch === '{' ) && this.selectionStart === end ) ){
                        break;
                    }
                }

                current_tag = stream.substr(start, ( end - start ) );
            }

        }

        // start matching
        if( e.type !== 'focusout' ){

            if( e.type !== 'init' && current_tag.length < 3 ){
                if(tags.length){
                    tags.remove();
                }

            }
            if(!tags.length){
                tags = $('<div class="magic-tags-autocomplete"></div>');
                list = $('<ul></ul>');
                list.appendTo(tags);
                tags.insertAfter(input);
                tags.on('mouseenter', function(){
                    $(this).data('focus', true);
                });
                tags.on('mouseleave', function(){
                    $(this).data('focus', false);

                    setTimeout(() => {
                        tags.remove();
                    }, 200);
                    if( !input.is(':focus') ){
                        input.trigger('focusout');
                    }
                });
            }

            //populate
            list.empty();
            // compatibility
            let tagtypes = 'system';
            let is_static = false;
            if(fieldtype === 'hidden' || fieldtype === 'dropdown' || fieldtype === 'radio' || fieldtype === 'toggle_switch' || fieldtype === 'checkbox'){
                is_static = true;
                fieldtype = 'text';
                tagtypes = 'all';
            }else if(fieldtype === 'paragraph' || fieldtype === 'html'){
                fieldtype = 'text';
            }
            // type set
            if(input.data('type')){
                fieldtype = input.data('type');
            }
            // search em!
            fieldtype = fieldtype.split(',');
            fieldtype.push('vars');
            for( let ft = 0; ft < fieldtype.length; ft++){
                for( const tp in system_values ){
                    if(typeof system_values[tp].tags === 'undefined' || typeof system_values[tp].tags[fieldtype[ft]] === 'undefined'){
                        continue;
                    }

                    type_instances = [tp];
                    if(tp !== 'system' && tp !== 'variable' && tp !== 'field'){
                        const type_instance_confs = jQuery(`.processor-${tp}`);
                        const wrapper = input.closest('.caldera-editor-processor-config-wrapper');
                        const wrapper_id = wrapper.prop('id');
                        type_instances = [];
                        // processor based - orderd
                        for(let c = 0; c<type_instance_confs.length; c++){
                            if(!wrapper.length && is_static === true ){
                                // static non processor - can be used
                                type_instances.push(type_instance_confs[c].id);
                            }else{
                                if(wrapper_id === type_instance_confs[c].id){
                                    continue;
                                }

                                // check index order is valid
                                if(jQuery(`li.${type_instance_confs[c].id}`).index() < jQuery(`li.${wrapper_id}`).index()){
                                    type_instances.push(type_instance_confs[c].id);
                                }
                            }

                        }
                    }
                    // all instances of tag
                    for( let instance = 0; instance < type_instances.length; instance++){
                        if(tagtypes === 'all' || tagtypes === tp || tp === 'variable'){
                            const heading = $(`<li class="header">${system_values[tp].type}${instance > 0 ? ` [${instance+1}]` : ''}</li>`);
                            let matches = 0;
                            heading.appendTo(list);

                            for( let i = 0; i < system_values[tp].tags[fieldtype[ft]].length; i++){

                                if(input.data('parent')){
                                    if(`variable:${input.data('parent')}` === system_values[tp].tags[fieldtype[ft]][i]){
                                        continue;
                                    }
                                }

                                let this_tag = system_values[tp].wrap[0] + system_values[tp].tags[fieldtype[ft]][i]+system_values[tp].wrap[1];
                                if(type_instances[instance] !== tp && type_instances.length > 1){
                                    this_tag = `${system_values[tp].wrap[0] + system_values[tp].tags[fieldtype[ft]][i]}:${type_instances[instance]}${system_values[tp].wrap[1]}`;
                                }
                                if( this_tag.includes(current_tag) || e.type === 'init'){

                                    matches += 1;
                                    const view_tag = this_tag.replace(current_tag, `<strong>${current_tag}</strong>`);

                                    const linetag = $(`<li class="tag" data-tag="${this_tag}">${view_tag}</li>`);


                                    linetag.on('click', function(){

                                        const selected = $(this).data('tag');


                                        input.val( stream.substr(0, start ) + selected + stream.substr( end ) ).trigger('change').focus();
                                        input[0].selectionStart = start + selected.length - ( selected.indexOf('*') > 0 ? 2 : 0 );
                                        input[0].selectionEnd = start + selected.length - ( selected.indexOf('*') > 0 ? 1 : 0 );
                                        end = start = input[0].selectionEnd;
                                        stream += selected;
                                        input.trigger('init.magic');
                                    });

                                    linetag.appendTo(list);
                                }
                            }
                            if(matches === 0){
                                heading.remove();
                            }
                        }
                    }
                }
            }
        }
        // count results found
        if(!list.children().length){
            tags.remove();
        }

        // focus out - remove
        if(e.type === 'focusout'){

            setTimeout(() => {
                tags.remove();
            }, 200);
        }
    });

    // precompile tempaltes
    pre_compile_templates();
    //compiled_templates

    // build configs on load:
    // allows us to keep changes on reload as not to loose settings on accidental navigation
    $('.caldera-select-field-type').not('.field-initialized').each((k, v) => {
        build_fieldtype_config(v);
    });

});//

var rebuild_field_binding;
var rebind_field_bindings;
var current_form_fields = {};
let required_errors = {};
let add_new_grid_page;
let add_page_grid;
var init_magic_tags;
var core_form;
var compiled_templates = {};

init_magic_tags = () => {
    //init magic tags
    const magicfields = jQuery('.magic-tag-enabled');

    magicfields.each((k, v) => {
        const input = jQuery(v);

        if(input.hasClass('magic-tag-init-bound')){
            const currentwrapper = input.parent().find('.magic-tag-init');
            if(!input.is(':visible')){
                currentwrapper.hide();
            }else{
                currentwrapper.show();
            }
            return;
        }else{
            const magictag = jQuery('<span class=""></span>');
            const wrapper = jQuery('<span style="position:relative;display:inline-block; width:100%;"></span>');

            if(input.is('input')){
                magictag.css('borderBottom', 'none');
            }

            if(input.hasClass('caldera-conditional-value-field')){
                wrapper.width('auto');
            }

            input.wrap(wrapper);
            magictag.insertAfter(input);
            input.addClass('magic-tag-init-bound');
            if(!input.is(':visible')){
                magictag.hide();
            }else{
                magictag.show();
            }
        }
    });

}

rebuild_field_binding = () => {

    // check form is loaded first
    if(!core_form){
        core_form = jQuery('.caldera-forms-options-form');
    }


    if(!core_form.hasClass('builder-loaded')){
        return;
    }

    const fields = jQuery('.caldera-editor-field-config-wrapper');//.not('.bound_field');

    // set object
    system_values.field = {
        tags	:	{
            text	:	[]
        },
        type	:	"Fields",
        wrap	:	['%','%']
    };

    // each field
    for(let f = 0; f < fields.length; f++){
        const field_id = fields[f].id;
        const label = jQuery(`#${field_id}_lable`).val();
        const slug = jQuery(`#${field_id}_slug`).val();
        const type = jQuery(`#${field_id}_type`).val();


        if(typeof system_values.field.tags[type] === 'undefined'){
            system_values.field.tags[type] = [];
        }
        system_values.field.tags[type].push( slug );
        if(type !== 'text'){
            system_values.field.tags.text.push( slug );
        }

        current_form_fields[field_id] = {
            label,
            slug,
            type
        };

        // bind names
        jQuery('option.bound-field').trigger('change').each((k, v) => {
            const bind = jQuery(v);
            if(bind.prop('value').indexOf('{') !== 0){
                bind.text( `${jQuery(`#${bind.prop('value')}_lable`).val()} [${jQuery(`#${bind.prop('value')}_lable`).val()}]`);
            }else{
                bind.text(bind.prop('value').replace('{','').replace('}',''));
            }
        }).removeClass('bound-field');
    };

};

rebind_field_bindings = () => {
    //return;
    const bindings = jQuery('.caldera-field-bind').not('.bound_field');

    let type_instances;
    let processor_li;



    if(!bindings.length){
        return;
    }

    bindings.addClass('bound_field');


    for(let v = 0; v < bindings.length; v++){
        const field = jQuery(bindings[v]);
        let current = field.val();
        const default_sel = field.data('default');
        const excludes = field.data('exclude');
        let count = 0;
        const wrapper = field.closest('.caldera-editor-processor-config-wrapper');
        const wrapper_id = wrapper.prop('id');
        let valid = '';

        if(default_sel && !field.hasClass('reload-binding')){
            current = default_sel;
        }

        if(field.is('select')){
            field.empty();

            var optgroup = jQuery('<optgroup label="Fields">');
            for(const fid in current_form_fields){
                if(field.data('type')){
                    if(!field.data('type').split(',').includes(current_form_fields[fid].type)){
                        continue;
                    }

                }
                // check this field is not the same
                if( field.data('id') !== fid ){
                    optgroup.append(`<option value="${fid}"${current === fid ? 'selected="selected"' : ''}>${current_form_fields[fid].label} [${current_form_fields[fid].slug}]</option>`);
                }

                count += 1;
            }
            optgroup.appendTo(field);
            // system values
            if(count === 0){
                field.empty();
            }

            for(const type in system_values){
                type_instances = [];

                if(excludes){
                    if( excludes.split(',').includes(type) ){
                        continue;
                    }
                }

                if(type !== 'system' && type !== 'variable'){

                    const type_instance_confs = jQuery(`.processor-${type}`);

                    for(let c = 0; c<type_instance_confs.length; c++){
                        if(wrapper_id === type_instance_confs[c].id){
                            continue;
                        }

                        type_instances.push(type_instance_confs[c].id);
                        if(type_instance_confs.length > 1){
                            if(processor_li = jQuery(`li.${type_instance_confs[c].id} .processor-line-number`)){
                                processor_li.html(`[${c + 1}]`);
                            }
                        }

                    }
                }else{
                    type_instances.push('__system__');
                }

                let types = [];
                if(field.data('type')){
                    types = field.data('type').split(',');
                    types.push('vars');
                }else{
                    types = ['text','vars'];
                }

                for(let t = 0; t<types.length; t++){
                    if( system_values[type].tags && system_values[type].tags[types[t]]){

                        for( let instance = 0; instance < type_instances.length; instance++){

                            // check index order is valid
                            if(jQuery(`li.${type_instances[instance]}`).index() > jQuery(`li.${wrapper_id}`).index() && type_instances[instance] !== '__system__'){
                                if( field.closest('.caldera-editor-processors-panel-wrap').length ){
                                    valid = ' disabled="disabled"';
                                }
                            }else{
                                valid = '';
                            }


                            var optgroup = jQuery(`<optgroup label="${system_values[type].type}${type_instances[instance] !== '__system__' ? ` ${jQuery(`li.${type_instances[instance]}`).find('.processor-line-number').html()}` : ''}"${valid}>`);

                            for( let i = 0; i < system_values[type].tags[types[t]].length; i++){

                                let bind_value = system_values[type].tags[types[t]][i];
                                // update labels on multiple
                                if(type_instances[instance] !== '__system__'){
                                    bind_value = bind_value.replace(type ,type_instances[instance]);
                                }

                                optgroup.append(`<option value="\{${bind_value}\}"${current === `{${bind_value}}` ? 'selected="selected"' : ''}${valid}>${system_values[type].tags[types[t]][i]}</option>`);

                                count += 1;
                            }

                            if(optgroup.children().length){
                                optgroup.appendTo(field);
                            }

                        }

                    }
                }

            }
            if(count === 0){
                field.empty();
                if(field.data('type')){
                    field.append(`<option value="">No ${field.data('type').split(',').join(' or ')} in form</option>`);
                    var no_options = true;
                }
            }else{
                field.prop('disabled', false);
            }

            if(!field.hasClass('required') && typeof no_options === 'undefined'){
                field.prepend('<option value=""></option>');
            }
            field.val(current);
        }
    }

    init_magic_tags();
    jQuery(document).trigger('bound.fields');
    jQuery('.caldera-header-save-button').prop("disabled", false);
    if( undefined != typeof  cf_revisions_ui ){
        cf_revisions_ui();
    }
};

function setup_field_type({trigger}) {

    return {'id' : trigger.prop('id')};
}



function check_required_bindings(el){
    let fields;
    const savebutton = jQuery('.caldera-header-save-button');
    const field_elements = jQuery('.layout-form-field');
    const nav_elements = jQuery('.caldera-processor-nav');
    let all_clear = true;

    if(el){
        fields = jQuery(el);
    }else{
        fields = jQuery('.caldera-config-field .required');
    }

    fields.removeClass('has-error');
    field_elements.removeClass('has-error');
    nav_elements.removeClass('has-error');

    jQuery('.error-tag').remove();
    //reset list
    required_errors = {};

    fields.each((k, v) => {
        var field = jQuery(v);
        const panel = field.closest('.caldera-config-editor-panel');

        if(!v.value.length){
            if(!required_errors[panel.prop('id')]){
                required_errors[panel.prop('id')] = 0;
            }

            var is_field = field.closest('.caldera-editor-field-config-wrapper');
            var is_process = field.closest('.caldera-editor-processor-config-wrapper');

            if(is_field.length){
                jQuery(`.layout-form-field[data-config="${is_field.prop('id')}"]`).addClass('has-error');
            }
            if(is_process.length){
                jQuery(`.${is_process.prop('id')}`).addClass('has-error');
            }
            required_errors[panel.prop('id')] += 1;
            field.addClass('has-error');

            all_clear = false;
        }else{
            //unique
            if( field.hasClass('field-slug') ){
                const slugs = jQuery('.field-slug').not(field);

                for(var s = 0; s < slugs.length; s++){
                    if( slugs[s].value === v.value ){
                        var field = jQuery(slugs[s]);

                        if(!required_errors[panel.prop('id')]){
                            required_errors[panel.prop('id')] = 0;
                        }
                        var is_field = field.closest('.caldera-editor-field-config-wrapper');
                        var is_process = field.closest('.caldera-editor-processor-config-wrapper');

                        if(is_field.length){
                            jQuery(`.layout-form-field[data-config="${is_field.prop('id')}"]`).addClass('has-error');
                        }
                        if(is_process.length){
                            jQuery(`.${is_process.prop('id')}`).addClass('has-error');
                        }
                        required_errors[panel.prop('id')] += 1;
                        field.addClass('has-error');
                        all_clear = false;
                        break;
                    }
                };
            }
            if( field.hasClass('toggle_value_field') ){
                const vals = field.closest('.caldera-config-group').find('.toggle_value_field').not(field);

                for(var s = 0; s < vals.length; s++){
                    if( vals[s].value === v.value ){
                        var field = jQuery(vals[s]);

                        if(!required_errors[panel.prop('id')]){
                            required_errors[panel.prop('id')] = 0;
                        }
                        var is_field = field.closest('.caldera-editor-field-config-wrapper');
                        var is_process = field.closest('.caldera-editor-processor-config-wrapper');

                        if(is_field.length){
                            jQuery(`.layout-form-field[data-config="${is_field.prop('id')}"]`).addClass('has-error');
                        }
                        if(is_process.length){
                            jQuery(`.${is_process.prop('id')}`).addClass('has-error');
                        }
                        required_errors[panel.prop('id')] += 1;
                        field.addClass('has-error');
                        all_clear = false;
                        break;
                    }
                };
            }
        }
    });

    for(const t in required_errors){
        jQuery('.caldera-forms-options-form').find(`a[href="#${t}"]`).append(`<span class="error-tag">${required_errors[t]}</span>`);
    }

    jQuery('.caldera-conditional-field-set').trigger('change');

    return all_clear;
}

jQuery(document).ready($ => {
    add_new_grid_page = obj => ({
        "page_no" : `pg_${Math.round( Math.random() * 10000000 )}`
    })

    add_page_grid = ({rawData, params}) => {
        const btn_count = $('.page-toggle').length + 1;
        const button = $(`<button type="button" data-name="Page ${btn_count}" data-page="${rawData.page_no}" class="page-toggle button">${params.trigger.data('addtitle')} ${btn_count}</button> `);
        const option_tab = $('#page-toggles');
        button.appendTo( option_tab );
        option_tab.show();
        buildSortables();
        button.trigger('click');
        if( btn_count === 1){
            option_tab.hide();
        }
        $(document).trigger('add.page');
    }

    // bind pages tab
    $(document).on('remove.page add.page load.page', e => {
        const btn_count = $('.page-toggle').length;
        const pages_tab = $('#tab_pages');

        if(btn_count <= 1){
            pages_tab.hide();
        }else{
            pages_tab.show();
        }
    });

    function buildLayoutString(){
        const grid_panels = $('.layout-grid-panel');
        let row_index = 0;

        grid_panels.each((pk, pv) => {
            const panel= $(pv);
            const capt = panel.find('.layout-structure');
            const rows = panel.find('.row');
            const struct = [];

            rows.each((k, v) => {
                const row = $(v);
                const cols = row.children().not('.column-merge');
                const rowcols = [];
                row_index += 1;
                cols.each((p, c) => {
                    let span = $(c).attr('class').split('-');
                    rowcols.push(span[2]);
                    const fields = $(c).find('.field-location');
                    if(fields.length){
                        fields.each((x, f) => {
                            const field = $(f);
                            field.val( `${row_index}:${p+1}` ).removeAttr('disabled');
                        });
                    }
                    // set name

                });
                struct.push(rowcols.join(':'));
            });
            capt.val(struct.join('|'));
        });
    }

    function insert_new_field(newfield, target, field_default){
        const name = `fld_${Math.round( Math.random() * 10000000 )}`;
        const new_name 	= name;
        const field_conf	= $('#field_config_panels');
        let new_conf_templ;
        let field_set;

        newfield.prop('id', '').prop('title', '');

        // field conf template
        new_conf_templ = Handlebars.compile( $('#caldera_field_config_wrapper_templ').html() );

        field_set = $.extend({},{
            "id"	:	new_name,
            "label"	:	'',
            "slug"	:	''
        }, field_default );
        // reset slug to blank
        field_set.slug = '';
        // pance new conf template
        field_conf.append( new_conf_templ( field_set ) );

        newfield.
        removeClass('button-small').
        removeClass('button').
        removeClass('button-primary').
        removeClass('ui-draggable').
        removeClass('layout-new-form-field').
        addClass('layout-form-field').
        attr('data-config', name).css({ 'display' : '', 'opacity' : '' });

        newfield.find('.layout_field_name').remove();
        newfield.find('.field-location').prop('name', `config[layout_grid][fields][${name}]`);
        newfield.find('.settings-panel').show();
        newfield.appendTo( target );
        buildSortables();
        newfield.find('.icon-edit').trigger('click');


        $(`#${name}_lable`).focus().select();
        baldrickTriggers();
        $(document).trigger('field.added');
        if( field_default ){
            $(`#${new_name}_type` ).data('type', field_set.type ).trigger('change');
        }else{
            $(`#${name}`).trigger('field.drop');
        }
        rebuild_field_binding();
    }

    function buildSortables(){

        // Sortables
        $('.toggle-options').sortable({
            handle: ".dashicons-sort",
        });


        $( "#grid-pages-panel" ).sortable({
            placeholder: 	"row-drop-helper",
            handle: 		".sort-handle",
            items:			".first-row-level",
            axis: 			"y",
            stop() {
                buildLayoutString();
            }
        });
        $( ".layout-column" ).sortable({
            connectWith: 	".layout-column",
            appendTo: 		"#grid-pages-panel",
            helper: 		"clone",
            items:			".layout-form-field",
            handle:			".drag-handle",
            cursor: 		"move",
            opacity: 		0.7,
            cursorAt: 		{left: 100, top: 15},
            start(e, {helper}) {
                helper.css({width: '200px', height: '35px', paddingTop: '20px'});
            },
            stop(e, {item}) {
                item.removeAttr('style');
                buildLayoutString();
            }
        });

        // Draggables
        $( "h3 .layout-new-form-field" ).draggable({
            helper: "clone",
            appendTo: "body"
        });
        $('.page-toggle.button').droppable({
            accept: ".layout-form-field",
            over(e, ui) {
                $(this).trigger('click');
                $( ".layout-column" ).sortable("refresh");
            }
        });
        // Tools Bar Items
        $( ".layout-column" ).droppable({
            greedy: true,
            activeClass: "ui-state-dropper",
            hoverClass: "ui-state-hoverable",
            accept: ".layout-new-form-field",
            drop(event, {draggable}) {
                const newfield= draggable.clone();
                const target = $(this);

                insert_new_field(newfield, target);
            }
        });


        buildLayoutString();
    }
    buildSortables();

    $('#grid-pages-panel').on('click','.column-fieldinsert .dashicons-plus-alt', function(e){
        //newfield-tool
        const target 		= $(this).closest('.column-container');

        const newfield 	= $('#newfield-tool').clone().css('display', '');

        insert_new_field(newfield, target);
    });

    $('#grid-pages-panel').on('click','.column-split', function(e){
        const column = $(this).parent().parent();
        const size = column.attr('class').split('-');
        const newcol = $('<div>').insertAfter(column);
        const left = Math.ceil(size[2]/2);
        const right = Math.floor(size[2]/2);


        size[2] = left;
        column.attr('class', size.join('-'));
        size[2] = right;
        newcol.addClass(size.join('-')).append('<div class="layout-column column-container">');
        $(this).remove();
        buildSortables();

        jQuery('.column-tools').remove();
        jQuery('.column-merge').remove();
    });
    $( "#grid-pages-panel" ).on('click', '.column-remove', function(e){
        const row = $(this).closest('.row');
        const fields = row.find('.layout-form-field');
        const wrap = row.closest('.layout-grid-panel');

        //find fields
        if(fields.length){
            if(!confirm($('#row-remove-fields-message').text())){
                return;
            }
            fields.each((k, v) => {
                const field_id = $(v).data('config');
                $(`#${field_id}` ).remove();
                // remove options
                $(`option[value="${field_id}"]`).remove();
                $(`[data-bind="${field_id}"]`).remove();

                // remove field
                delete current_form_fields[field_id];

            });
        }
        //return;

        row.slideUp(200, function(){
            $(this).remove();
            buildLayoutString();
            rebuild_field_binding();
            if(!wrap.find('.row').length){
                wrap.remove();
                const btn = $('#page-toggles .button-primary');
                const prev = btn.prev();
                const next = btn.next();

                btn.remove();
                if(prev.length){
                    prev.trigger('click');
                }else{
                    next.trigger('click');
                }
            }
            $(document).trigger('remove.page');
        });

        jQuery('.column-tools').remove();
        jQuery('.column-merge').remove();
    });

    $( ".caldera-config-editor-main-panel" ).on('click', '.caldera-add-row', e => {
        e.preventDefault();
        const wrap = $('.page-active');
        if(!wrap.length){
            $('.caldera-add-page').trigger('click');
            return;
        }
        const new_row = $( '<div style="display:none;" class="first-row-level row"><div class="col-xs-12"><div class="layout-column column-container"></div></div></div>' );

        $('.page-active').append( new_row );
        new_row.slideDown( 200 );
        buildSortables();
        buildLayoutString();
    });

    $( "#grid-pages-panel" ).on('click', '.column-join', function(e){
        const column = $(this).parent().parent().parent();

        const prev 		= column.prev();
        const left 		= prev.attr('class').split('-');
        const right 		= column.attr('class').split('-');
        left[2]		= parseFloat(left[2])+parseFloat(right[2]);


        column.find('.layout-column').contents().appendTo(prev.find('.layout-column'));
        prev.attr('class', left.join('-'));
        column.remove();
        buildLayoutString();
        jQuery('.column-tools').remove();
        jQuery('.column-merge').remove();
    });

    $('#grid-pages-panel').on('mouseenter','.row', function(e){
        const setrow = jQuery(this);
        jQuery('.column-tools,.column-merge').remove();
        setrow.children().children().first().append(`<div class="column-remove column-tools" data-placement="top" title="${CF_ADMIN_TOOLTIPS.delete_row}" ><i class="icon-remove"></i></div>`);
        setrow.children().children().last().append('<div class="column-sort column-tools" style="text-align:right;"><i class="dashicons dashicons-menu drag-handle sort-handle"></i></div>');

        setrow.children().children().not(':first').prepend('<div class="column-merge"><div class="column-join column-tools"><i class="icon-join"></i></div></div>');
        const single = setrow.parent().parent().parent().width()/12-1;
        setrow.children().children().each((k, v) => {
            const column = $(v);
            const width = column.width()/2-5;
            column.prepend(`<div class="column-fieldinsert column-tools"><i class="dashicons dashicons-plus-alt" data-toggle="tooltip" data-placement="top" title="${CF_ADMIN_TOOLTIPS.add_field_row}"></i></div>`);
            if(!column.parent().hasClass('col-xs-1')){
                column.prepend(`<div class="column-split column-tools" data-placement="top" title="${CF_ADMIN_TOOLTIPS.split_row}"><i class="dashicons dashicons-leftright"></i></div>`);
                column.find('.column-split').css('left', width);
            }
        });

        jQuery( ".column-merge" ).draggable({
            axis: "x",
            helper: "clone",
            appendTo: setrow,
            grid: [single, 0],
            drag(e, {helper, originalPosition}) {
                $(this).addClass('dragging');
                $('.column-tools').remove();
                $('.column-split').remove();
                const column = $(this).parent().parent();
                const dragged = helper;
                const direction = (originalPosition.left > dragged.position().left) ? 'left' : 'right';
                const step = 0;
                const prev = column.prev();
                const single = Math.round(column.parent().width()/12-10);
                const distance = Math.abs(originalPosition.left - dragged.position().left);

                column.parent().addClass('sizing');

                if(distance >= single){
                    const left 		= prev.attr('class').split('-');
                    const right 		= column.attr('class').split('-');

                    left[2]		= parseFloat(left[2]);
                    right[2]	= parseFloat(right[2]);

                    if(direction === 'left'){
                        left[2]--;
                        right[2]++;
                        if(left[2] > 0 && left[2] < (left[2]+right[2]) ){
                            prev.attr('class', left.join('-'));
                            column.attr('class', right.join('-'));
                            originalPosition.left = dragged.position().left;
                        }else{
                            $(this).draggable( "option", "disabled", true );
                        }
                    }else{
                        left[2]++;
                        right[2]--;
                        if(right[2] > 0 && right[2] < (right[2]+right[2]) ){
                            prev.attr('class', left.join('-'));
                            column.attr('class', right.join('-'));
                            originalPosition.left = dragged.position().left;
                        }else{
                            $(this).draggable( "option", "disabled", true );
                        }

                    }
                    buildLayoutString();
                }
            },
            stop() {
                $(this).removeClass('dragging').parent().parent().parent().removeClass('sizing');
            }
        });
    });
    $('#grid-pages-panel').on('mouseleave','.row', e => {
        jQuery('.column-tools').remove();
        jQuery('.column-merge').remove();
    });

    $('#grid-pages-panel').on('click', '.layout-form-field .icon-remove', function(){
        const clicked = $(this);
        const panel = clicked.parent();
        const config = $(`#${panel.data('config')}`);

        panel.slideUp(100, function(){
            $(this).remove();
        });
        config.slideUp(100, function(){
            $(this).remove();
        });
    });
    $( document ).on('click', '.layout-form-field .dashicons-admin-page', function(){
        const clicked = $( this );
        const wrap = clicked.parent();
        const clone_id = wrap.data('config');
        const clone = $(`#${clone_id}` ).formJSON();
        const target 		= clicked.closest('.column-container');
        const newfield 	= wrap.clone().css('display', '');
        let new_params = {};

        if( wrap.hasClass('field-edit-open') ){
            wrap.removeClass('field-edit-open');
            newfield.removeClass('field-edit-open');
            $('.caldera-editor-field-config-wrapper').hide();
        }

        if( clone.config.fields[ clone_id ] ){
            new_params = clone.config.fields[ clone_id ];
            delete new_params.ID;
        }

        insert_new_field(newfield, target, new_params);
    })
    $( document ).on('click', '.layout-form-field .icon-edit', function(){
        const clicked = $(this);
        const panel 	= clicked.parent();
        const type 	= $(`#${panel.data('config')}_type`).val();

        $('.caldera-editor-field-config-wrapper').hide();

        if(panel.hasClass('field-edit-open')){
            panel.removeClass('field-edit-open');
        }else{
            $('.layout-form-field').removeClass('field-edit-open');
            panel.addClass('field-edit-open');
            $(`#${panel.data('config')}`).show();
        }

        $(document).trigger(`show.${panel.data('config')}`);
        $(document).trigger('show.fieldedit');

        if( type === 'radio' || type === 'checkbox' || type === 'dropdown' || type === 'toggle_switch' ){
            $(`#${panel.data('config')}_auto`).trigger('change');
        }
    });
    $('body').on('click', '.layout-modal-edit-closer,.layout-modal-save-action', function(e){
        e.preventDefault();

        const clicked = $(this);
        const panel = $('.layout-form-field.edit-open');
        const modal = clicked.closest('.layout-modal-container');
        settings = modal.find('.settings-panel').first();

        $('.edit-open').removeClass('edit-open');
        settings.appendTo(panel.find('.settings-wrapper')).hide();

        modal.hide();
    });

    // clear params
    $('.layout-editor-body').on('change', '.layout-core-pod-query', function(){
        $(this).parent().find('.settings-panel-row').remove();
        $('.edit-open').find('.drag-handle .set-pod').html(` - ${$(this).val()}`);
    });
    $('.layout-editor-body').on('click', '.remove-where', function(){
        $(this).closest('.settings-panel-row').remove();
    });
    // load pod fields
    $('.layout-editor-body').on('click', '.use-pod-container', function(){
        const clicked = $(this);
        const podselect = clicked.prev();
        const pod	= podselect.val();
        const container = '';

        if(!pod.length){
            return;
        }

        $('.edit-open').find('.drag-handle .set-pod').html(` - ${podselect.val()}`);

        clicked.parent().parent().find('.spinner').css('display', 'inline-block');

        const data = {
            'action'	:	'pq_loadpod',
            'pod_reference'	:	{
                'pod'	:	pod
            }
        };

        $.post( cfAdminAJAX, data, res => {
            clicked.parent().find('.spinner').css('display', 'none');

            let template = $('#where-line-tmpl').html();
            let fields = '';
            const container = clicked.closest('.settings-panel').data('container');



            for(const i in res){
                fields += `<option value="${res[i]}">${res[i]}</option>`;
            }
            template = template.replace(/{{fields}}/g, fields).replace(/{{container_id}}/g, container);

            clicked.parent().append( template );
        });
    });

    // edit row
    $('.caldera-editor-header').on('click', '.column-sort .icon-edit', e => {

    });
    // bind tray stuff
    $('.layout-editor-body').on('tray_loaded', '.layout-template-tray', () => {
        buildSortables();
    });
    // build panel navigation
    $('.caldera-editor-header').on('click', '.caldera-editor-header-nav a', function(e){
        e.preventDefault();

        const clicked = $(this);

        // remove active tab
        $('.caldera-editor-header-nav li').removeClass('active');

        // hide all tabs
        $('.caldera-editor-body').hide();

        // show new tab
        $( clicked.attr('href') ).show();

        // set active tab
        clicked.parent().addClass('active');
        rebind_field_bindings();
    });

    $('body').on('change', '.required', function(){
        check_required_bindings(this);
    });

    // prevent error forms from submiting
    $('body').on('submit', '.caldera-forms-options-form', e => {
        const errors = $('.required.has-error');
        if(errors.length){
            e.preventDefault();
        }
    });


    //toggle_option_row
    $('.caldera-editor-body').on('click', '.add-toggle-option', function(e){
        const clicked		= $(this);

        if(clicked.data('bulk')){
            $(clicked.data('bulk')).toggle();
            $(clicked.data('bulk')).find('textarea').focus();
            return;
        }

        const wrapper		= clicked.closest('.caldera-editor-field-config-wrapper');
        const toggle_rows	= wrapper.find('.toggle-options');
        const row			= $('#field-option-row-tmpl').html();
        const template	= Handlebars.compile( row );
        const key			= `opt${parseInt( ( Math.random() + 1 ) * 0x100000 )}`;

        const config		= {
            _name	:	`config[fields][${wrapper.prop('id')}][config]`,
            option	: {}
        };

        if(clicked.data('options')){
            const batchinput 	= $(clicked.data('options'));
            var batch 		= batchinput.val().split("\n");
            let has_vals 	= false;
            for( let i = 0; i < batch.length; i ++){
                let label = batch[i];
                let val = label;
                const parts = val.split('|');
                if( parts.length > 1 ){
                    val = parts[0];
                    label = parts[1];
                    has_vals = true;
                }
                config.option[`opt${parseInt( ( Math.random() + i ) * 0x100000 )}`] = {
                    value	:	val,
                    calc_value: val,
                    label,
                    default	:	false
                }
            }
            $(clicked.data('options')).parent().hide();
            batchinput.val('');
            if( true === has_vals ){
                wrapper.find('.toggle_show_values').prop( 'checked', true );
            }else{
                wrapper.find('.toggle_show_values').prop( 'checked', false );
            }
            toggle_rows.empty();
        }else{
            // add new option
            config.option[key]	=	{
                value	:	'',
                label	:	'',
                calc_value: '',
                default :	false
            };
        }
        $('.preset_options').val('');
        // place new row
        toggle_rows.append( template( config ) );
        wrapper.find('.toggle_show_values').trigger('change');


        $('.toggle-options').sortable({
            handle: ".dashicons-sort"
        });
        if(!batch){
            toggle_rows.find('.toggle_label_field').last().focus();
        }
    });
    // presets
    $('.caldera-editor-body').on('change', '.preset_options', function(e){
        const select = $( this );
        const preset = select.val();
        const batch = $( select.data('bulk') );

        if( preset_options && preset_options[ preset ] && preset_options[ preset ].data ){
            if( typeof preset_options[ preset ].data === 'object' ){
                if( preset_options[ preset ].data.length ){
                    preset_options[ preset ].data = preset_options[ preset ].data.join("\n");
                }else{

                }
            }
            batch.val( preset_options[ preset ].data );
        }
    });
    // remove an option row
    $('.caldera-editor-body').on('click', '.toggle-remove-option', function(e){
        const triggerfield = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-config').first();
        $(this).parent().remove();
        triggerfield.trigger('change');
        $(document).trigger('option.remove');
    });

    $('.caldera-editor-body').on('click', '.page-toggle', function(e){
        const clicked = $(this);
        const wrap = clicked.parent();
        const btns = wrap.find('.button');

        btns.removeClass('button-primary');
        $('.layout-grid-panel').hide().removeClass('page-active');
        $(`#${clicked.data('page')}`).show().addClass('page-active');
        clicked.addClass('button-primary');
        //reindex
        btns.each((k, v) => {
            $(v).html(`${wrap.data('title')} ${k+1}` );
        });
        if(btns.length === 1){
            wrap.hide();
        }
    });

    $('.caldera-editor-body').on('blur toggle.values', '.toggle_label_field', function(e){
        const $label = $(this);
        const $value = $( `.toggle_value_field[data-opt="${$label.data( 'option' )}"]` );

        if( $value.is( ':visible' ) ){
            return;
        }

        $value.val( $label.val() );
    });


    $( document ).on('change focusout', '.toggle_value_field', () => {
        $( document ).trigger('show.fieldedit');
    });

    $( document ).on('show.fieldedit option.remove', e => {
        $('.toggle_value_field.has-error').removeClass('has-error');
        const field = $( `#${$('.layout-form-field.field-edit-open').data('config')}` );
        const options = field.find('.toggle_value_field');
        const notice = field.find('.notice');
        let count = 0;

        for( let i = 0; i < options.length; i++ ){
            const option = options[ i ].value;
            let repeats = 0;
            for( let f = 0; f < options.length; f++ ){
                if( options[ i ] === options[ f ] ){
                    continue;
                }

                if( options[ i ].value === options[ f ].value ){
                    $( options[ f ] ).addClass('has-error');
                    repeats++;
                }
            }
            if( repeats > 0 ){
                $( options[ i ] ).addClass('has-error');
                count++;
            }
        }

        if( count > 0 ){
            notice.slideDown();
            e.preventDefault();
        }else{
            notice.slideUp();
        }
    });

    let is_pulsating = false;
    let pulsing_adders;

    const focus_initial_field = e => {
        const field = $('.layout-grid-panel .icon-edit').first();
        if( field.length ){
            field.trigger('click');
        }else{
            $('.layout-column.column-container').first().trigger('mouseover');
            is_pulsating = setInterval( pulsate_adders, 500 );
        }
        $( document ).off('load.page', focus_initial_field );
    };
    $( document ).on('load.page', focus_initial_field );
    function pulsate_adders(){

        if( is_pulsating ){
            const adders = $('.column-fieldinsert');
            if( adders.length ){
                adders.stop().fadeToggle(700);
                $('.layout-new-form-field').stop().fadeToggle(700);
            }else{
                cf_clear_puler();
            }
        }
    }

    const cf_clear_puler = () => {
        if( is_pulsating ){
            clearTimeout( is_pulsating );
            $(document).off('mouseover', '.layout-new-form-field, .column-fieldinsert', cf_clear_puler);
        }
        $('.layout-new-form-field, .column-fieldinsert').fadeIn();
    };
    $(document).on('mouseover', '.layout-new-form-field, .column-fieldinsert', cf_clear_puler );
    // build fild bindings
    rebuild_field_binding();
    $(document).trigger('load.page');

    const $newProcessorButton = $('.new-processor-button');
    let addProcessorButtonPulser;

    // build processor sortables
    function build_processor_sortables(){
        // set sortable groups
        $( ".caldera-editor-processors-panel ul" ).sortable({
            update() {
                rebuild_field_binding();
            },
            /**
             * Pulses processor button, changes to primary color if processor list is empty to make obvious to user
             *
             * @since 1.5.0.9
             */
            create() {
                if( 0 == $( '.caldera-editor-processors-panel ul' ).children().length) {
                    $newProcessorButton.addClass('button-primary');
                    addProcessorButtonPulser = new CalderaFormsButtonPulse( $newProcessorButton );
                    window.setTimeout(() => {
                        addProcessorButtonPulser.startPulse();
                    }, 3000);
                }
            }
        });

    }

    // set active processor editor
    $('body').on('click', '.caldera-processor-nav a', function(e){

        e.preventDefault();

        const clicked = $(this);

        $('.caldera-processor-nav').removeClass('active');
        $('.caldera-editor-processor-config-wrapper').hide();
        $( clicked.attr('href') ).show();
        clicked.parent().addClass('active');

    });

    $('body').on('click', '.add-new-processor', function(e){
        if( 'object' === typeof addProcessorButtonPulser ){
            $newProcessorButton.removeClass( 'button-primary' );
            addProcessorButtonPulser.stopPulse();
        }

        const clicked = $(this);
        const new_conf_templ = Handlebars.compile( $('#processor-wrapper-tmpl').html() );
        let wrap = $('.active-processors-list');
        let process_conf = $('.caldera-editor-processor-config');
        let processid = Math.round(Math.random() * 100000000);

        let new_templ = Handlebars.compile( $('#processor-line-tmpl').html() );
        let new_proc = {
            "id"	:	`fp_${processid}`,
            "type"	:	clicked.data('type')
        };

        // place new group line
        wrap.append( new_templ( new_proc ) );

        // place config
        process_conf.append( new_conf_templ( new_proc ) );

        // reset sortable
        $('#form_processor_baldrickModalCloser').trigger('click');
        $('.caldera-processor-nav a').last().trigger('click');
        $(`#fp_${processid}_type`).val(clicked.data('type')).trigger('change');
        build_processor_sortables();

    });

    // remove processor
    $('body').on('click', '.delete-processor', function(e){
        const clicked = $(this);
        const parent = clicked.closest('.caldera-editor-processor-config-wrapper');
        const type = parent.data('type');

        if(!confirm(clicked.data('confirm'))){
            return;
        }

        $(`.${parent.prop('id')}`).remove();
        parent.remove();

        $('.caldera-processor-nav a').first().trigger('click');

        rebuild_field_binding();
    });

    // set title & config of selected processor
    $('body').on('change', '.caldera-select-processor-type', function(e){
        const selected = $(this);
        const parent = selected.closest('.caldera-editor-processor-config-wrapper');
        let title = selected.find(`option[value="${selected.val()}"]`).text();
        const title_line = parent.find('.caldera-editor-processor-title');
        const activeline = $('.caldera-processor-nav.active a');

        if(title === ''){
            title = title_line.data('title');
        }

        title_line.html( title );
        activeline.html( `${title} <span class="processor-line-number"></span>` ).parent().addClass( `processor_type_${selected.val()}` );

        // get config
        build_processor_config(this);

        rebuild_field_binding();
    });
    $( document ).on('click', '#cf-shortcode-preview', function(){
        $(this).focus().select();
    } );
    $( document ).on('change', '.cf-email-preview-toggle', function(){
        const clicked = $(this);
        const preview_button = $('.caldera-header-email-preview-button');
        if( clicked.is(':checked') ){
            preview_button.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
        }else{
            preview_button.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
        }
    } );


    // build processor type config
    function build_processor_config(el){
        const select 			= $(el);
        const templ			= $(`#${select.val()}-tmpl`).length ? $(`#${select.val()}-tmpl`).html() : '';
        const parent			= select.closest('.caldera-editor-processor-config-wrapper');
        const target			= parent.find('.caldera-config-processor-setup');
        let template 		= Handlebars.compile(templ);
        let config			= parent.find('.processor_config_string').val();
        const current_type	= select.data('type');

        // Be sure to load the processors preset when switching back to the initial processor type.
        if(config.length && current_type === select.val() ){
            config = JSON.parse(config);
        }else{
            // default config
            config = processor_defaults[`${select.val()}_cfg`];
        }

        // build template
        if(!config){
            config = {};
        }

        config._id = parent.prop('id');
        config._name = `config[processors][${parent.prop('id')}][config]`;




        template = $('<div>').html( template( config ) );

        // send to target
        target.html( template.html() );

        // check for init function
        if( typeof window[`${select.val()}_init`] === 'function' ){
            window[`${select.val()}_init`](parent.prop('id'), target);
        }

        // check if conditions are allowed
        if(parent.find('.no-conditions').length){
            // conditions are not supported - remove them
            parent.find('.toggle_option_tab').remove();
        }


        rebuild_field_binding();
        baldrickTriggers();

        // initialise baldrick triggers
        $('.wp-baldrick').baldrick({
            request     : cfAdminAJAX,
            method      : 'POST',
            before(el) {

                const tr = $(el);

                if( tr.data('addNode') && !tr.data('request') ){
                    tr.data('request', 'cf_get_default_setting');
                }
            }
        });
    }

    // build configs on load:
    // allows us to keep changes on reload as not to loose settings on accedental navigation
    rebuild_field_binding();

    $('.caldera-select-processor-type').each((k, v) => {
        build_processor_config(v);
    });


    build_processor_sortables();
});//


// field binding helper
Handlebars.registerHelper('_field', function({hash}) {
    const config = this;
    let required="";
    let is_array = "";
    let exclude="";

    let default_val = this[hash.slug] ? ` data-default="${this[hash.slug]}"` : '';

    if(hash.required){
        required = " required";
    }
    if(hash.exclude){
        exclude = `data-exclude="${hash.exclude}"`;
    }
    if(hash.array){
        is_array = "[]";
        if(hash.array !== 'true'){
            default_val = `value="${hash.array}"`;
        }
    }

    let out = `<select ${hash.type ? `data-type="${hash.type}"` : ''}${default_val} ${exclude} name="${this._name}[${hash.slug}]${is_array}" id="${this._id}_${hash.slug}" class="block-input field-config caldera-field-bind${required}">`;
    if(this[hash.slug]){
        out += `<option class="bound-field" value="${this[hash.slug]}" class="bound-field"></option>`;
    }else{
        if(!hash.required){
            out += '<option value=""></option>';
        }
    }
    for(const fid in current_form_fields){

        let sel = '';

        if(hash.type){
            if(current_form_fields[fid].type !== hash.type){
                continue;
            }
        }

        if(config[hash.slug]){
            if(config[hash.slug] === fid){
                sel = ' selected="selected"';
            }
        }


        out += `<option value="${fid}"${sel}>${current_form_fields[fid].label} [${current_form_fields[fid].slug}]</option>`;
    }

    out += '</select>';
    if(hash.required){
        out += `<input class="field-config" name="${this._name}[_required_bounds][]" type="hidden" value="${hash.slug}">`;
    }
    return out;
});

Handlebars.registerHelper('console', function(context, options) {
    console.log(this);
});

let revisions = {};
/**
 * Get revisions from API and update panel UI
 *
 * @since 1.5.3
 */
function cf_revisions_ui() {
    const url = CF_ADMIN.rest.revisions;
    const templateEl = document.getElementById('tmpl--revisions');
    if (null === templateEl) {
        return;
    }

    const $spinner = jQuery( '#caldera-forms-revisions-spinner' );
    $spinner.css({
        visibility: 'visible',
        float:'none'
    });
    jQuery.get(url, r => {
        if( r.hasOwnProperty( 'message' ) ){
            document.getElementById('caldera-forms-revisions').innerHTML = `<p class="notice notice-large notice-info">${r.message}</p>`;
        }else{
            const data = {
                revisions: r
            };
            revisions = r;
            let template = templateEl.innerHTML;
            const source = jQuery('#tmpl--revisions').html();
            template = Handlebars.compile(source);
            document.getElementById('caldera-forms-revisions').innerHTML = template(data);
        }

        $spinner.css({
            visibility: 'hidden',
            float:'none'
        });

        jQuery('input[type=radio][name=caldera-forms-revision]').change(function() {
            jQuery( '#caldera-forms-revision-go' ).attr( 'href', jQuery( this ).data( 'edit' ) )
                .css({
                    display: 'inline-block',
                    visibility: 'visible'
                }).attr( 'aria-hidden', false );
        });


    }).error( () => {
        $spinner.css({
            visibility: 'hidden',
            float:'none'
        });
    });

}


