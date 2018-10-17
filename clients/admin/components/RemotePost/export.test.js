import RemotePost from './index';
import {snapshotObjectKeysAndTypes} from "../../testUtil/snapshotObjectKeysAndTypes";

describe( 'RemotePost export', () => {
	it( 'index has the right types and keys', () => {
		snapshotObjectKeysAndTypes(RemotePost);
	});
});