import React from 'react';
import ReactDOM from "react-dom";
import PropTypes from 'prop-types';

/**
 * Render children using a Portal
 *
 * @see https://reactjs.org/docs/portals.html
 *
 * @since 1.8.0
 *
 * @param props
 * @return {{$$typeof, key, children, containerInfo, implementation}}
 * @constructor
 */
export const RenderComponentViaPortal = (props) => {
	const {children,domNode} = props;
	return ReactDOM.createPortal(
		children,
		domNode
	);
};

/**
 * Prop type definitions for a RenderComponentViaPortal component
 *
 * @type {{children: (e|*), domNode: (e|*)}}
 */
RenderComponentViaPortal.propTypes = {
	children: PropTypes.any.isRequired,
	domNode: PropTypes.instanceOf(Element).isRequired
};