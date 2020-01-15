import cfEditorState from './stateFactory';
var cf_new_condition_line, cf_new_condition_group;
/**
 *
 * @param {cfEditorState} state
 * @param $
 * @param document
 */
export default function (state,$,document) {
    const $document = $(document);
    function get_base_form() {
        var data_fields = $('.caldera-forms-options-form').formJSON(),
            object = {
                _open_condition: data_fields._open_condition,
                conditions: data_fields.conditions,
                fields: data_fields.config.fields,
                magic: data_fields._magic
            };

        return object;
    }
    cf_new_condition_group = function () {
        var data = get_base_form(),
            db = $('#cf-conditions-db'),
            id = 'con_' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766);

        if (!data.conditions) {
            data.conditions = {};
        }

        data.conditions[id] = {
            id: id
        };

        data._open_condition = id;

        db.val(JSON.stringify(data));

        return data;
    };

    $document.on('click', '#new-conditional', function () {

       console.log('New Conditionals');
    });

    /**
    var $newConditionalButton = $('#new-conditional');
    var addProcessorButtonPulser;

    $newConditionalButton.on('click', function () {
        if ('object' === typeof addProcessorButtonPulser) {
            $newConditionalButton.removeClass('button-primary');
            addProcessorButtonPulser.stopPulse();
        }
    });
     **/

    $document.on('click', '[data-add-group]', function () {

        var clicked = $(this),
            pid = clicked.data('addGroup'),
            db = $('#cf-conditions-db'),
            data = get_base_form(),
            id = 'rw' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766),
            cid = 'cl' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766);

        if (!data.conditions[pid].group) {
            data.conditions[pid].group = {};
        }
        if (!data.conditions[pid].group[id]) {
            data.conditions[pid].group[id] = {};
        }

        // initial line
        data.conditions[pid].group[id][cid] = {
            parent: id
        };

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
    });
    $document.on('blur change', '[data-new-condition]', function () {
        var clicked = $(this),
            id = clicked.data('newCondition');
        if (!clicked.val().length) {
            $('.condition-point-' + id).remove();
        }
        var db = $('#cf-conditions-db'),
            data = get_base_form();

        data._open_condition = id;

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
    });
    $document.on('change', '[data-live-sync]', function () {

        var data = get_base_form(),
            db = $('#cf-conditions-db');

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');

    });

    $document.on('click', '#tab_conditions', function () {

        if (0 === $('.active-conditions-list').children().length) {
            $newConditionalButton.addClass('button-primary');
            addProcessorButtonPulser = new CalderaFormsButtonPulse($newConditionalButton);
            window.setTimeout(function () {
                addProcessorButtonPulser.startPulse();
            }, 3000);
        }

        var data = get_base_form(),
            db = $('#cf-conditions-db');

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');

    });

    $document.on('click', '[data-open-group]', function () {
        const $clicked = $(this);
        console.log('opened group',$clicked);

    });

    $document.on('click', '[data-remove-line]', function () {
        const $clicked = $(this);
        console.log('Remove Line',$clicked);
    });

    $document.on('click', '[data-remove-group]', function () {
        var clicked = $(this),
            id = clicked.data('removeGroup');

        if (clicked.data('confirm')) {
            if (!confirm(clicked.data('confirm'))) {
                return;
            }
        }

        $('.condition-point-' + id).remove();

        var db = $('#cf-conditions-db'),
            data = get_base_form();

    });


    $document.on('show.fieldedit', function () {

    });


}