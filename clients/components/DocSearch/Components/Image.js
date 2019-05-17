import React from 'react';

export default function (props) {
    return (
        <img
            src={props.src}
            className={props.className}
            width={props.width}
            height={props.height}
            style={props.style}
            alt={props.alt}
            style={
                {
                    width: '100%',
                    height: 'auto'
                }
            }
        />
    )
}

