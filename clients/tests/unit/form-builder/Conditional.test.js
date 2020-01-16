import React from 'react';
import Conditional, {AppliesToFields} from "../../../form-builder/components/Conditional";
import testForm from './test-form';
import renderer from 'react-test-renderer';
import system_values from "./system_values";
import stateFactory, {
    getAllFieldsUsed,
    getFieldsNotAllowedForConditional,
    getFieldsUsedByConditional
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
    Object.values(testForm.conditional_groups.conditions).map(group => state.addConditional(factory.conditionalFromCfConfig(group)));

    const fieldsUsed = getAllFieldsUsed(state);
    const notAllowedFields = getFieldsNotAllowedForConditional(groupId, state);
    const appliedFields = getFieldsUsedByConditional(groupId, state);
    console.log(fieldsUsed.length, notAllowedFields.length, formFields.length);
    console.log(fieldsUsed);

    const strings = {'applied-fields': 'applied-fields', 'select-apply-fields': 'select-apply-fields'};
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


})
;
