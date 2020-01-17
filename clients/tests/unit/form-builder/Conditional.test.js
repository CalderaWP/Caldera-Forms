import React from 'react';
import Conditional, {AppliesToFields, ConditionalLine} from "../../../form-builder/components/Conditional";
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
    setConditionalsFromCfConfig(testForm,state);

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

    test('AppliesToFields matches snapshot', () => {
        const component = renderer.create(
            <AppliesToFields
                {...props}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });
    test('AppliesToFields has the right number of boxes disabled', () => {
        const component = mount(
            <AppliesToFields
                {...props}
            />
        );
        expect(component.find({type: 'checkbox'}).length).toBe(6);
        expect(component.find({type: 'checkbox', disabled: true}).length).toBe(notAllowedFields.length);
    });

    test('AppliesToFields has the right number of boxes checked', () => {
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
        const props = {line,strings,formFields,isFirst:true};
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
        const props = {line,strings,formFields,isFirst:true};
        const component = renderer.create(
            <ConditionalLine
                {...props}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    })


    test( 'setConditionalsFromCfConfig adds conditionals from cf1 config style', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        setConditionalsFromCfConfig(testForm,state);
        expect(state.getConditional('con_3156693554561454').config.name).toBe('Hide Dropdown')
    });

})

