describe( 'Testing', ()=> {
    it( 'Works', ()=> {
        const roy= true;
        expect(roy).toBe(roy);
    })
    it( 'Can snapshot', ()=> {
        const mike = {
            roy:true
        };
        expect(JSON.stringify(mike)).toMatchSnapshot();
    })
})