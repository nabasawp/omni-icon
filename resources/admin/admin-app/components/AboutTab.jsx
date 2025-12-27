import { __ } from '@wordpress/i18n';

const AboutTab = () => {
	const version = window.omniIconAdmin?.version;
	
	return (
		<div className="omni-icon-tab-content omni-icon-about-tab">
			<div className="about-hero">
				<div className="hero-icon">
					<omni-icon name="omni:omni-icon" width="64" height="64"></omni-icon>
				</div>
				<h1>{__('Omni Icon', 'omni-icon')}</h1>
				<p className="hero-tagline">
					{__('Enterprise-grade icon management for WordPress', 'omni-icon')}
				</p>
				<p className="hero-description">
					{__('A modern WordPress plugin that seamlessly integrates icons across the WordPress ecosystem with support for multiple page builders, custom icon uploads, and access to 200,000+ icons from Iconify.', 'omni-icon')}
				</p>
			</div>

			<div className="about-grid">
				<div className="about-card feature-card">
					<div className="card-icon">
						<omni-icon name="lucide:database" width="32" height="32"></omni-icon>
					</div>
					<h3>{__('Multi-source Icon System', 'omni-icon')}</h3>
					<p>{__('Upload custom icons, use bundled icons, or access 200,000+ Iconify icons. Three powerful sources, one unified interface.', 'omni-icon')}</p>
				</div>

				<div className="about-card feature-card">
					<div className="card-icon">
						<omni-icon name="lucide:zap" width="32" height="32"></omni-icon>
					</div>
					<h3>{__('Server-Side Rendering', 'omni-icon')}</h3>
					<p>{__('Icons pre-rendered on server for instant display with multi-layer caching (memory, filesystem, IndexedDB) for optimal performance.', 'omni-icon')}</p>
				</div>

				<div className="about-card feature-card">
					<div className="card-icon">
						<omni-icon name="lucide:code" width="32" height="32"></omni-icon>
					</div>
					<h3>{__('Web Component', 'omni-icon')}</h3>
					<p>{__('Use the <omni-icon> custom element anywhere in your theme or content with attribute reactivity and lazy loading.', 'omni-icon')}</p>
				</div>

				<div className="about-card feature-card">
					<div className="card-icon">
						<omni-icon name="lucide:shield-check" width="32" height="32"></omni-icon>
					</div>
					<h3>{__('Secure & Modern', 'omni-icon')}</h3>
					<p>{__('SVG sanitization prevents XSS attacks. Built with PHP 8.2+ attributes, Symfony DI, and auto-discovery architecture.', 'omni-icon')}</p>
				</div>
			</div>

			<div className="about-section-divider"></div>

			<div className="about-footer-section">
				<div className="footer-grid">
					<div className="footer-card">
						<div className="footer-card-icon">
							<omni-icon name="lucide:github" width="24" height="24"></omni-icon>
						</div>
						<div className="footer-card-content">
							<h4>{__('GitHub Repository', 'omni-icon')}</h4>
							<p>
								<a href="https://github.com/nabasa-dev/omni-icon" target="_blank" rel="noopener noreferrer">
									nabasa-dev/omni-icon
								</a>
							</p>
						</div>
					</div>

					<div className="footer-card">
						<div className="footer-card-icon">
							<omni-icon name="lucide:circle-check-big" width="24" height="24"></omni-icon>
						</div>
						<div className="footer-card-content">
							<h4>{__('Current Version', 'omni-icon')}</h4>
							<p>{version}</p>
						</div>
					</div>

					<div className="footer-card">
						<div className="footer-card-icon">
							<omni-icon name="lucide:star" width="24" height="24"></omni-icon>
						</div>
						<div className="footer-card-content">
							<h4>{__('Open Source', 'omni-icon')}</h4>
							<p>
								<a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener noreferrer">
									GPL-3.0 License
								</a>
							</p>
						</div>
					</div>

					<div className="footer-card">
						<div className="footer-card-icon">
							<omni-icon name="lucide:facebook" width="24" height="24"></omni-icon>
						</div>
						<div className="footer-card-content">
							<h4>{__('Facebook Community', 'omni-icon')}</h4>
							<p>
								<a href="https://www.facebook.com/groups/1142662969627943" target="_blank" rel="noopener noreferrer">
									Join our community
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div className="about-section-divider"></div>

			<div className="about-support-section">
				<div className="support-header">
					<div className="support-icon">
						<omni-icon name="lucide:heart" width="48" height="48"></omni-icon>
					</div>
					<h2>{__('Love This Plugin?', 'omni-icon')}</h2>
					<p className="support-description">
						{__('Omni Icon is 100% free and open source, crafted with passion for the WordPress community. Your sponsorship helps maintain and improve all our free WordPress plugins, not just Omni Icon. Supporting one plugin means supporting all our open-source efforts!', 'omni-icon')}
					</p>
				</div>

				<div className="support-actions">
					<a
						href="https://github.com/sponsors/suasgn"
						target="_blank"
						rel="noopener noreferrer"
						className="support-button primary github-sponsor"
					>
						<omni-icon name="lucide:github" width="24" height="24"></omni-icon>
						<div className="button-content">
							<span className="button-label">{__('GitHub Sponsors', 'omni-icon')}</span>
						</div>
					</a>

					<a
						href="https://ko-fi.com/Q5Q75XSF7"
						target="_blank"
						rel="noopener noreferrer"
						className="support-button primary kofi-sponsor"
					>
						<omni-icon name="simple-icons:kofi" width="24" height="24"></omni-icon>
						<div className="button-content">
							<span className="button-label">{__('Support via Ko-fi', 'omni-icon')}</span>
						</div>
					</a>
				</div>

				<div className="sponsors-showcase">
					<h3 className="sponsors-title">{__('Proudly Sponsored By', 'omni-icon')}</h3>
					<div className="sponsors-grid">
						<a
							href="https://windpress.com"
							target="_blank"
							rel="noopener noreferrer"
							className="sponsor-card"
						>
							<div className="sponsor-logo">
								<omni-icon name="omni:windpress" width="56" height="56"></omni-icon>
							</div>
							<div className="sponsor-info">
								<h4>WindPress</h4>
								<p>{__('Tailwind CSS for WordPress', 'omni-icon')}</p>
							</div>
						</a>

						<a
							href="https://livecanvas.com"
							target="_blank"
							rel="noopener noreferrer"
							className="sponsor-card"
						>
							<div className="sponsor-logo">
								<omni-icon name="omni:livecanvas" width="56" height="56"></omni-icon>
							</div>
							<div className="sponsor-info">
								<h4>LiveCanvas</h4>
								<p>{__('Visual Site Builder for WordPress', 'omni-icon')}</p>
							</div>
						</a>
					</div>
				</div>

				<div className="sponsorship-benefits">
					<h3>{__('Sponsorship Benefits', 'omni-icon')}</h3>
					<ul>
						<li>
							<div className="benefit-icon">
								<omni-icon name="lucide:package" width="18" height="18"></omni-icon>
							</div>
							<span>{__('Your brand icon bundled in releases', 'omni-icon')}</span>
						</li>
						<li>
							<div className="benefit-icon">
								<omni-icon name="lucide:file-text" width="18" height="18"></omni-icon>
							</div>
							<span>{__('Logo featured in all plugin READMEs', 'omni-icon')}</span>
						</li>
						<li>
							<div className="benefit-icon">
								<omni-icon name="lucide:award" width="18" height="18"></omni-icon>
							</div>
							<span>{__('Featured in all plugin admin pages', 'omni-icon')}</span>
						</li>
						<li>
							<div className="benefit-icon">
								<omni-icon name="lucide:users" width="18" height="18"></omni-icon>
							</div>
							<span>{__('Exposure to thousands of developers', 'omni-icon')}</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
	);
};

export default AboutTab;
