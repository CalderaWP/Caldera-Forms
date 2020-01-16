import React from 'react';
import {ConditionalsList,NewConditionalButton,NewGroupName} from "../../../form-builder/components/Conditionals";
import testForm from './test-form';
import renderer from 'react-test-renderer';


describe('Conditionals', () => {
    it('Renders list', () => {
        const component = renderer.create(
            <ConditionalsList
                conditionals={Object.values(testForm.conditional_groups.conditions)}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });

    it( 'Renders new conditional button', () => {
        const component = renderer.create(
            <NewConditionalButton
                text={'New'}
                onClick={jest.fn()}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });

    it( 'New group name field renders',() => {
        const props = {placeholder: 'Placeholder', onChange: jest.fn(), id: 'f', value: 'val'};
        const component = renderer.create(
            <NewGroupName
                text={'New'}
                onClick={jest.fn()}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });

});