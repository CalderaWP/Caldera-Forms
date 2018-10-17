import React from 'react';
import classNames from 'classnames'
import {TabPanel} from '@wordpress/components';
import proSettingsConfigFields from './proSettingsConfigFields';
import generalSettingsConfigFields from  './generalSettingsConfigFields';
import {SettingsGroup} from "./SettingsGroup";
import type {settingsType} from "../../types/types/settings";
import Grid from 'react-css-grid';
import {ProWhatIs} from "./ProSettings/ProWhatIs/ProWhatIs";
import {ProFreeTrial} from "./ProSettings/ProFreeTrial/ProFreeTrial";
import {ProEnterApp} from "./ProSettings/ProEnterApp/ProEnterApp";

type Props = {
	settings: settingsType,
	proConnected: boolean
}
/**
 * Creates the UI for Caldera FormsSlot global settings
 */
export class Settings extends React.PureComponent<Props> {

	/**
	 * Create Settings componet
	 * @param props
	 */
	constructor(props: Props) {
		super(props);
		(this: any).onSettingsSave = this.onSettingsSave.bind(this);
		(this: any).getConfigFields = this.getConfigFields.bind(this);
	}


	/**
	 * Dispatches settings to parent on save
	 * @param update
	 */
	onSettingsSave(update) {
		this.props.updateSettings(update);
	};


	getConfigFields(tabName){
		switch(tabName){
			case 'generalSettings':
				return generalSettingsConfigFields;
			case 'apiKeys':
				return proSettingsConfigFields.apiKeys;
			case 'proGeneral':
				return proSettingsConfigFields.generalSettings;
			default:
				return [];
		}
	}

	/**
	 * Creat main Caldera FormsSlot settings UI
	 * @return {*}
	 */
	render() {
		const {settings,onSettingsSave} = this.props;
		return (
			<div>
				<TabPanel
					orientation={'horizontal'}
					className={
						classNames(Settings.classNames.wrapper, this.props.classNames)
					}
					activeClass={
						classNames(Settings.classNames.active)
					}
					tabs={[
						{
							name: 'generalSettings',
							title: 'Global Form Settings',
						},
						{
							name: 'apiKeys',
							title: 'Api Keys',
							className: 'privacy-settings',
						},
						{
							name: 'proGeneral',
							title: 'Caldera Forms Pro',
							className: 'pro-general-settings',
						}
					]}
				>
					{
						(tabName) => {
							return <SettingsGroup
								settings={settings}
								settingsKey={tabName}
								onSettingsSave={onSettingsSave}
								configFields={this.getConfigFields(tabName)}
							/>
						}
					}
				</TabPanel>
				<Grid>
					{this.props.proConnected &&
						<React.Fragment>
							<ProWhatIs/>
							<ProFreeTrial/>

						</React.Fragment>
					}
					{this.props.proConnected &&
						<React.Fragment>
							<ProEnterApp/>
						</React.Fragment>
					}




				</Grid>
			</div>

		);
	}
};





/**
 * Class names used in the Settings component
 * @type {{wrapper: string}}
 */
Settings.classNames = {
	wrapper: 'caldera-forms-global-settings',
	active: 'caldera-forms-settings-tab-active'
};