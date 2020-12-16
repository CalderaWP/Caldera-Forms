import React from 'react';

export default function (props) {
    return (
        <img
            src={props.src}
            className={props.className}
            width={props.width}
            height={props.height}
            alt={props.alt}
            style={ props.style ? props.style :
                {
                    width: '100%',
                    height: 'auto'
                }
            }
            lazy="true"
        />
    )
}

