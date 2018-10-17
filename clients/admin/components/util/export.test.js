import util from './index';
import {snapshotObjectKeysAndTypes} from "../../testUtil/snapshotObjectKeysAndTypes";

describe( 'util  export', () => {
	it( 'index has the right types and keys', () => {
		snapshotObjectKeysAndTypes(util);
	});
});