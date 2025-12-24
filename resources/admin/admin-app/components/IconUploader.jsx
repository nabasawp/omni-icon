import { useState, useCallback, useRef } from 'react';
import { Button, Notice, TextControl, FormFileUpload } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import IconUpload from '~icons/tabler/upload';
import IconCheck from '~icons/tabler/check';
import IconAlertCircle from '~icons/tabler/alert-circle';

const IconUploader = ({ onUploadSuccess }) => {
	const [selectedFile, setSelectedFile] = useState(null);
	const [iconSet, setIconSet] = useState('');
	const [isUploading, setIsUploading] = useState(false);
	const [uploadStatus, setUploadStatus] = useState(null); // { type: 'success' | 'error', message: string }
	const [uploadResult, setUploadResult] = useState(null);
	const fileInputRef = useRef(null);

	const handleFileSelect = useCallback((event) => {
		const file = event.target.files?.[0];
		
		if (!file) {
			setSelectedFile(null);
			return;
		}

		// Validate file type
		if (!file.name.toLowerCase().endsWith('.svg')) {
			setUploadStatus({
				type: 'error',
				message: __('Please select an SVG file.', 'omni-icon'),
			});
			setSelectedFile(null);
			return;
		}

		// Validate file size (1MB max)
		if (file.size > 1024 * 1024) {
			setUploadStatus({
				type: 'error',
				message: __('File size must be less than 1MB.', 'omni-icon'),
			});
			setSelectedFile(null);
			return;
		}

		setSelectedFile(file);
		setUploadStatus(null);
		setUploadResult(null);
	}, []);

	const handleUpload = useCallback(async () => {
		if (!selectedFile) {
			return;
		}

		setIsUploading(true);
		setUploadStatus(null);
		setUploadResult(null);

		try {
			const formData = new FormData();
			formData.append('icon', selectedFile);
			
			if (iconSet.trim()) {
				formData.append('icon_set', iconSet.trim());
			}

			const response = await fetch(`${window.omniIconAdmin.apiUrl}/upload`, {
				method: 'POST',
				headers: {
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
				body: formData,
			});

			const data = await response.json();

			if (!response.ok) {
				throw new Error(data.message || __('Upload failed', 'omni-icon'));
			}

			setUploadStatus({
				type: 'success',
				message: data.message || __('Icon uploaded successfully!', 'omni-icon'),
			});
			
			setUploadResult(data);
			
			// Reset form
			setSelectedFile(null);
			if (fileInputRef.current) {
				fileInputRef.current.value = '';
			}

			// Notify parent component
			if (onUploadSuccess) {
				onUploadSuccess(data);
			}

		} catch (error) {
			setUploadStatus({
				type: 'error',
				message: error.message || __('An error occurred during upload.', 'omni-icon'),
			});
		} finally {
			setIsUploading(false);
		}
	}, [selectedFile, iconSet, onUploadSuccess]);

	const handleDismissNotice = useCallback(() => {
		setUploadStatus(null);
		setUploadResult(null);
	}, []);

	return (
		<div className="omni-icon-uploader">
			<div className="omni-icon-uploader-form">
				<div className="omni-icon-form-section">
					<h2>{__('Upload SVG Icon', 'omni-icon')}</h2>
					<p className="description">
						{__('Select an SVG file to upload. The file will be sanitized for security.', 'omni-icon')}
					</p>

					<div className="omni-icon-form-fields">
						<div className="omni-icon-form-field">
							<FormFileUpload
								accept=".svg,image/svg+xml"
								onChange={handleFileSelect}
								ref={fileInputRef}
								render={({ openFileDialog }) => (
									<Button
										variant="secondary"
										onClick={openFileDialog}
										icon={<IconUpload />}
									>
										{selectedFile ? selectedFile.name : __('Choose SVG File', 'omni-icon')}
									</Button>
								)}
							/>
							{selectedFile && (
								<div className="omni-icon-file-info">
									<IconCheck className="icon-success" />
									<span>{selectedFile.name}</span>
									<span className="file-size">
										({(selectedFile.size / 1024).toFixed(2)} KB)
									</span>
								</div>
							)}
						</div>

						<div className="omni-icon-form-field">
							<TextControl
								label={__('Icon Set (Optional)', 'omni-icon')}
								value={iconSet}
								onChange={setIconSet}
								placeholder="e.g., brand, social, custom"
								help={__('Group icons into sets. Icons in the same set will share a prefix. Leave empty for the default "local" set.', 'omni-icon')}
							/>
						</div>

						<div className="omni-icon-form-actions">
							<Button
								variant="primary"
								onClick={handleUpload}
								disabled={!selectedFile || isUploading}
								isBusy={isUploading}
							>
								{isUploading ? __('Uploading...', 'omni-icon') : __('Upload Icon', 'omni-icon')}
							</Button>
						</div>
					</div>
				</div>

				{uploadStatus && (
					<Notice
						status={uploadStatus.type}
						onRemove={handleDismissNotice}
						isDismissible
					>
						{uploadStatus.message}
					</Notice>
				)}

				{uploadResult && uploadResult.icon_name && (
					<div className="omni-icon-upload-result">
						<h3>{__('Upload Successful!', 'omni-icon')}</h3>
						<div className="omni-icon-result-preview">
							<div className="omni-icon-preview-icon">
								<omni-icon
									name={uploadResult.icon_name}
									width="64"
									height="64"
								/>
							</div>
							<div className="omni-icon-result-info">
								<div className="info-row">
									<strong>{__('Icon Name:', 'omni-icon')}</strong>
									<code>{uploadResult.icon_name}</code>
								</div>
								<div className="info-row">
									<strong>{__('Filename:', 'omni-icon')}</strong>
									<span>{uploadResult.filename}</span>
								</div>
								<div className="info-row">
									<strong>{__('Usage:', 'omni-icon')}</strong>
									<code>{`<omni-icon name="${uploadResult.icon_name}"></omni-icon>`}</code>
								</div>
							</div>
						</div>
					</div>
				)}
			</div>

			<div className="omni-icon-uploader-info">
				<div className="info-box">
					<h3>{__('Guidelines', 'omni-icon')}</h3>
					<ul>
						<li>
							<IconAlertCircle className="icon-info" />
							{__('Only SVG files are accepted', 'omni-icon')}
						</li>
						<li>
							<IconAlertCircle className="icon-info" />
							{__('Maximum file size: 1MB', 'omni-icon')}
						</li>
						<li>
							<IconAlertCircle className="icon-info" />
							{__('Files will be automatically sanitized for security', 'omni-icon')}
						</li>
						<li>
							<IconAlertCircle className="icon-info" />
							{__('Use descriptive filenames (they become the icon name)', 'omni-icon')}
						</li>
					</ul>
				</div>

				<div className="info-box">
					<h3>{__('Icon Sets', 'omni-icon')}</h3>
					<p>
						{__('Organize your icons into sets for better management. For example:', 'omni-icon')}
					</p>
					<ul>
						<li><code>brand</code> → <code>brand:logo</code></li>
						<li><code>social</code> → <code>social:facebook</code></li>
						<li><code>custom</code> → <code>custom:icon</code></li>
					</ul>
					<p>
						{__('Icons without a set will use the "local" prefix.', 'omni-icon')}
					</p>
				</div>
			</div>
		</div>
	);
};

export default IconUploader;
