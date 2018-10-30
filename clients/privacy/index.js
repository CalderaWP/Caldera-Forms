import React from 'react';
import { createStore } from 'redux'
import { Provider } from 'react-redux'
import reducer from './reducers'
import ReactDOM from "react-dom";
import {PrivacySettingsWrapped} from "./containers/PrivacySettings";
/**
 *
 * @type {string}
 */
const ID = 'caldera-forms-privacy-settings';
const element =     document.getElementById(ID);

if( null === element  ){
    return;
}
global.wp = global.wp || {};

Object.defineProperty( global.wp, 'element', {
    get: () => React
} );

/**
 *
 * @type {*}
 */
const store = createStore(
    reducer,
);



ReactDOM.render(
    <Provider store={store}>
        <PrivacySettingsWrapped />
    </Provider>,
    document.getElementById(ID)
);
