import Settings from './index';
import {snapshotObjectKeysAndTypes} from "../../testUtil/snapshotObjectKeysAndTypes";

describe( 'Settings export', () => {
	it( 'index has the right types and keys', () => {
		snapshotObjectKeysAndTypes(Settings);
	});
});