import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import IconManager from './IconManager';
import HelpTab from './HelpTab';
import AboutTab from './AboutTab';

const AdminApp = () => {
	const [refreshTrigger] = useState(0);
	const [activeTab, setActiveTab] = useState('icons');

	const tabs = [
		{ id: 'icons', label: __('Icons', 'omni-icon') },
		// { id: 'help', label: __('Help', 'omni-icon') },
		{ id: 'about', label: __('About', 'omni-icon') },
	];

	return (
		<div className="omni-icon-admin-wrapper">
			<div className="omni-icon-admin-header">
				<h1>{__('Omni Icon', 'omni-icon')}</h1>
			</div>

			<nav className="omni-icon-admin-tabs">
				{tabs.map((tab) => (
					<button
						key={tab.id}
						className={`omni-icon-tab ${activeTab === tab.id ? 'is-active' : ''}`}
						onClick={() => setActiveTab(tab.id)}
					>
						{tab.label}
					</button>
				))}
			</nav>

			<div className="omni-icon-admin-content">
				{activeTab === 'icons' && <IconManager refreshTrigger={refreshTrigger} />}
				{activeTab === 'help' && <HelpTab />}
				{activeTab === 'about' && <AboutTab />}
			</div>
		</div>
	);
};

export default AdminApp;
