import NewForm from './index';
import {snapshotObjectKeysAndTypes} from "../../testUtil/snapshotObjectKeysAndTypes";

describe( 'NewForm export', () => {
	it( 'index has the right types and keys', () => {
		snapshotObjectKeysAndTypes(NewForm);
	});
});