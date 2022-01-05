import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

import Edit from './edit';

registerBlockType('hassle-free-date-list/date-list', {
	supports: {
		anchor: true,
		align:[ 'left', 'right', 'center' ]
	},
	attributes: {
		sID: {
			type:'string',
			default:'',
		},
		layout: {
			type: 'string',
			default: 'row',
		},
		timeLayout: {
			type: 'string',
			default: 'row',
		},
		largeNumber: {
			type: 'boolean',
			default: false,
		},
		boldDate: {
			type: 'boolean',
			default: false,
		},
		largeTime: {
			type: 'boolean',
			default: false,
		},
		dateColor: {
			type: 'string',
			default: ''
		},
		dateBgColor: {
			type: 'string',
			default: ''
		},
		timeColor: {
			type: 'string',
			default: ''
		},
		timeBgColor: {
			type: 'string',
			default: ''
		},
		closedColor: {
			type: 'string',
			default: ''
		},
		closedBgColor: {
			type: 'string',
			default: ''
		},
		fullColor: {
			type: 'string',
			default: ''
		},
		fullBgColor: {
			type: 'string',
			default: ''
		},
	},
	edit: Edit,
});
