import React from  'react';
import PropTypes from 'prop-types';
export  const StatusIndicator = (props) =>{
    if( ! props.show){
        return null;
    }
    let className = 'cf-alert';
    if( props.success){
        className = `${className} cf-alert-success`;
    }else{
        className = `${className} cf-alert-error`;
    }

    return(
        <div className={"cf-alert-wrap"}>
            <p
                className={className}
            >
                {props.message}
            </p>
        </div>
    )



};
StatusIndicator.propTypes = {
    message: PropTypes.string,
    success: PropTypes.bool,
    show: PropTypes.bool,
};