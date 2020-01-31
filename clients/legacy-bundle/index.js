import wpBabel from '@wordpress/babel-preset-default';
import browserlistConfig from '@wordpress/browserslist-config';
import data from '@wordpress/data';
import deprecated from '@wordpress/deprecated';
import domReady from '@wordpress/dom-ready';
import el from '@wordpress/element';
import isShallowEqual from '@wordpress/is-shallow-equal';
import reduxRoutine from '@wordpress/redux-routine';
import components from '@wordpress/components';
import blocks from '@wordpress/blocks';
import utils from '@wordpress/editor';
import date from '@wordpress/editor';
import editor from '@wordpress/editor';
function wpDependencies(){

    const dependencies = wpBabel + browserlistConfig + data + deprecated + 
    domReady + el + isShallowEqual + reduxRoutine + components +
    utils + date + editor + blocks;
    
    return dependencies;
}
wpDependencies();
