import FormListComponents from './index';
import {snapshotObjectKeysAndTypes} from "../../testUtil/snapshotObjectKeysAndTypes";

describe( 'Form List components export', () => {
	it( 'matches snapshot', () => {
		snapshotObjectKeysAndTypes(FormListComponents);
	});
});