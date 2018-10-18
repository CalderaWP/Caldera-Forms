import React from 'react';
import { createStore } from 'redux'
import { Provider } from 'react-redux'
import reducer from './reducers'
import ReactDOM from "react-dom";
import {PrivacySettingsWrapped} from "./containers/PrivacySettings";

Object.defineProperty( global.wp, 'element', {
    get: () => require( 'react' )
} );

/**
 *
 * @type {*}
 */
const store = createStore(
    reducer,
);

/**
 *
 * @type {string}
 */
const ID = 'caldera-forms-privacy-settings';

ReactDOM.render(
    <Provider store={store}>
        <PrivacySettingsWrapped />
    </Provider>,
    document.getElementById(ID)
);