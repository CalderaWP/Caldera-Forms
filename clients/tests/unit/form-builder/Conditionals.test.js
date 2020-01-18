import React from 'react';
import {NewConditionalButton} from "../../../form-builder/components/Conditionals";
import renderer from 'react-test-renderer';


describe('Conditionals', () => {

    it('Renders new conditional button', () => {
        const component = renderer.create(
            <NewConditionalButton
                text={'New'}
                onClick={jest.fn()}
            />
        );
        expect(component.toJSON()).toMatchSnapshot();
    });

});