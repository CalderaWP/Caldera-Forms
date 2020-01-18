import React, {Fragment} from "react";
import {NewConditionalButton} from "./Conditionals";

/**
 * UI for adding a conditional group
 *
 * @since 1.8.10
 *
 * @param strings
 * @param onNewConditional
 * @returns {*}
 * @constructor
 */
export const NewConditionalGroup = ({strings, onNewConditional}) => {
    //Tracks the new group's name
    const [newGroupName, setNewGroupName] = React.useState('');

    //Tracks the new group's ID
    const [newGroupId, setNewGroupId] = React.useState('');

    //Should we show new group name input?
    const [showNewGroupName, setShowNewGroupName] = React.useState(false);

    //Ref for new group name input so we can control focus.
    let newNameInputRef = React.useRef('');

    /**
     * When new group name input is blurred:
     * * If has name, add group and hide new group name.
     * * If no name, hide new group name.
     *
     * @since 1.8.10
     *
     */
    const onNewBlur = () => {
        if (newGroupName.length) {
            onNewConditional(newGroupName, newGroupId);
            setShowNewGroupName(false);
        } else {
            setShowNewGroupName(false);
            setNewGroupName('');
            setNewGroupId('');
        }
    };

    /**
     * When we click on the new group button:
     * * Create ID for group
     * * Show new group name input
     * * Focus new group name input
     *
     * @since 1.8.10
     */
    const onClickNew = () => {
        const id = `con_${Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5)}`;
        setNewGroupId(id);
        setShowNewGroupName(true);
        setNewGroupName('');
        newNameInputRef.current.focus();
    };

    return (
        <Fragment>
            {!showNewGroupName && <NewConditionalButton
                text={strings['new-conditional']}
                onClick={onClickNew}
            />}

            <input
                ref={newNameInputRef}
                type="text"
                name={`conditions[${newGroupId}][name]`}
                value={newGroupName}
                className="condition-new-group-name"
                placeholder={strings['new-group-name']}
                style={{width: "100%",display:showNewGroupName ? 'block' : 'none'}}
                onChange={(e) => setNewGroupName(e.target.value)}
                onBlur={onNewBlur}
            />

        </Fragment>
    )

}