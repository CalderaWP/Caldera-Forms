import {handleFileUploadResponse, hashAndUpload, handleFileUploadError, processFiles, processFileField} from "../../../render/fileUploads";
import {hashFile, createMediaFromFile} from "../../../render/util";
import * as data from './Mocks/mockUtils';

describe( 'Test the files const passed to processFiles', () => {

	let processFunctions = {};
	let processData = {}

	beforeEach(() => {
		processFunctions = {processFiles, hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
		processData = {
			values: {},
			obj: data.obj,
			field: data.cf2.fields.fld_9226671_1,
			fieldId: data.cf2.fields.fld_9226671_1.fieldId,
			cf2: data.cf2,
			$form: data.obj.$form,
			CF_API_DATA: data.CF_API_DATA,
			messages: data.messages,
			theComponent: data.theComponent
		}
		processData.obj.$form.data = jest.fn();
		processFunctions.processFiles = jest.fn();
	});

	afterEach(() => {
		processFunctions.processFiles.mockReset();
	});

	it( 'Values set to one file' , () => {
		processData.values = data.oneValue;
		processFileField(processData, processFunctions);
		expect(processFunctions.processFiles).toBeCalled();
		expect(processFunctions.processFiles.mock.calls[0][0].length).toBe(1);
	});

	it( 'Values set to two files' , () => {
		processData.values = data.twoValues;
		processFileField(processData, processFunctions);
		expect(processFunctions.processFiles).toBeCalled();
		expect(processFunctions.processFiles.mock.calls[0][0].length).toBe(2);
	});

	it( 'Values set to three files' , () => {
		processData.values = data.threeValues;
		processFileField(processData, processFunctions);
		expect(processFunctions.processFiles).toBeCalled();
		expect(processFunctions.processFiles.mock.calls[0][0].length).toBe(3);
	});

	it( 'Values set to five files' , () => {
		processData.values = data.fiveValues;
		processFileField(processData, processFunctions);
		expect(processFunctions.processFiles).toBeCalled();
		expect(processFunctions.processFiles.mock.calls[0][0].length).toBe(5);
	});


});

describe( 'Unit tests, ignoring cf2 var side effects for handleFileUploadResponse', () => {

	let $form = {};
	let submit;
	let field = {};

	beforeEach(() => {
		$form = data.obj.$form;
		field = data.cf2.fields.fld_9226671_1;
	 	$form.submit = jest.fn();
	});

	afterEach(() => {
		$form.submit.mockClear();
	});

	it( 'Throws an error if passed non-object & does not submit form', () => {
		const response = undefined;
		let error = undefined;
		try{
			const r = handleFileUploadResponse(
				response,
				data.cf2,
				$form,
				{},
				field
			);
		}catch (e) {
			 error = e;
		}

		expect( undefined === typeof  error ).toBe(false);
		expect( $form.submit.mock.calls.length ).toBe(0);
	});

	it( 'Triggers submit, if passed object with control and lastFile = true', () => {

		let error = undefined;

		try{
			const r = handleFileUploadResponse(
				{control: 'nico'},
				data.cf2,
				$form,
				{},
				field,
				true
			);
		}catch (e) {
			error = e;
		}
		expect($form.submit).toBeCalled();
		expect($form.submit.mock.calls.length).toBe(1);
		expect(error).toBe(undefined);
	});

	it( 'Puts error message from response in messages var if possible and throws error', () => {
		let response = '';
		let error = '';
		const message = 'An Error Has Occured';
		const messages = {};
		const ID = field.fieldIdAttr;
		try{
			const r = handleFileUploadResponse(
				{
					message
				},
				data.cf2,
				$form,
				messages,
				field
			);
		}catch (e) {
			error = e;
		}

		expect(messages[ID].error).toBe(true);
		expect(messages[ID].message).toEqual(message);
		expect(error).toEqual({
			"message": "An Error Has Occured"
		});
		expect( undefined === typeof error ).toBe(false);
		expect( $form.submit.mock.calls.length ).toBe(0);
	});

	it( 'Throws error if response does not have control prop', () => {
		let error = undefined;
		const message = 'An Error Has Occured';
		const messages = {};
		const ID = field.fieldIdAttr;

		try{
			const r = handleFileUploadResponse(
				{message},
				data.cf2,
				$form,
				{},
				field
			);
		}catch (e) {
			error = e;
		}
		expect($form.submit).not.toBeCalled();
		expect($form.submit.mock.calls.length).toBe(0);
		expect(error).toEqual({
			"message": "An Error Has Occured"
		});
		expect( undefined === typeof error ).toBe(false);
	});

});

describe( 'Check responses with different values passed', () => {

	let $form = {};
	let submit;
	let field = {};
	let response = '';
	let error = '';

	beforeEach(() => {
		$form = data.obj.$form;
		field = data.cf2.fields.fld_9226671_1;
		response = undefined;
		error = undefined;
		$form.submit = jest.fn();
	});

	afterEach(() => {
		$form.submit.mockReset();
	})

	it( 'Throws error if response does not have control prop', () => {

		try {
			const r = handleFileUploadResponse(
				response,
				data.cf2,
				$form,
				{},
				data.field
			);
		} catch (e) {
			error = e;
		}

		expect(undefined === typeof  error).toBe(false);
		expect($form.submit.mock.calls.length).toBe(0);
	});

});

describe( 'hashAndUpload', () => {

	let processFunctions = {};
	let processData = {};

	beforeEach(() => {
		processFunctions = {hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
		processData = {
			verify: 'f42ea553cb',
			field: data.cf2.fields.fld_9226671_1,
			fieldId: data.cf2.fields.fld_9226671_1.fieldId,
			cf2: data.cf2,
			$form: data.obj.$form,
			CF_API_DATA: data.CF_API_DATA,
			messages: data.messages,
			theComponent: data.theComponent
		}
		processFunctions.hashFile = jest.fn();
	})

	afterEach(() => {
		processFunctions.hashFile.mockClear();
	})

	it( 'Call to hashFile' , () => {
		hashAndUpload(data.file, processData, processFunctions );
		expect( processFunctions.hashFile ).toBeCalled();
	});

});

describe( 'Calls to hashAndUpload based on number of files passed to processFiles', () => {

	let processFunctions = {};
	let processData = {}
	beforeEach(() => {
		processFunctions = {hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
		processData = {
			verify: 'f42ea553cb',
			field: data.cf2.fields.fld_9226671_1,
			fieldId: data.cf2.fields.fld_9226671_1.fieldId,
			cf2: data.cf2,
			$form: data.obj.$form,
			CF_API_DATA: data.CF_API_DATA,
			messages: data.messages,
			theComponent: data.theComponent
		}
		processFunctions.hashAndUpload = jest.fn();
	})

	afterEach(() => {
		processFunctions.hashAndUpload.mockClear();
	})

	it( 'Call to hashAndUpload three times because three files' , () => {
		processFiles(data.threeFiles, processData, processFunctions);
		expect(processFunctions.hashAndUpload).toBeCalled();
		expect(processFunctions.hashAndUpload.mock.calls.length).toBe(3);
	});

	it( 'Call to hashAndUpload once because one file' , () => {
		processFiles([data.file], processData, processFunctions);
		expect(processFunctions.hashAndUpload).toBeCalled();
		expect(processFunctions.hashAndUpload.mock.calls.length).toBe(1);
	});

});
