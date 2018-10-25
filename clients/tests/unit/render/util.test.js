import {hashFiles} from "../../../render/util";

const CryptoJS = require("crypto-js");

var fs = require('fs');
//979296365572B262DBDA9C5186C7D0BE
const expectHash = '979296365572b262dbda9c5186c7d0be';

describe( 'File hashing', () => {
	it( 'Has php-compatible md5 file function', () => {
		const contents = fs.readFileSync(__dirname + '/screenshot.jpeg');
		const foundHash = CryptoJS.MD5(contents).toString();
		expect(foundHash).toEqual(expectHash );

	});

	it( 'Function hashes array', () => {
		const hashes = hashFiles( [
			fs.readFileSync(__dirname + '/screenshot.jpeg')
		]);
		expect( hashes[0] ).toBe( expectHash );
	})
})