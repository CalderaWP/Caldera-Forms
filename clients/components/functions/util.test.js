import {optionFactory} from "./optionFactory";
import {pickArray} from "./pickArray";
import {findFieldById} from "./findFieldById";

describe('optionFactory ', () => {
	const value = 1;
	const label = 'One';
	it('creates value-label', () => {
		expect(optionFactory(value, label)).toEqual({
			value,
			label
		});
	});

	it('Uses value as label if label is not provided', () => {
		expect(optionFactory(value)).toEqual({
			value,
			label: value
		});
	});

	it('Passes the extra options', () => {
		expect(optionFactory(value, label, {mike: 'roy'})).toEqual({
			value,
			label,
			mike: 'roy'
		});
	});

	it('Extra options does not set value or label', () => {
		expect(optionFactory(value, label, {
			mike: 'roy',
			label: 'Pancakes',
			value: 'pans'

		})).toEqual({
			value,
			label,
			mike: 'roy'
		});
	});
});

describe('pickArray', () => {
	it('picks id', () => {
		expect(
			pickArray([{
				id: 1
			}, {
				id: 2,
			}],'id')
		).toEqual([1, 2])
	});

	it('picks column keys', () => {
		expect(
			pickArray([
				{key: 'a', name: 'ID'},
				{key: 'b', name: 'Count'}
			],'key')
		).toEqual(['a','b'])
	});


});

describe( 'findFieldById', () => {
	const field = {
		id: 'fld1',
		slug: 'name'
	};
	const form = {
		fields: {
			fld1 : field
		}
	};

	expect( findFieldById( 'fld1', form ) ).toEqual(field);
	expect( findFieldById( 'fld2', form ) ).toEqual(null);
});


