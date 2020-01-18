import React from 'react';
import Conditional, {
    AppliesToFields,
    ConditionalLine,
    ConditionalLines
} from "../../../form-builder/components/Conditional";
import testForm from './test-form';
import renderer from 'react-test-renderer';
import system_values from "./system_values";
import stateFactory, {
    getAllFieldsUsed,
    getFieldsNotAllowedForConditional,
    getFieldsUsedByConditional, setConditionalsFromCfConfig
} from "../../../form-builder/stateFactory";
import {mount} from "enzyme/build";
import EnzymeAdapter from '../createEnzymeAdapter'

describe('Editor for a single conditional', () => {

    const formFields = Object.values(testForm.fields);
    const current_form_fields = formFields.map(field => {
            return {
                slug: field.slug,
                label: field.label,
                type: field.type
            }
        }
    );
    const groupId = 'con_8761120514939434';
    const factory = stateFactory(system_values, current_form_fields);
    const state = factory.createState();
    setConditionalsFromCfConfig(testForm, state);

    const fieldsUsed = getAllFieldsUsed(state);
    const notAllowedFields = getFieldsNotAllowedForConditional(groupId, state);
    const appliedFields = getFieldsUsedByConditional(groupId, state);


    const strings = {
        if: 'If',
        and: 'And',
        name: 'Name',
        disable: 'Disable',
        'add-conditional-group': 'add-conditional-group',
        'applied-fields': 'applied-fields',
        'select-apply-fields': 'select-apply-fields',
        'remove-condition': 'remove-condition',
    };
    const props = {
        onChange: jest.fn(),
        formFields,
        fieldsUsed, appliedFields, notAllowedFields, strings, groupId
    };

    test.skip('AppliesToFields matches snapshot', () => {
        const component = renderer.create(
            <AppliesToFields
                {...props}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });
    test.skip('AppliesToFields has the right number of boxes disabled', () => {
        const component = mount(
            <AppliesToFields
                {...props}
            />
        );
        expect(component.find({type: 'checkbox'}).length).toBe(6);
        expect(component.find({type: 'checkbox', disabled: true}).length).toBe(notAllowedFields.length);
    });

    test.skip('AppliesToFields has the right number of boxes checked', () => {
        const component = mount(
            <AppliesToFields
                {...props}
            />
        );
        expect(component.find({type: 'checkbox'}).length).toBe(6);
        expect(component.find({type: 'checkbox', checked: true}).length).toBe(4);
    });

    test('ConditionalLine with a number field as value', () => {
        const line = {
            "parent": "rw7802324828821689",
            "field": "fld_1705245",
            "compare": "greater",
            "value": "5"
        };
        const props = {line, strings, formFields, isFirst: true};
        const component = renderer.create(
            <ConditionalLine
                {...props}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    })

    test('ConditionalLine with a checkbox field as value', () => {
        const line = {
            "parent": "rw7802324828821689",
            "field": "fld_5216203",
            "compare": "greater",
            "value": "opt1184564"
        };
        const props = {line, strings, formFields, isFirst: true};
        const component = renderer.create(
            <ConditionalLine
                {...props}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });

    test('Conditional line updates field', () => {
        const line = {
            "parent": "rw7802324828821689",
            "field": "fld_5216203",
            "compare": "greater",
            "value": "opt1184564"
        };
        const onUpdateLine = jest.fn();
        const props = {line, strings, formFields, isFirst: true, onUpdateLine};
        const component = mount(
            <ConditionalLine
                {...props}
            />
        );
        expect(component.find('.condition-line-field').prop('value')).toBe(line.field);
        component.find('.condition-line-field').simulate('change', {
            target: {value: formFields[0].ID}
        });
        expect(onUpdateLine).toBeCalledWith({
            ...line,
            field: formFields[0].ID
        });

        expect(component.find('.condition-line-compare').prop('value')).toBe(line.compare);
        component.find('.condition-line-compare').simulate('change', {
            target: {value: 'isnot'}
        });


    });

    test('Conditional line updates compare', () => {
        const line = {
            "parent": "rw7802324828821689",
            "field": "fld_5216203",
            "compare": "greater",
            "value": "opt1184564"
        };
        const onUpdateLine = jest.fn();
        const props = {line, strings, formFields, isFirst: true, onUpdateLine};
        const component = mount(
            <ConditionalLine
                {...props}
            />
        );


        expect(component.find('.condition-line-compare').prop('value')).toBe(line.compare);
        component.find('.condition-line-compare').simulate('change', {
            target: {value: 'isnot'}
        });

        expect(onUpdateLine).toBeCalledWith({
            ...line,
            compare: 'isnot'
        });


    });

    test('Conditional line remove', () => {
        const line = {
            id: 'ffsdkl',
            "parent": "rw7802324828821689",
            "field": "fld_5216203",
            "compare": "greater",
            "value": "opt1184564"
        };
        const onRemoveLine = jest.fn();
        const props = {line, strings, formFields, isFirst: true, onRemoveLine};
        const component = mount(
            <ConditionalLine
                {...props}
            />
        );
        component.find('.condition-line-remove').simulate('click');
        expect(onRemoveLine).toBeCalledWith(line.id,line.parent);

    });

    test('ConditionalLines lists lines that can be changed and removed', () => {
        const lines = [
            {
                id: 'cl483565773895193',
                "parent": "rw8614452180961339",
                "field": "fld_5216203",
                "compare": "is",
                "value": "opt1326030"
            },
            {
                id: "cl9529641586904005",
                "parent": "rw4809818870119261",
                "field": "fld_313720",
                "compare": "is",
                "value": "opt1326030"
            }
        ];
        const onUpdateLine = jest.fn();
        const onRemoveLine = jest.fn();
        const props = {lines,strings, formFields,onUpdateLine,onRemoveLine};
        const component = mount(
            <ConditionalLines
                {...props}
            />
        );
        expect( component.find( '.caldera-condition-line' ).length ).toBe(2);

        component.find('.condition-line-compare').last().simulate('change', {
            target: {value: 'isnot'}
        });

        //Updating line sends update line * line id
        expect(onUpdateLine).toBeCalledWith({
            ...lines[1],
            compare: 'isnot'
        },lines[1].id);

        //Remove line
        component.find('.condition-line-remove').last().simulate('click');
        expect( onRemoveLine ).toBeCalledWith(lines[1].id,lines[1].parent)

    });


    test('setConditionalsFromCfConfig adds conditionals from cf1 config style', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        setConditionalsFromCfConfig(testForm.conditional_groups.conditions, state);
        expect(state.getConditional('con_3156693554561454').config.name).toBe('Hide Dropdown')
    });


});

