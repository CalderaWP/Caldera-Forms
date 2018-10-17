import React from "react";
import entryType from './entryType'
import classNames from 'classnames'
import PropTypes from 'prop-types';
import {Button} from '@wordpress/components';
import Grid from 'react-css-grid'


export const Entry = (props) => {
	const {gridCollapse,gridGap} = props;


	return (
		<div>
			<Grid
				width={gridCollapse}
				gap={gridGap}

			>
				<h3

				>
					Entry {props.id}
				</h3>
				<Button
					icon={'no'}
					title={'Click To Close Entry'}
					onClick={props.onClose}
				>
					Close
				</Button>
			</Grid>

			<Grid
				width={gridCollapse}
				gap={gridGap}
			>
				<div className="caldera-forms-entry-left">
					<div
						className={classNames(
							{
								'user-avatar': props.user.id && 0 < props.user.id
							},
							`user-avatar-${props.user.id}`
						)}
						title={props.user.name}
						style={{
							marginTop: '-1px'
						}}
					>
						<img
							alt={`User Avatar for ${props.user.name}`}
							src={props.user.avatar}
						/>
					</div>

				</div>

				<div
					className={
						classNames('caldera-forms-entry-right', 'tab-detail-panel')
					}
				>
					<ul>

						{
							props.fields.map(field => {
							return (
								<li
									className="entry-detail"
									key={field.id}
								>
									<span className="entry-label">
										{field.label ? field.label : field.slug}
									</span>
									<span className="entry-content">
										{field.value}
									</span>
								</li>
							)
						})}
					</ul>
				</div>
			</Grid>
		</div>

	);
};

/**
 *
 * @type {{fields, user, id, form: shim}}
 */
Entry.propTypes = {
	...entryType,
	form: PropTypes.object.isRequired,
	onClose: PropTypes.func.isRequired,
	gridCollapse: PropTypes.number,
	gridGap: PropTypes.number
};

/**
 *
 * @type {{user: {id: string, avatar: string, name: string}}}
 */
Entry.defaultProps = {
	user: {
		id: '',
		avatar: '',
		name: ''
	},
	gridCollapse: 320,
	gridGap: 24
};