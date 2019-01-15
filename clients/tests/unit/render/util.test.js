import {getFieldConfigBy} from "../../../render/util";
import {formRenderTestProps} from "./CalderaFormsRender.test";
describe(  'getFieldConfigBy', () => {
	it ( 'finds fields that exits by fieldId', () => {
		expect( getFieldConfigBy(formRenderTestProps.fieldsToControl, 'fieldId', 'fld_5899467' ).fieldIdAttr).toBe( 'fld_5899467_1' )
	});

	it ( 'finds no fields when searching by invalid field id', () => {
		expect( getFieldConfigBy(formRenderTestProps.fieldsToControl, 'fieldId', 'fld_1' )).toBe( undefined )
	});

});

