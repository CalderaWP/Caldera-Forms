import renderer from "react-test-renderer";
import React from 'react';
import {mockEntries} from "../../test-data/mockEntries";
import {formwithIdCf1} from "../../test-data/forms";
import {Entry} from "./Entry";



describe( 'Entry component', () => {
	it( 'Matches snapshot', () => {
		const entry = mockEntries['32'];
		expect( entry.hasOwnProperty('user')).toBe(true);
		expect( renderer.create(
			<Entry
				fields={Object.values(entry.fields)}
				user={entry.user}
				id={entry.id}
				form={formwithIdCf1}
			/>
		).toJSON() ).toMatchSnapshot();
	});

	it( 'Handles not having a user', () => {
		const entry = mockEntries['32'];
		expect( entry.hasOwnProperty('user')).toBe(true);
		expect( renderer.create(
			<Entry
				fields={Object.values(entry.fields)}
				id={entry.id}
				form={formwithIdCf1}
			/>
		).toJSON() ).toMatchSnapshot();
	});
});