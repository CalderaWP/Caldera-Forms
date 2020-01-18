import React from 'react';
import {mount, shallow} from "enzyme/build";
import EnzymeAdapter from '../createEnzymeAdapter'
import ConditionalEditor,{ConditionalEditorApp} from "../../../form-builder/ConditionalEditor";
import testForm from "./test-form";
import stateFactory, {setConditionalsFromCfConfig} from "../../../form-builder/stateFactory";
import system_values from "./system_values";

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

const formFields = Object.values(testForm.fields);
const current_form_fields = formFields.map(field => {
        return {
            slug: field.slug,
            label: field.label,
            type: field.type
        }
    }
);
const factory = stateFactory(system_values, current_form_fields);
const state = factory.createState();
setConditionalsFromCfConfig(testForm.conditional_groups.conditions, state);


describe('ConditionalEditor', () => {
    it('lists conditionals', () => {
        //expect.assertions(4);
        const fields = state.getAllFields();
        const conditionals = state.getAllConditionals();
        expect(conditionals.length).toBe(3);

        const component = mount(
            <ConditionalEditor strings={strings} fields={fields} conditionals={conditionals}/>
        );
        const navList = component.find('.caldera-condition-nav');
        expect(navList.length).toBe(3);
        conditionals.map(c => {
            const {id} = c;
            const name = c.config.name;
            expect(navList.find(`#condition-group-${id}`).text()).toBe(name);
        });
    });



    test('Activating conditional', () => {

        const component = mount(
            <ConditionalEditorApp strings={strings} state={state}/>
        );
        const condition = state.getAllConditionals()[1];
        const {id} = condition;
        expect(component.find('.active').length).toBe(0);
        component.find(`#condition-open-group-${id}`)
            .simulate('click');
        expect(component.find('.active').length).toBe(1);


    });

    test('Changing conditional name updates list', () => {

        const component = mount(
            <ConditionalEditorApp strings={strings} state={state}/>
        );
        const condition = state.getAllConditionals()[1];
        const {id} = condition;
        component.find(`#condition-open-group-${id}`)
            .simulate('click');

        expect(component.find('.active').length).toBe(1);
        expect(component.find(`#condition-group-con_3156693554561454`).text()).toBe('Hide Dropdown');

        expect(component.find(`#condition-group-name-${id}`).length).toBe(1);
        component.find(`#condition-group-name-${id}`)
            .simulate('change', {target: {value: 'Hello'}});
        expect(component.find(`#condition-group-con_3156693554561454`).text()).toBe('Hello');

    });

    test('Adding a conditional', () => {
        const component = mount(
            <ConditionalEditorApp strings={strings} state={state}/>
        );
        component.find('#new-conditional').simulate('click');
        component.find('.condition-new-group-name').simulate('change', {
            target: {value: 'Hello'}
        }).simulate('blur');
        //Adds to list
        expect( component.find('.active-conditions-list').children().length).toBe(4);
        //Sets an active class
        expect(component.find('.active').length).toBe(1);
        //Has this conditional editor open
        expect(component.find('.condition-group-name').prop('value')).toBe('Hello')
    });

  test( 'Remove condition', () => {
      const component = mount(
          <ConditionalEditorApp strings={strings} state={state}/>
      );
      component.find('#new-conditional').simulate('click');

      //Adds to list
      expect( component.find('.active-conditions-list').children().length).toBe(3);
      const condition = state.getAllConditionals()[1];
      const {id} = condition;
      component.find(`#condition-open-group-${id}`)
          .simulate('click');
      component.find( '.condition-remove' ).simulate('click');
      expect( component.find('.active-conditions-list').children().length).toBe(2);

  })

});