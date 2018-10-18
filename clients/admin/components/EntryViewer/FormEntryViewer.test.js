import renderer from "react-test-renderer";
import React from 'react';
import {FormEntryViewer} from "./FormEntryViewer";
import {shallow} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import {formWithIdCf2} from "../../test-data/forms";
import {getFormColumns} from "./getFormColumns";
import {formwithIdCf1} from "../../test-data/forms";
import getFormRows from "./getFormRows";
import {mockEntries} from "../../test-data/mockEntries";
const entries = mockEntries;
Enzyme.configure({adapter: new Adapter()});

const genericHandler = () => {};
describe( 'Prepare form columns', () => {

	it( 'gets the entry list fields and adds entry actions only by default', () => {
		expect(getFormColumns(formwithIdCf1).length).toBe(3);
	});

	it( 'gets all fields', () => {
		expect(getFormColumns(formwithIdCf1,false).length).toBe(6);
	});

	it( 'Sets name prop for each field', () => {
		Object.values(getFormColumns(formwithIdCf1,false)).forEach( column => {
			expect( column.hasOwnProperty('name')).toBe(true);
		})
	});


	it( 'does not get entryActions when arg is false all fields', () => {
		expect(getFormColumns(formwithIdCf1,true,false).length).toBe(2);
	});


});

describe( 'Prepare form rows', () => {
	it( 'Prepares all entries', () => {
		expect( getFormRows(entries,genericHandler()).length).toBe(Object.keys(entries).length);
	});

	it( 'gets the entry list fields and adds entry actions only by default', () => {
		const rows =  getFormRows(entries,genericHandler());
		expect(Object.keys(rows[0]).length).toBe(4);

	});

	const entryOneExpectedData = entries[Object.keys(entries)[0]];

	it( 'gets the entry list fields', () => {
		const rows =  getFormRows(entries,genericHandler());
		const row = rows[0];
		expect(row.hasOwnProperty('id')).toBe(true);
		expect(row.hasOwnProperty('datestamp')).toBe(true);
		expect(row.hasOwnProperty('entryActions')).toBe(true);
	});

	it( 'has the right values for the entry list fields', () => {
		const rows =  getFormRows(entries,genericHandler());
		const row = rows[0];
		expect(row.id).toEqual(entryOneExpectedData.id);
		expect(row.datestamp).toEqual(entryOneExpectedData.datestamp);
		expect(typeof row.entryActions).toEqual('object');
	});



	it( 'gets all fields', () => {
		const rows =  getFormRows(entries,genericHandler(),false);
		expect(Object.keys(rows[0]).length).toBe(8);
	});

	it( 'gets all field values', () => {
		const rows =  getFormRows(entries,genericHandler(),false);
		const row = rows[0];
		const {fields} = entryOneExpectedData;
		Object.keys(fields).forEach(fieldId => {
			expect(row[fieldId]).toEqual(fields[fieldId].value);
		});

	});

	it( 'does not get entryActions when arg is false, but does get all fields', () => {
		const rows =  getFormRows(entries,genericHandler(),false,false);
		expect(Object.keys(rows[0]).length).toBe(7);
	});


});

describe( 'Form entry viewer', () => {

	it( 'has working methods', () => {
		const component = shallow(
			<FormEntryViewer
				entries={entries}
				form={formWithIdCf2}
				getEntries={genericHandler}
				onPageNav={genericHandler}

			/>
		);

		expect(
			JSON.stringify(component.instance().getEntryFields(entries['32'] ) )
		).toMatchSnapshot();
	});
	it.skip( 'Matches snapshot', () => {
		expect(
			renderer.create(
				<FormEntryViewer
					entries={entries}
					form={formWithIdCf2}
					getEntries={genericHandler}
					onPageNav={genericHandler}
				/>
			).toJSON()
		).toMatchSnapshot();
	});


});


