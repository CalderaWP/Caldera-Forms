import { onRequest, createMediaFromFile, getFieldConfigBy, hashAndUpload } from '../../../render/util'

import * as data  from "./Mocks/mockUtils";
import {formRenderTestProps} from "./CalderaFormsRender.test";


describe(  'getFieldConfigBy', () => {

	it('finds fields that exits by fieldId', () => {
		expect(getFieldConfigBy(formRenderTestProps.fieldsToControl, 'fieldId', 'fld_5899467').fieldIdAttr).toBe('fld_5899467_1')
	});

	it('finds no fields when searching by invalid field id', () => {
		expect(getFieldConfigBy(formRenderTestProps.fieldsToControl, 'fieldId', 'fld_1')).toBe(undefined)
	});

});

describe(  'Test files request', () => {

	it( 'Calls to sub functions' , () => {

		const createMediaFromFile = jest.fn();
		const hashFile = jest.fn();

		hashAndUpload(data.threeFiles, data.verify, data.field, data.fieldId, data.cf2, data.API_FOR_FILES_URL, data._wp_nonce, data.obj, createMediaFromFile, hashFile );

		expect( createMediaFromFile ).toBeCalled();
		expect( hashFile ).toBeCalled();


	});

	it( 'CTesting onRequest' , () => {

		const createMediaFromFile = jest.fn();
		const hashFile = jest.fn();
		const hashAndUpload = jest.fn();
		const setBlocking = jest.fn();
		const removeFromBlocking = jest.fn();
		const removeFromUploadStarted = jest.fn();
		const removeFromPending = jest.fn();

		onRequest( data.obj, data.cf2, data.shouldBeValidating, data.messages, data.theComponent, data.values, data.fieldsToControl, data.CF_API_DATA,
			createMediaFromFile, hashFile, hashAndUpload, setBlocking, removeFromBlocking, removeFromUploadStarted, removeFromPending );

		expect( removeFromBlocking ).toBeCalled();
		expect( hashAndUpload).toBeCalled();

	});


});

