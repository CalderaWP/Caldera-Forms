import React from 'react';
import  {NewConditionalButton} from "../../../form-builder/components/Conditionals";
import {NewConditionalGroup} from "../../../form-builder/components/NewConditionalGroup";
import {mount, shallow} from "enzyme/build";
import EnzymeAdapter from '../createEnzymeAdapter'
describe( 'NewConditionalButton', () => {
    const strings = {'new-conditional': 'New Conditional'};

    test( 'Shows button by default', () => {
        const component = mount(<NewConditionalGroup strings={strings} onNewConditional={jest.fn()} />);
        expect( component.find( '#new-conditional').length ).toBe(1);
    });

    test( 'Hides button after after clicking button.', () => {
        const component = mount(<NewConditionalGroup strings={strings} onNewConditional={jest.fn()} />);
        component.find( '#new-conditional').simulate('click');
        expect( component.find( '#new-conditional').length ).toBe(0);
    });

    test( 'It passes name to callback when blurred', () => {
        const  onNewConditional = jest.fn();
        const component = mount(<NewConditionalGroup
            strings={strings}
            onNewConditional={onNewConditional} />);
        component.find( '#new-conditional').simulate('click');
        expect( component.find( '#new-conditional').length ).toBe(0);
        component.find( '.condition-new-group-name' ).simulate('change', {
            target : {value: 'Hello'}
        }).simulate('blur');

        expect( onNewConditional ).toBeCalledTimes(1);
        //got new name
        expect( onNewConditional.mock.calls[0][0]).toBe('Hello');
        //Got a new ID
        expect( typeof onNewConditional.mock.calls[0][1]).toBe('string');

    });
});