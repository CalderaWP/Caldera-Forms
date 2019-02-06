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
	let processFunctions = {};
	let processData = {};
	let theComponent = {};

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
			theComponent: data.theComponent,
			strings: data.CF_API_DATA.strings.cf2FileField
		}
		processData.obj.$form.data = jest.fn();

		processFunctions.handleFileUploadError = jest.fn();

		$form = data.obj.$form;
		field = data.cf2.fields.fld_9226671_1;
	 	$form.submit = jest.fn();
		theComponent.addFieldMessage = jest.fn();
	});

	afterEach(() => {
		$form.submit.mockClear();
		processFunctions.handleFileUploadError.mockReset();
		theComponent.addFieldMessage.mockReset();
	});

	it( 'Throws an error if passed non-object & does not submit form', () => {

		handleFileUploadResponse(
			undefined,
			data.file,
			processData,
			processFunctions,
			true
		);

		expect( $form.submit.mock.calls.length ).toBe(0);
		expect( processFunctions.handleFileUploadError ).toBeCalled()
		expect( processFunctions.handleFileUploadError.mock.calls.length ).toBe(1)
	});

	it( 'Triggers submit, if passed object with control and lastFile = true', () => {

		handleFileUploadResponse(
			{control: 'nico'},
			data.file,
			processData,
			processFunctions,
			true
		);
		expect($form.submit).toBeCalled();
		expect($form.submit.mock.calls.length).toBe(1);
	});

	it( 'Throws error if response does not have control prop', () => {
		let error = undefined;
		const message = 'An Error Has Occured';
		const messages = {};
		const ID = field.fieldIdAttr;
		const file = data.file;
		const lastFile = true;

		handleFileUploadResponse(
			{message},
			data.file,
			processData,
			processFunctions,
			true
		);
		expect($form.submit).not.toBeCalled();
		expect($form.submit.mock.calls.length).toBe(0);
		expect( processFunctions.handleFileUploadError ).toBeCalled()
		expect( processFunctions.handleFileUploadError.mock.calls.length ).toBe(1)
	});

	it( 'Unit Test handleFileUploadError with error object that has a message property', () => {
		let error = { 'message': 'An Error Has Occured' };
		handleFileUploadError(
			error,
			data.file,
			data.CF_API_DATA.strings.cf2FileField,
			data.cf2.fields.fld_9226671_1.fieldIdAttr,
			theComponent
		);
		expect( theComponent.addFieldMessage ).toBeCalled();
		expect( theComponent.addFieldMessage.mock.calls.length ).toBe(1);
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
