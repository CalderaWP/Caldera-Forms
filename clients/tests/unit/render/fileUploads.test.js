import {handleFileUploadResponse} from "./fileUploads";
import {cf2} from  './Mocks/mockUtils';
describe( 'Unit tests, ignoring cf2 var side effects for handleFileUploadResponse', () => {
	let $form;
	let submit;
	const field = cf2.fields.fld_9226671_1;

	beforeEach(() => {
	 	submit = jest.fn();
		let $form = jest.fn(() => ({
			submit
		}));

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
		expect( submit.mock.calls.length ).toBe(0);


	});

	it( 'Triggers submit, if passed object with control', () => {
		const r = handleFileUploadResponse(
			{control: 'nico'},
			cf2,
			$form,
			{},
			field
		);
		expect( submit.mock.calls.length ).toBe(1);


	});

	it( 'Puts error message from response in messages var if possible and throws error', () => {
		const response = undefined;
		let error = undefined;
		const message = 'An Error Has Occured';
		const messages = {};
		const {fieldId} = field;
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

		expect( messages[fieldId].error).toBe(true);
		expect( messages[fieldId].message).toEquals(message);
		expect( undefined === typeof error ).toBe(false);
		expect( submit.mock.calls.length ).toBe(0);
	});

	it( 'Throws error if response does not have control prop', () => {

	});

});
