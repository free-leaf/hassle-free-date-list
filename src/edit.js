import { __ } from '@wordpress/i18n';
import './editor.scss';
import {
	useBlockProps,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';

import {
	PanelBody,
	Placeholder,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';

import { calendar } from '@wordpress/icons';

import ServerSideRender from '@wordpress/server-side-render';
const {
	Fragment,
} = wp.element;

import { useState } from '@wordpress/element';

export default function Edit( { attributes, setAttributes, className } ) {
	const [ isSelected, setIsSelected ] = useState( attributes.sID );
	const {
		sID,
		layout,
		timeLayout,
		largeNumber,
		boldDate,
		largeTime,
		dateColor,
		dateBgColor,
		timeColor,
		timeBgColor,
		closedColor,
		closedBgColor,
		fullColor,
		fullBgColor,
	}=attributes;

	let selectOption=[];
	hfdlOpt.forEach(element => {
		selectOption.push({
					label:element['title'],
					value:element['id']
				});
	});
	selectOption.push({
		label:__('Select Date List','hassle-free-date-list'),
		value:'',
		disabled: true,
	});

	function onSelectSID(value){
		setAttributes( { sID: value } );
		setIsSelected(value);
	}

	if( isSelected < 1 ){
		return(
			<div {...useBlockProps({
				className: className,
			})}
			>
				<Placeholder icon={ calendar } label={__('Select Date List','hassle-free-date-list')}>
				<SelectControl
					value={ sID }
					options={selectOption}
					onChange={( value ) => onSelectSID(value)}
				/>
				</Placeholder>
		</div>
		);
	}

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ __('Date List Setting','hassle-free-date-list') }>
				<SelectControl
						label={ __('Date List ID','hassle-free-date-list') }
						value={ sID }
						options={selectOption}
						onChange={ ( value ) => onSelectSID(value) }
					/>
					<SelectControl
						label={ __('Date List layout','hassle-free-date-list') }
						value={ layout }
						options={ [
							{ label: __('row','hassle-free-date-list'), value: 'row' },
							{ label: __('column','hassle-free-date-list'), value: 'col' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
					<SelectControl
						label={ __('Time Layout','hassle-free-date-list') }
						value={ timeLayout }
						options={ [
							{ label: __('horizontal','hassle-free-date-list'), value: '1row' },
							{ label: __('break date and time','hassle-free-date-list'), value: '2row' },
							{ label: __('vertical','hassle-free-date-list'), value: 'rows' },
						] }
						onChange={ ( value ) => setAttributes( { timeLayout: value } ) }
					/>
					<ToggleControl
						label={ __('Large date number','hassle-free-date-list') }
						checked={ largeNumber }
						value={ largeNumber }
						onChange={ ( value ) => setAttributes( { largeNumber: value } ) }
					/>
					<ToggleControl
						label={ __('Bold date','hassle-free-date-list') }
						checked={ boldDate }
						value={ boldDate }
						onChange={ ( value ) => setAttributes( { boldDate: value } ) }
					/>
					<ToggleControl
						label={ __('Large time number','hassle-free-date-list') }
						checked={ largeTime }
						value={ largeTime }
						onChange={ ( value ) => setAttributes( { largeTime: value } ) }
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __('Color Setting','hassle-free-date-list') }
					initialOpen={ false }
					colorSettings={ [
						{
							value: dateBgColor,
							onChange: ( value ) => setAttributes( { dateBgColor: value } ),
							label: __('Date Background','hassle-free-date-list'),
						},
						{
							value: dateColor,
							onChange: ( value ) => setAttributes( { dateColor: value } ),
							label: __('Date Text','hassle-free-date-list'),
						},
						{
							value: timeBgColor,
							onChange: ( value ) => setAttributes( { timeBgColor: value } ),
							label: __('Time Background','hassle-free-date-list'),
						},
						{
							value: timeColor,
							onChange: ( value ) => setAttributes( { timeColor: value } ),
							label: __('Time Text','hassle-free-date-list'),
						},
						{
							value: closedBgColor,
							onChange: ( value ) => setAttributes( { closedBgColor: value } ),
							label: __('Closed label Background','hassle-free-date-list'),
						},
						{
							value: closedColor,
							onChange: ( value ) => setAttributes( { closedColor: value } ),
							label: __('Closed label Text','hassle-free-date-list'),
						},
						{
							value: fullBgColor,
							onChange: ( value ) => setAttributes( { fullBgColor: value } ),
							label: __('Full label Background','hassle-free-date-list'),
						},
						{
							value: fullColor,
							onChange: ( value ) => setAttributes( { fullColor: value } ),
							label: __('Full label Text','hassle-free-date-list'),
						},
					] }
				/>
			</InspectorControls>
			<div {...useBlockProps({
				className: className,
			})}
			>
				<ServerSideRender
					block={"hassle-free-date-list/date-list"}
					attributes={ attributes }
				/>
			</div>
		</Fragment >
	);
}