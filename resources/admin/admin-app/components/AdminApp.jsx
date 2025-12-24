import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import IconManager from './IconManager';

const AdminApp = () => {
	const [refreshTrigger] = useState(0);

	return (
		<div className="omni-icon-admin-wrapper">
			<div className="omni-icon-admin-header">
				<h1>{__('Omni Icon - Local Icon Management', 'omni-icon')}</h1>
				<p className="description">
					{__('Upload and manage your custom SVG icons. Uploaded icons will be available across all page builders and blocks.', 'omni-icon')}
				</p>
			</div>

			<div className="omni-icon-admin-content">
				<IconManager refreshTrigger={refreshTrigger} />
			</div>
		</div>
	);
};

export default AdminApp;
