function caldera_forms_sendwp_remote_install() {
    var data = {
        'action': 'caldera_forms_sendwp_remote_install',
        'sendwp_nonce': sendwp_vars.nonce
    };
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        var data = JSON.parse(response);
        //Check for errors before calling caldera_forms_sendwp_register_client()
        if(data.error === true ){
            if( data.debug === '!security'){
                var div = jQuery('<div class="notice error"></div>');
                var p = jQuery('<p></p>');
                var notice = sendwp_vars.security_failed_message;
                div.append(p);
                p.append(notice);
                jQuery("#cf-email-settings-ui").prepend(div);
            } else if( data.debug === '!user_capablity'){
                var userdiv = jQuery('<div class="notice error"></div>');
                var userp = jQuery('<p></p>');
                var usernotice = sendwp_vars.user_capability_message;
                userdiv.append();
                userp.append(usernotice);
            }
        } else {
            caldera_forms_sendwp_register_client(data.register_url, data.client_name, data.client_secret, data.client_redirect, data.partner_id);
        }
        
    });
}

function caldera_forms_sendwp_register_client(register_url, client_name, client_secret, client_redirect, partner_id) {

    var form = document.createElement("form");
    form.setAttribute("method", 'POST');
    form.setAttribute("action", register_url);

    function caldera_forms_sendwp_append_form_input(name, value) {
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", name);
        input.setAttribute("value", value);
        form.appendChild(input);
    }

    caldera_forms_sendwp_append_form_input('client_name', client_name);
    caldera_forms_sendwp_append_form_input('client_secret', client_secret);
    caldera_forms_sendwp_append_form_input('client_redirect', client_redirect); 
    caldera_forms_sendwp_append_form_input('partner_id', partner_id);    
    
    document.body.appendChild(form);
    form.submit();
}