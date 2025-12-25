import { __ } from '@wordpress/i18n';

const HelpTab = () => {
	return (
		<div className="omni-icon-tab-content omni-icon-help-tab">
			<div className="tab-content-inner">
				<h2>{__('Help & Documentation', 'omni-icon')}</h2>
				<p className="description">
					{__('Documentation and help resources coming soon.', 'omni-icon')}
				</p>
			</div>
		</div>
	);
};

export default HelpTab;
