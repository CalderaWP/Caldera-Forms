export const mockSTATE = {
	loading: false,
	connected: true,
	forms: [
		{ form_id : 1, name: 'One', layout: 2, pdf_layout:1 },
		{ form_id : 2, name: 'Two', layout: 1, pdf_layout:2 },
	],
	settings : {
		enhancedDelivery: true,
		generatePDFs: false
	},
	layouts : [
		{ id: 1, name: 'One' },
		{ id: 2, name: 'Two' }
	],
	account: {
		plan: 'apex',
		id: 42,
		apiKeys: {
			public: 'public',
			secret: 'secret',
			token: 'token'
		}
	}

};