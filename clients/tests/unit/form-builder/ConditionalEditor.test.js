import React from 'react';
import {mount,shallow} from "enzyme/build";
import EnzymeAdapter from '../createEnzymeAdapter'
import ConditionalEditor from "../../../form-builder/ConditionalEditor";
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
const groupId = 'con_8761120514939434';
const factory = stateFactory(system_values, current_form_fields);
const state = factory.createState();
setConditionalsFromCfConfig(testForm.conditional_groups.conditions,state);



describe( 'ConditionalEditor', () => {
   it( 'lists conditionals', () => {
       expect.assertions(4);
       const component = mount(
           <ConditionalEditor strings={strings} state={state} />
       );
       expect(component.find( '.caldera-condition-nav' ).length ).toBe(3);
       state.getAllConditionals().map(c => {
           expect(component.find(`condition-group-${c.id}`).innerHtml ).toBe(c.name);
       });
   });

   test( 'Changing conditional name updates list', () => {
       const component = mount(
           <ConditionalEditor strings={strings} state={state} />
       );
       const condition = state.getAllConditionals()[1];
       const {id} = condition;
       expect(component.find(`condition-group-${condition.id}`).innerHtml ).toBe(condition.name);

       component.find( `#condition-group-name-${id}`)
           .simulate('change', { target: { value: 'Hello' } });
       
       expect(state.getConditional(id).config.name).toBe( 'Hello');

       expect(component.find(`condition-group-${condition.id}`).innerHtml ).toBe('Hello');

   });
});