export function bindBaldrickTriggers($,adminAJAX) {
    return function () {
        $('.ajax-trigger').baldrick({
            request: adminAJAX,
            method: 'POST',
            before: function (el, e) {
                var clicked = $(el);
                // check for a nonce

                var nonce = $('#cf_toolbar_actions'),
                    referer = nonce.parent().find('[name="_wp_http_referer"]');

                if (nonce.length && referer.length) {
                    clicked.data('cf_toolbar_actions', nonce.val());
                    clicked.data('_wp_http_referer', referer.val());
                }

                if (clicked.data('trigger')) {
                    e.preventDefault();
                    var trigger = $(clicked.data('trigger'));

                    trigger.trigger(( trigger.data('event') ? trigger.data('event') : 'click' ));
                    return false;
                }
            },
            complete: function () {
                // check for init function
                $('.init_field_type[data-type]').each(function (k, v) {
                    var ftype = $(v);
                    if (typeof window[ftype.data('type') + '_init'] === 'function') {
                        window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
                    }
                });
            }
        });
    };
};