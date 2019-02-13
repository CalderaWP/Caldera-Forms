import renderer from 'react-test-renderer';
import {shallow} from 'enzyme';
import {CalderaFormsRender} from "../../../render/components";
import EnzymeAdapter from '../createEnzymeAdapter'
import React from 'react';
import {shouldDisableKey, shouldShowKey} from "../../../render/components/CalderaFormsRender";

const handler = () => {
};
const formId = 'CF5bdb2f3d8f7bd';

export function FakeOldState(formId, jQuery) {
	let fieldValues = {};
	this.__getFieldValues = function () {
		return fieldValues;
	};
	this.getState = function (fieldIdAttr) {
		return fieldValues.hasOwnProperty(fieldIdAttr)
			? fieldValues[fieldIdAttr]
			: null;
	};
	this.mutateState = function (fieldIdAttr, value) {
		fieldValues[fieldIdAttr] = value;
	};
	this.events = function () {
		return {
			/**
			 * Attach an event to change of an input in the state
			 *
			 * @since 1.5.3
			 *
			 * @param id {String} Field ID attribute
			 * @param callback {Function} The callback function
			 */
			subscribe: function (id, callback) {
			},
			detach: function (id, callback) {
			},
			emit: function (eventName, payload) {
			},
			attatchEvent: function (eventName, callback) {
			}
		}
	};
};


export const formRenderTestProps = {
	"cfState": new FakeOldState(formId, {}),
	"formId": formId,
	"formIdAttr": "CF5bdb2f3d8f7bd_1",
	"fieldsToControl": [
		{
			"type": "text",
			"outterIdAttr": "cf2-fld_12_1",
			"fieldId": "fld_12",
			"fieldLabel": "Text field",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": false,
			"fieldDefault": "",
			"fieldValue": "",
			"fieldIdAttr": "fld_12_1",
			"formId": "CF5bdb2f3d8f7bd",
		},
		{
			"type": "text",
			"outterIdAttr": "cf2-fld_text_req_1",
			"fieldId": "fld_text_req",
			"fieldLabel": "Text field",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": true,
			"fieldDefault": "",
			"fieldValue": "",
			"fieldIdAttr": "fld_text_req_1",
			"formId": "CF5bdb2f3d8f7bd",
		},
		{
			"type": "text",
			"outterIdAttr": "cf2-fld_text_req_2",
			"fieldId": "fld_text_req_2",
			"fieldLabel": "Text field",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": true,
			"fieldDefault": "Hi Roy",
			"fieldValue": "",
			"fieldIdAttr": "fld_text_req_2",
			"formId": "CF5bdb2f3d8f7bd",
		},
		{
			"type": "file",
			"outterIdAttr": "cf2-fld_5899467_1",
			"fieldId": "fld_5899467",
			"fieldLabel": "One File PNG Only",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": false,
			"fieldDefault": "",
			"fieldValue": "",
			"fieldIdAttr": "fld_5899467_1",
			"configOptions": {
				"multiple": false,
				"multiUploadText": false,
				"allowedTypes": "png",
				"control": "cf2-fld_5899467_15bdb63e87431e"
			},
			"formId": "CF5bdb2f3d8f7bd",
			"control": "cf2_file5bdb63e8742f3"
		},
		{
			"type": "file",
			"outterIdAttr": "cf2-fld_7480239_1",
			"fieldId": "fld_7480239",
			"fieldLabel": "Two Files PNG or JPG",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": false,
			"fieldDefault": "",
			"fieldValue": "",
			"fieldIdAttr": "fld_7480239_1",
			"configOptions": {
				"multiple": 1,
				"multiUploadText": false,
				"allowedTypes": false,
				"control": "cf2-fld_7480239_15bdb63e87436a"
			},
			"formId": "CF5bdb2f3d8f7bd",
			"control": "cf2_file5bdb63e874357"
		}
	],
	"shouldBeValidating": false,
  "strings": {
    "cf2FileField": {
      "removeFile": "Remove file",
      "defaultButtonText": "Drop files or click to select files to Upload"
    }
  }
};

describe('Form render methods', () => {

	it('gets the CF state object', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect(component.instance().getCfState()).toBeDefined();
	});

	test.skip('getFieldValue for a non-file field', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		expect(props.state.getState('fld_12_1')).toBe('foot');


		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);

		component.instance().getCfState().mutateState( 'fld_12_1', 'foot' );
		expect(component.instance().getFieldValue('fld_12_1')).toBe('foot');

	});

	test('getFieldValues returns an object', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};

		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);

		expect(typeof component.instance().getAllFieldValues()).toBe('object');

	});

	test( 'sets default field value in state', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};

		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);


		component.instance().getCfState().mutateState( 'fld_12_1', '' );
		component.instance().getCfState().mutateState( 'fld_text_req_2', 'Hi Roy' );
	});

	test.skip('getFieldValues returns values', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};

		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		const value = [
			{
				"preview": "blob:http://localhost:8228/eb12ce64-102f-4ba9-b87f-a2ec3f77756f"
			}
		];
		component.instance().getCfState().mutateState( 'fld_12_1', 'foot' );
		expect(component.instance().getAllFieldValues().fld_12_1).toBe('foot');
		expect(component.instance().getAllFieldValues().fld_7480239_1).toBe(value);

	});

	test('getFieldValue for a file field', () => {
		const fakeState = new FakeOldState(formId, {});

		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		const value = [
			{
				"preview": "blob:http://localhost:8228/eb12ce64-102f-4ba9-b87f-a2ec3f77756f"
			}
		];
		component.instance().setFieldValue("fld_7480239_1", value);
		expect(component.instance().getFieldValue('fld_7480239_1')).toEqual(value);

	});

	test('setFieldShouldShow update state to indicate field should not show', () => {
		const fakeState = new FakeOldState(formId, {});

		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().setFieldShouldShow('fld_12_1', false, '');
		expect(component.state(shouldShowKey('fld_12_1'))).toBe(false);
	});

	test('setFieldShouldShow update state to indicate field should show', () => {
		const fakeState = new FakeOldState(formId, {});

		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().setFieldShouldShow('fld_12_1', true, '');
		expect(component.state(shouldShowKey('fld_12_1'))).toBe(true);
	});

	test('getFieldConfig', () => {
		const fakeState = new FakeOldState(formId, {});

		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().setFieldShouldShow('fld_12_1', true, '');
		expect(component.instance().getFieldConfig('fld_12_1').fieldId).toBe('fld_12');
	});

	test('setFieldShouldDisable disables field in state', () => {
		const fakeState = new FakeOldState(formId, {});

		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().setFieldShouldDisable('fld_12_1', true, '');
		expect(component.state(shouldDisableKey('fld_12_1'))).toBe(true);
	});
	test('setFieldShouldDisable NOT disables field in state', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().setFieldShouldDisable('fld_12_1', true,);
		component.instance().setFieldShouldDisable('fld_12_1', false,);
		expect(component.state(shouldDisableKey('fld_12_1'))).toBe(false);
	});


	test('getHandler returns a function', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( typeof component.instance().getHandler('fld_12_1') ).toBe( 'function' );
	});

	test('getHandler returns the same function', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( component.instance().getHandler('fld_12_1') ).toBe( component.instance().getHandler('fld_12_1') );
	});

	test( 'isFieldRequired knows a field is required', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( component.instance().isFieldRequired('fld_text_req_1') ).toBe( true );

	});

	test( 'isFieldRequired knows a field is not required', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( component.instance().isFieldRequired('fld_12_1') ).toBe( false );

	});


	test( 'isFieldValid considers required field invalid when empty', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( component.instance().isFieldValid('fld_text_req_1') ).toBe( false );

	});

	test( 'isFieldValid considers required field valid when not empty', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.setState({fld_text_req_1: 'Hi Roy' });
		expect( component.instance().isFieldValid('fld_text_req_1') ).toBe( true );

	});


	test( 'isFieldValid considers required field valid when has array of values', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.setState({fld_7480239_1: ['Hi Roy'] });
		expect( component.instance().isFieldValid('fld_7480239_1') ).toBe( true );

	});

	test( 'isFieldValid considers a non-required field valid when  empty', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect( component.instance().isFieldValid('fld_7480239_1') ).toBe( true );

	});

	test( 'isFieldValid considers a non-required field valid when not empty', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().getCfState().mutateState( 'fld_12_1', 'foot' );
		expect( component.instance().isFieldValid('fld_12_1') ).toBe( true );

	});


	test( 'Set message for field', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().addFieldMessage( 'fld_12_1', 'Good things!', false );
		expect( typeof  component.state( 'messages')['fld_12_1']).toBe( 'object')
		expect( component.state( 'messages')['fld_12_1'].message ).toBe( 'Good things!' );
		expect( component.state( 'messages')['fld_12_1'].error ).toBe( false );

	});


	test( 'Updates message for field', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().addFieldMessage( 'fld_12_1', 'Good things!', false );
		component.instance().addFieldMessage( 'fld_12_1', 'Bad things!', true );
		expect( typeof  component.state( 'messages')['fld_12_1']).toBe( 'object')
		expect( component.state( 'messages')['fld_12_1'].message ).toBe( 'Bad things!' );
		expect( component.state( 'messages')['fld_12_1'].error ).toBe( true );

	});

	test( 'Can have two different messages for two different fields', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().addFieldMessage( 'fld_12_1', 'Good things!', false );
		component.instance().addFieldMessage( 'fld_5899467_1', 'Bad things!' );
		expect( typeof  component.state( 'messages')['fld_12_1']).toBe( 'object')
		expect( component.state( 'messages')['fld_12_1'].message ).toBe( 'Good things!' );
		expect( component.state( 'messages')['fld_5899467_1'].message ).toBe( 'Bad things!' );
		expect( component.state( 'messages')['fld_12_1'].error ).toBe( false );
		expect( component.state( 'messages')['fld_5899467_1'].error ).toBe( true );

	});


	test( 'Does not set message for non-existant fields', () => {
		const fakeState = new FakeOldState(formId, {});
		const props = {...formRenderTestProps, state: fakeState};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		component.instance().addFieldMessage( 'aaaa', 'Good things!', false );
		component.instance().addFieldMessage( 'fld_5899467', 'Bad things!' );
		expect( component.state( 'messages')).toEqual( {});
	});

	test( 'Get default file field translatable Strings', () => {
		const props = {...formRenderTestProps};
		const component = shallow(
		  <CalderaFormsRender
			{...props}
		  />
		);
		expect(component.instance().getStrings()).toBeDefined();
		expect(component.instance().getStrings()).toBeTruthy();
	});

	test( 'Get checkFieldValues function', () => {
		const props = {...formRenderTestProps};
		const component = shallow(
			<CalderaFormsRender
				{...props}
			/>
		);
		expect(component.instance().checkFieldValues()).toBeDefined();
		expect(component.instance().checkFieldValues()).toBeTruthy();
	});

});

