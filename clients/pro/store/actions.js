import { localAPI, appAPI, appToken } from './util/API';
import { urlString } from './util/urlString';
import CFProConfig from  './util/wpConfig';

export const ACTIONS = {
	getAccount(context){
		return new Promise((resolve, reject) => {
			localAPI.get().then(response => {
				var r;
				if ('string' == typeof response.data) {
					const maybe = JSON.parse(response.data);
					if ('object' === typeof  maybe) {
						r = maybe;
					}else{
                        context.commit('connected',false );
                        throw new Exception;
					}
				} else {
					r = response.data;
				}
				context.commit('forms', r.forms);
                context.commit('apiKeys', r.apiKeys);
                context.commit('connected',true);
                context.commit('accountId', r.account_id);
				context.commit('plan', r.plan);
				context.commit('logLevel', r.logLevel);
				context.commit('enhancedDelivery', r.enhancedDelivery);
				context.commit('formScreen', r.hasOwnProperty( 'formScreen' ) ? r.formScreen : CFProConfig.formScreen );
				resolve(response);
			}, error => {
				reject(error);
			});
		})
	},
	saveAccount(context) {
		let key = context.state.account.apiKeys.public;
		if( key && 'string' === typeof key ){
			key = key.trim();
		}
        let secret = context.state.account.apiKeys.secret;
        if( secret && 'string' === typeof secret ){
            secret = secret.trim();
        }

		return localAPI.post('', {
			accountId: context.state.account.id,
			apiKey: key,
			apiSecret: secret,
			enhancedDelivery: context.state.settings.enhancedDelivery,
			plan: context.state.account.plan,
			forms: context.state.forms,
			logLevel: context.state.settings.logLevel
		}).then(r => {
			if( r.data.hasOwnProperty( '_cfAlertMessage' ) ){
				context.dispatch( 'updateMainAlert', _cfAlertMessage );
			}else{
				context.dispatch( 'updateMainAlert', {
					message: context.state.strings.saved,
					show: true,
					success: true,
					fade: 1500
				});
			}
		});
	},
    openApp({commit, state}) {
        return new Promise((resolve, reject) => {
			const url = urlString(
                {
                    public: state.account.apiKeys.public,
                    token: appToken( state.account.apiKeys ),
                },
                'https://app.calderaformspro.com/app'
            );
            const win = window.open(url, '_blank');
            win.focus();
        	resolve(true);
        });
	},
	testConnection({commit, state}) {
		return new Promise((resolve, reject) => {
			if( state.connected ){
                resolve('Already Connected');
            }

			if ('string' === typeof state.account.apiKeys.public && 'string' === typeof state.account.apiKeys.secret) {
				return appAPI.get(
					urlString(
						{
							public: state.account.apiKeys.public,
							token: appToken( state.account.apiKeys ),
						},
						'/account/verify'
					)
				).then(r => {
						commit( 'connected', true );
                        state.account.plan = r.plan;
						state.account.id = r.id;
						resolve(r);
					},
					error => {
                        commit('connected',false);
                        reject(error);
					});

			}else{
				reject( 'Not Connected' );
			}

		});

	},
	getLayouts({commit, state}) {
		if( state.connected ){
			return appAPI.get(
				urlString(
					{
						simple: true,
						public: state.account.apiKeys.public,
						token: appToken( state.account.apiKeys ),
					},
					'/layouts/list'
				)
			).then(
				r => {
					commit( 'layouts', r.data );
				}, e => {
					console.log(e);
				}
			);
		}
	},
	/**
	 * Set the main alert -- status.
	 *
	 * Using this over mutation mainAlert, which this uses, is you can send a number of milliseconds in alert.fade and it will removed in that number of milliseconds
	 *
	 * @since 1.0.0
	 *
	 * @param {*} context
	 * @param {Object} alert Commit payload
	 */
	updateMainAlert(context, alert){
		const fade = ( alert.hasOwnProperty( 'fade' ) && ! isNaN( alert.fade ) ) ? alert.fade : 0;
		if( fade ){
			//OMG(s) window scope.
			window.setTimeout( () =>{
				context.dispatch( 'closeMainAlert' );
			}, fade );
		}
		context.commit('mainAlert',alert)
	},
	/**
	 * Make mainAlert clode
	 *
	 * @since 1.0.0
	 *
	 * @param context
	 */
	closeMainAlert(context){
		context.dispatch( 'updateMainAlert', {
			show:false,
		} );
	}
};
