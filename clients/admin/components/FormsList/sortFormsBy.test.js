
import {sortFormsBy} from "./sortFormsBy";

describe( ' ', () => {
	const forms = [
		{
			"updated_at" : "2012-01-01T06:25:24Z",
			"name" : "old"
		},
		{
			"updated_at" : "2014-01-09T11:25:13Z",
			"name" : "new"
		},
		{
			"updated_at" : "2012-08-05T04:13:24Z",
			"name" : "middle"
		}
	];
	it( 'Sorts by date', () => {
		const sorted = sortFormsBy('updated_at', forms );
		expect(sorted[0].name).toBe( 'new')
		expect(sorted[1].name).toBe( 'middle')
		expect(sorted[2].name).toBe( 'old')
	} );
	it( 'Sorts by name', () => {
		const sorted = sortFormsBy('name', forms );
		expect(sorted[0].name).toBe( 'middle')
		expect(sorted[1].name).toBe( 'new')
		expect(sorted[2].name).toBe( 'old')
	} );
});