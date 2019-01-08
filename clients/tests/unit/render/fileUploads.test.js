import {handleFileUploadResponse, hashAndUpload, handleFileUploadError, processFiles, processFileField} from "../../../render/fileUploads";
import {hashFile, createMediaFromFile} from "../../../render/util";
import {cf2, obj, CF_API_DATA, messages} from  './Mocks/mockUtils';
import * as data from './Mocks/mockUtils';

describe( 'Unit tests, ignoring cf2 var side effects for handleFileUploadResponse', () => {
	let $form = obj.$form;
	let submit;
	const field = cf2.fields.fld_9226671_1;

	beforeEach(() => {
	 	$form.submit = jest.fn();
	});


	it( 'Throws an error if passed non-object & does not submit form', () => {
		const response = undefined;
		let error = undefined;
		try{
			const r = handleFileUploadResponse(
				response,
				cf2,
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
				cf2,
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
				cf2,
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
				cf2,
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
	let $form = obj.$form;
	let submit;
	const field = cf2.fields.fld_9226671_1;
	const response = undefined;
	let error = undefined;

	beforeEach(() => {
		$form.submit = jest.fn();
	});

	it( 'Throws error if response does not have control prop', () => {

		try {
			const r = handleFileUploadResponse(
				response,
				cf2,
				$form,
				{},
				field
			);
		} catch (e) {
			error = e;
		}

		expect(undefined === typeof  error).toBe(false);
		expect($form.submit.mock.calls.length).toBe(0);
	});

});


describe( 'hashAndUpload', () => {

	let processFunctions = {hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
	const processData = {
		verify: 'f42ea553cb',
		field: data.cf2.fields.fld_9226671_1,
		fieldId: data.cf2.fields.fld_9226671_1.fieldId,
		cf2: cf2,
		$form: obj.$form,
		CF_API_DATA: CF_API_DATA,
		messages: messages
	}

	it( 'Call to hashFile' , () => {
		processFunctions.hashFile = jest.fn();
		hashAndUpload(data.file, processData, processFunctions );
		expect( processFunctions.hashFile ).toBeCalled();
	});

});

describe( 'Calls to hashAndUpload based on number of files passed to processFiles', () => {

	let processFunctions = {hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
	const processData = {
		verify: 'f42ea553cb',
		field: data.cf2.fields.fld_9226671_1,
		fieldId: data.cf2.fields.fld_9226671_1.fieldId,
		cf2: cf2,
		$form: data.obj.$form,
		CF_API_DATA: data.CF_API_DATA,
		messages: messages
	}

	it( 'Call to hashAndUpload three times because three files' , () => {
		processFunctions.hashAndUpload = jest.fn();
		processFiles(data.threeFiles, processData, processFunctions);
		expect(processFunctions.hashAndUpload).toBeCalled();
		expect(processFunctions.hashAndUpload.mock.calls.length).toBe(3);
	});

	it( 'Call to hashAndUpload once because one file' , () => {
		processFunctions.hashAndUpload = jest.fn();
		processFiles([data.file], processData, processFunctions);
		expect(processFunctions.hashAndUpload).toBeCalled();
		expect(processFunctions.hashAndUpload.mock.calls.length).toBe(1);
	});

});

describe( 'Test the files const passed to processFiles', () => {

	let processFunctions = {processFiles, hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError};
	const processData = {
		values: data.values,
		obj: data.obj,
		field: data.cf2.fields.fld_9226671_1,
		fieldId: data.cf2.fields.fld_9226671_1.fieldId,
		cf2: cf2,
		$form: data.obj.$form,
		CF_API_DATA: data.CF_API_DATA,
		messages: messages
	}

	it( 'Values set to three files' , () => {

		processData.obj.$form.data = jest.fn();
		processFunctions.processFiles = jest.fn();

		processFileField(processData, processFunctions);

		expect(processFunctions.processFiles).toBeCalled();
		expect(processFunctions.processFiles.mock.calls[0][0].length).toBe(3)
	});


});