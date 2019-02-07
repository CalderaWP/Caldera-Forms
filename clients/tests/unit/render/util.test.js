import {
	getFieldConfigBy,
	removeFromBlocking,
	removeFromPending,
	removeFromUploadStarted,
	setBlocking,
	createMediaFromFile,
	setSubmitButtonState,
	getFormIdAttr
} from '../../../render/util'

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

describe( 'cf2 var', () => {

	let cf2;
	beforeEach(() => {
		cf2 = {};
		cf2.pending = [];
		cf2.uploadStarted =  [];
		cf2.uploadCompleted =  [];
		cf2.fieldsBlocking = [];
	});

	afterEach(() => {
		cf2 = {};
	});

	test( 'removeFromPending removes a valid entry when its the only fieldId', () => {
		cf2.pending.push( 'fld123');
		removeFromPending('fld123', cf2 );
		expect(cf2.pending.length).toBe(0);
	});

	test( 'removeFromPending removes a valid entry when its one of many fieldId', () => {
		cf2.pending.push( 'fld456');
		cf2.pending.push( 'fld123');
		removeFromPending('fld123', cf2 );
		expect(cf2.pending.length).toBe(1);
		expect( cf2.pending.includes('fld456')).toBe(true);
		expect( cf2.pending.includes('fld123')).toBe(false);

	});

	test( 'removeFromPending does nothing when given invalid fieldId', () => {
		cf2.pending.push( 'fld456');
		cf2.pending.push( 'fld678');
		removeFromPending('fld123', cf2 );
		expect(cf2.pending.length).toBe(2);
		expect( cf2.pending.includes('fld456')).toBe(true);
		expect( cf2.pending.includes('fld678')).toBe(true);
	});




	test( 'removeFromUploadStarted removes a valid entry when its the only fieldId', () => {
		cf2.uploadStarted.push( 'fld123');
		removeFromUploadStarted('fld123', cf2 );
		expect(cf2.uploadStarted.length).toBe(0);
	});

	test( 'removeFromUploadStarted removes a valid entry when its one of many fieldId', () => {
		cf2.uploadStarted.push( 'fld456');
		cf2.uploadStarted.push( 'fld123');
		removeFromUploadStarted('fld123', cf2 );
		expect(cf2.uploadStarted.length).toBe(1);
		expect( cf2.uploadStarted.includes('fld456')).toBe(true);
		expect( cf2.uploadStarted.includes('fld123')).toBe(false);

	});

	test( 'removeFromUploadStarted does nothing when given invalid fieldId', () => {
		cf2.uploadStarted.push( 'fld456');
		cf2.uploadStarted.push( 'fld678');
		removeFromUploadStarted('fld123', cf2 );
		expect(cf2.uploadStarted.length).toBe(2);
		expect( cf2.uploadStarted.includes('fld456')).toBe(true);
		expect( cf2.uploadStarted.includes('fld678')).toBe(true);
	});




	test( 'removeFromBlocking removes a valid entry when its the only fieldId', () => {
		cf2.fieldsBlocking.push( 'fld123');
		removeFromBlocking('fld123', cf2 );
		expect(cf2.fieldsBlocking.length).toBe(0);
	});

	test( 'removeFromBlocking removes a valid entry when its one of many fieldId', () => {
		cf2.fieldsBlocking.push( 'fld456');
		cf2.fieldsBlocking.push( 'fld123');
		removeFromBlocking('fld123', cf2 );
		expect(cf2.fieldsBlocking.length).toBe(1);
		expect( cf2.fieldsBlocking.includes('fld456')).toBe(true);
		expect( cf2.fieldsBlocking.includes('fld123')).toBe(false);

	});

	test( 'removeFromBlocking does nothing when given invalid fieldId', () => {
		cf2.fieldsBlocking.push( 'fld456');
		cf2.fieldsBlocking.push( 'fld678');
		removeFromBlocking('fld123', cf2 );
		expect(cf2.fieldsBlocking.length).toBe(2);
		expect( cf2.fieldsBlocking.includes('fld456')).toBe(true);
		expect( cf2.fieldsBlocking.includes('fld678')).toBe(true);
	});


	test( 'setBlocking adds to fieldsBlocking', () => {
		setBlocking('cf123', cf2 );
		expect(cf2.fieldsBlocking.includes('cf123') ).toBe(true);
	});

	test( 'setBlocking removes from uploadStarted', () => {
		cf2.uploadStarted.push( 'cf123');
		setBlocking('cf123', cf2 );
		expect(cf2.uploadStarted.includes('cf123') ).toBe(false);
	});

	test( 'setBlocking removes from pending', () => {
		cf2.pending.push( 'cf123');
		setBlocking('cf123', cf2 );
		expect(cf2.pending.includes('cf123') ).toBe(false);
	});

});


describe( 'createMediaFromFile', () => {

	beforeEach(() => {
		fetch.resetMocks()
	});

	const API_FOR_FILES_URL = 'http://localhost:8228/wp-json/cf-api/v3/file';
	const formId = 'cf1';
	const nonce = '26bc3db86e';
	const additionalData = {
		hashes: ['09164c642f7ae975afe146e7a29d6913'],
		verify: '2c6463e902',
		formId,
		fieldId: 'fld_9226671',
		control: 'cf2_file5c1cfbcaa426c',
		_wp_nonce: nonce,
		API_FOR_FILES_URL
	};


	it( 'calls fetch with the right url', () => {
		createMediaFromFile( data.file, additionalData, fetch );
		expect(fetch.mock.calls[0][0]).toEqual('http://localhost:8228/wp-json/cf-api/v3/file');
	});

	it( 'calls fetch with the file in body', () => {
		createMediaFromFile( data.file, additionalData, fetch );
		const bodyData = fetch.mock.calls[0][1].body;
		const fileData = bodyData.get('file');
		expect(fileData).toBeDefined();
		expect(fileData).toBeTruthy();
	});

	it( 'calls fetch with POST HTTP method', () => {
		createMediaFromFile( data.file, additionalData, fetch );
		expect(fetch.mock.calls[0][1].method).toEqual('POST');
	});

	it( 'calls fetch with headers', () => {
		createMediaFromFile( data.file, additionalData, fetch );
		expect(typeof fetch.mock.calls[0][1].headers).toEqual('object');
	});

	it( 'Token to fetch headers', () => {
		createMediaFromFile( data.file, additionalData, fetch );
		expect(fetch.mock.calls[0][1].headers['X-WP-Nonce']).toEqual(nonce);
	});
});


describe('setSubmitButtonState', () => {

	let cf2 = {
		"CF5bed436999460_1": {
			"fields": {
				"fld_9226671_1": {},
				"fld_6214010_1": {}
			},
		},
		"pending": [],
		"uploadStarted": [],
		"uploadCompleted": [],
		"fieldsBlocking": []
	};
	const fieldConfig = {
		"type": "file",
		"outterIdAttr": "cf2-fld_6214010_1",
		"fieldId": "fld_6214010",
		"fieldLabel": "File2",
		"fieldCaption": "",
		"fieldPlaceHolder": "",
		"isRequired": true,
		"fieldDefault": "",
		"fieldValue": "",
		"fieldIdAttr": "fld_6214010_1",
		"configOptions": {
			"multiple": 1,
			"multiUploadText": false,
			"allowedTypes": "image/jpeg,image/pjpeg",
			"control": "cf2-fld_6214010_15c52e619cc068",
			"usePreviews": true,
			"previewWidth": 100,
			"previewHeight": 100,
			"maxFileUploadSize": 0
		},
		"formId": "CF5bed436999460",
		"control": "cf2_file5c52e619cbeda"
	};

	it( 'Get form ID Attr', () => {
		const result = getFormIdAttr( cf2,  "fld_6214010_1" );
		expect(result).toBe("CF5bed436999460_1");
	});

	it( 'Test submit button state when cf2.fieldBlocking is empty', () => {
		const result = setSubmitButtonState( cf2, fieldConfig );
		expect( result ).toBe(true);
	});

	it( 'Test submit button state when cf2.fieldBlocking is empty and State = false', () => {
		const result = setSubmitButtonState( cf2, fieldConfig, false );
		expect( result ).toBe(false);
	});
	it( 'Test submit button state when cf2.fieldBlocking is not empty', () => {
		cf2.fieldsBlocking = ["fld_6214010_1"];
		const result = setSubmitButtonState( cf2, fieldConfig );
		expect( result ).toBe(false);
	});
});