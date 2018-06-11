import React from 'react';
import { createStore, applyMiddleware } from 'redux'
import { Provider } from 'react-redux'
import thunk from 'redux-thunk'
import reducer from './reducers'
import ReactDOM from "react-dom";
import {PrivacySettingsWrapped} from "./containers/PrivacySettings";

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