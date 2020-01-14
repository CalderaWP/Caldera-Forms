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

    $document.on('click', '[data-add-line]', function () {

        var clicked = $(this),
            id = clicked.data('addLine'),
            db = $('#cf-conditions-db'),
            data = get_base_form(),
            pid = clicked.data('group'),
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
        get_base_form();
        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
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


        var clicked = $(this),
            id = clicked.data('openGroup'),
            db = $('#cf-conditions-db'),
            data = get_base_form();

        data._open_condition = id;
        db.val(JSON.stringify(data)).trigger('rebuild-conditions');

    });

    $document.on('click', '[data-remove-line]', function () {
        var clicked = $(this),
            id = clicked.data('removeLine');

        $('.condition-line-' + id).remove();

        var db = $('#cf-conditions-db'),
            data = get_base_form();

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
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

        data._open_condition = '';

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
    });

    $document.on('keydown keyup keypress change', '[data-sync]', function (e) {
        var press = $(this),
            target = $(press.data('sync'));
        if (target.is('input')) {
            target.val(press.val()).trigger('change');
        } else {
            target.html(press.val());
        }
    });
    $document.on('change', '[data-bind-condition]', function () {

        $document.trigger('show.fieldedit');

        var clicked = $(this),
            bind = $(clicked.data('bindCondition'));
        if (clicked.is(':checked')) {
            bind.val(clicked.val());
        } else {
            bind.val('');
        }

        var data = get_base_form(),
            db = $('#cf-conditions-db');

        db.val(JSON.stringify(data)).trigger('rebuild-conditions');
    });
    $document.on('show.fieldedit', function () {

        var data = $('#caldera-forms-conditions-panel').formJSON(),
            condition_selectors = $('.cf-conditional-selector');
        condition_selectors.each(function () {
            var select = $(this),
                selected = select.parent().val(),
                field = select.parent().data('id');

            select.empty();
            for (var con in data.conditions) {
                var run = true;
                // check field is not in here.
                for (var grp in data.conditions[con].group) {
                    for (var ln in data.conditions[con].group[grp]) {
                        if (data.conditions[con].group[grp][ln].field === field) {
                            run = false;
                        }
                    }
                }
                if (true === run) {
                    var sel = '',
                        line = '<option value="' + con + '" ' + (selected === con ? 'selected="selected"' : '') + '>' + data.conditions[con].name + '</option>';

                    select.append(line);
                }
            }

        });
    });


}