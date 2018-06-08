import * as cfStateWebpack from '@caldera-labs/state'
describe( 'Testing', ()=> {
    it( 'Works', ()=> {
        const roy= true;
        expect(roy).toBe(roy);
    });

    it( 'Can snapshot', ()=> {
        const mike = {
            roy:true
        };
        expect(JSON.stringify(mike)).toMatchSnapshot();
    });

    it( 'Can use imported state',  () => {
        expect(cfStateWebpack.store.actions.setForm({name:1})).toEqual({ type: 'SET_FORM', form: { name: 1 } });
    });

    it( 'Can import CF state', () => {
        expect(cfStateWebpack).toHaveProperty('store');
        expect(cfStateWebpack).toHaveProperty('state');
    })
})