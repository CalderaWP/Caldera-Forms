import {
	visitPage,
	getCfField,
	clearCfField,
	cfFieldIsVisible,
	cfFieldDoesNotExist,
	cfFieldHasValue,
	cfFieldSelectValue,
	cfFieldSetValue,
	cfFieldCheckValue,
	cfFieldIsDisabled,
	cfFieldUnCheckValue,
	cfFieldIsNotDisabled,
	cfFieldCheckAllValues,
	cfFieldCalcFieldValueIs
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('hello-world');
	});

	const formId = 'cf111';



	it( 'Can add an image and see preview', () => {
		//Load form
		//Add a field to file field
		//See preview
	});

	it( 'Remove button does not exists if no file set', () => {
		//Load form
		//NOT see remove file button
	});

	it( 'Remove button exists if file is there', () => {
		//Load form
		//Add a field to file field
		//See remove file button
	});

	it( 'Can remove an image and preview is removed', () => {
		//Load form
		//Add a field to file field
		//See preview
		//Remove field
		//Not see preview
	});

	it( 'If multiple upload option is not enabled, can not add a second file.', () => {
		//Load form (multiple upload support false)
		//Add a field to file field
		//See preview
		//NOT see add more button
	});

	it( 'If multiple upload option is not enabled, and file is added and file is removed and a new one could be added', () => {
		//Load form (multiple upload support false)
		//Add a field to file field
		//NOT see add more button
		//Remove field
		//SEE add field button

	});

	it( 'If multiple upload option is not enabled, and file is added and file is removed and a new one can be added', () => {
		//Load form (multiple upload support false)
		//Add a field to file field
		//NOT see add more button
		//Remove field
		//Add file
		//See preview

	});

	it( 'Can add an image and see preview', () => {
		//Load form
		//Add a field to file field
		//See preview
	});

	it( 'Can add an image and see name', () => {
		//Load form
		//Add a field to file field
		//See file name
	});

	it( 'Can add an non-image and see name', () => {
		//Load form
		//Add a field to file field (not an image)
		//See file name
	});


	it( 'Can add a non-image and not see broken image link', () => {
		//Load form
		//Add a field to file field (not an image)
		//See file name
		//Not see broken file link
	});

	it( 'Can add multiple files', () => {
		//Load form (multiple upload support true)
		//Add a field to file field
		//See add more button
		//Add file
		//See preview of both files

	});

	it( 'Can remove one file when multiple files ser', () => {
		//Load form (multiple upload support true)
		//Add a field to file field
		//See add more button
		//Add file 2
		//See preview file 1 and 2
		//Add file 3
		//See preview of 1, 2 and 3
		//Remove file 2
		//See preview of 1 and 3
		//Remove file 1
		//See preview of file 3


	});
});