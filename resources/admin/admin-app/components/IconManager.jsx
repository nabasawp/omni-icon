import { useState, useEffect, useCallback, useMemo, useRef } from 'react';
import { Button, Spinner, Notice, Modal, TextControl, FormFileUpload } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import IconTrash from '~icons/tabler/trash';
import IconRefresh from '~icons/tabler/refresh';
import IconSearch from '~icons/tabler/search';
import IconFolder from '~icons/tabler/folder';
import IconFolderPlus from '~icons/tabler/folder-plus';
import IconEdit from '~icons/tabler/edit';
import IconCheck from '~icons/tabler/check';
import IconX from '~icons/tabler/x';
import IconUpload from '~icons/tabler/upload';
import IconAlertCircle from '~icons/tabler/alert-circle';
import LocalIconItem from './LocalIconItem';

const IconManager = ({ refreshTrigger }) => {
	const [iconSets, setIconSets] = useState({});
	const [selectedSet, setSelectedSet] = useState('all');
	const [icons, setIcons] = useState([]);
	const [isLoading, setIsLoading] = useState(true);
	const [error, setError] = useState(null);
	const [searchQuery, setSearchQuery] = useState('');
	const [selectedIcon, setSelectedIcon] = useState(null);
	const [deleteModal, setDeleteModal] = useState(null);
	const [isDeleting, setIsDeleting] = useState(false);
	const [draggedIcon, setDraggedIcon] = useState(null);
	const [renameSet, setRenameSet] = useState(null); // { oldName: string, newName: string }
	const [isRenamingSet, setIsRenamingSet] = useState(false);
	const [createSetModal, setCreateSetModal] = useState(false);
	const [newSetName, setNewSetName] = useState('');
	const [isCreatingSet, setIsCreatingSet] = useState(false);
	const [uploadModal, setUploadModal] = useState(false);
	const [selectedFile, setSelectedFile] = useState(null);
	const [isUploading, setIsUploading] = useState(false);
	const [uploadStatus, setUploadStatus] = useState(null);
	const [uploadResult, setUploadResult] = useState(null);
	const fileInputRef = useRef(null);
	const gridRef = useRef(null);

	// Fetch icon sets
	const fetchIconSets = useCallback(async () => {
		try {
			const response = await fetch(`${window.omniIconAdmin.apiUrl}/sets`, {
				headers: {
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
			});

			if (!response.ok) {
				throw new Error(__('Failed to fetch icon sets', 'omni-icon'));
			}

			const data = await response.json();
			setIconSets(data.data || {});
		} catch (err) {
			setError(err.message);
		}
	}, []);

	// Fetch all icons or icons by set
	const fetchIcons = useCallback(async () => {
		setIsLoading(true);
		setError(null);

		try {
			let url = `${window.omniIconAdmin.apiUrl}/icons`;
			
			if (selectedSet !== 'all') {
				url = `${window.omniIconAdmin.apiUrl}/sets/${selectedSet}/icons`;
			}

			const response = await fetch(url, {
				headers: {
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
			});

			if (!response.ok) {
				throw new Error(__('Failed to fetch icons', 'omni-icon'));
			}

			const data = await response.json();
			setIcons(data.data || []);
		} catch (err) {
			setError(err.message);
		} finally {
			setIsLoading(false);
		}
	}, [selectedSet]);

	// Delete icon
	const handleDeleteIcon = useCallback(async (iconName) => {
		setIsDeleting(true);

		try {
			const response = await fetch(`${window.omniIconAdmin.apiUrl}/${iconName}`, {
				method: 'DELETE',
				headers: {
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
			});

			if (!response.ok) {
				const data = await response.json();
				throw new Error(data.message || __('Failed to delete icon', 'omni-icon'));
			}

			// Refresh icons and sets
			await fetchIcons();
			await fetchIconSets();
			
			setDeleteModal(null);
			setSelectedIcon(null);
		} catch (err) {
			setError(err.message);
		} finally {
			setIsDeleting(false);
		}
	}, [fetchIcons, fetchIconSets]);

	// Move icon to different set
	const handleMoveIcon = useCallback(async (iconName, targetSet) => {
		try {
			const response = await fetch(`${window.omniIconAdmin.apiUrl}/move`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
				body: JSON.stringify({
					icon_name: iconName,
					target_set: targetSet === 'local' ? null : targetSet,
				}),
			});

			if (!response.ok) {
				const data = await response.json();
				throw new Error(data.message || __('Failed to move icon', 'omni-icon'));
			}

			// Refresh icons and sets
			await fetchIcons();
			await fetchIconSets();
			setSelectedIcon(null);
		} catch (err) {
			setError(err.message);
		}
	}, [fetchIcons, fetchIconSets]);

	// Rename icon set
	const handleRenameSet = useCallback(async () => {
		if (!renameSet || !renameSet.newName.trim()) {
			return;
		}

		setIsRenamingSet(true);

		try {
			const response = await fetch(`${window.omniIconAdmin.apiUrl}/sets/${renameSet.oldName}/rename`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
				body: JSON.stringify({
					new_name: renameSet.newName.trim(),
				}),
			});

			if (!response.ok) {
				const data = await response.json();
				throw new Error(data.message || __('Failed to rename set', 'omni-icon'));
			}

			// If current selected set was renamed, update it
			if (selectedSet === renameSet.oldName) {
				setSelectedSet(renameSet.newName.trim());
			}

			// Refresh sets and icons
			await fetchIconSets();
			await fetchIcons();
			setRenameSet(null);
		} catch (err) {
			setError(err.message);
		} finally {
			setIsRenamingSet(false);
		}
	}, [renameSet, selectedSet, fetchIconSets, fetchIcons]);

	// Create new icon set
	const handleCreateSet = useCallback(async () => {
		if (!newSetName.trim()) {
			return;
		}

		setIsCreatingSet(true);

		try {
			const response = await fetch(`${window.omniIconAdmin.apiUrl}/sets/create`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
				body: JSON.stringify({
					set_name: newSetName.trim(),
				}),
			});

			if (!response.ok) {
				const data = await response.json();
				throw new Error(data.message || __('Failed to create set', 'omni-icon'));
			}

			// Refresh sets
			await fetchIconSets();
			setCreateSetModal(false);
			setNewSetName('');
			setSelectedSet(newSetName.trim());
		} catch (err) {
			setError(err.message);
		} finally {
			setIsCreatingSet(false);
		}
	}, [newSetName, fetchIconSets]);

	// Upload icon
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

	const handleUploadIcon = useCallback(async () => {
		if (!selectedFile) {
			return;
		}

		setIsUploading(true);
		setUploadStatus(null);
		setUploadResult(null);

		try {
			const formData = new FormData();
			formData.append('icon', selectedFile);
			
			// Use selected set from grid (not from input field)
			const targetSet = selectedSet === 'all' ? '' : selectedSet;
			if (targetSet) {
				formData.append('icon_set', targetSet);
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

			// Refresh icons and sets
			await fetchIcons();
			await fetchIconSets();

			// Auto close after 2 seconds on success
			setTimeout(() => {
				setUploadModal(false);
				setUploadStatus(null);
				setUploadResult(null);
			}, 2000);

		} catch (error) {
			setUploadStatus({
				type: 'error',
				message: error.message || __('An error occurred during upload.', 'omni-icon'),
			});
		} finally {
			setIsUploading(false);
		}
	}, [selectedFile, selectedSet, fetchIcons, fetchIconSets]);

	// Refresh data
	const handleRefresh = useCallback(async () => {
		try {
			// Clear cache first
			await fetch(`${window.omniIconAdmin.apiUrl}/cache/clear`, {
				method: 'POST',
				headers: {
					'X-WP-Nonce': window.omniIconAdmin.nonce,
				},
			});
		} catch (err) {
			// Silently fail cache clear, still continue with refresh
			console.warn('Failed to clear cache:', err);
		}
		
		await fetchIconSets();
		await fetchIcons();
	}, [fetchIconSets, fetchIcons]);

	// Icon selection
	const handleSelectIcon = useCallback((iconName) => {
		setSelectedIcon(prevIcon => prevIcon === iconName ? null : iconName);
	}, []);

	// Drag and drop handlers
	const handleDragStart = useCallback((e, icon) => {
		setDraggedIcon(icon);
		e.dataTransfer.effectAllowed = 'move';
	}, []);

	const handleDragEnd = useCallback(() => {
		setDraggedIcon(null);
	}, []);

	const handleDragOver = useCallback((e) => {
		e.preventDefault();
		e.dataTransfer.dropEffect = 'move';
	}, []);

	const handleDropOnSet = useCallback((e, targetSet) => {
		e.preventDefault();
		if (draggedIcon && draggedIcon.icon_set !== targetSet) {
			handleMoveIcon(draggedIcon.icon_name, targetSet);
		}
		setDraggedIcon(null);
	}, [draggedIcon, handleMoveIcon]);

	// Filter icons by search query
	const filteredIcons = useMemo(() => {
		if (!searchQuery.trim()) {
			return icons;
		}

		const query = searchQuery.toLowerCase();
		return icons.filter(icon => 
			icon.name.toLowerCase().includes(query) ||
			icon.icon_name.toLowerCase().includes(query)
		);
	}, [icons, searchQuery]);

	// Convert icons to IconItem format
	const iconItems = useMemo(() => {
		return filteredIcons.map(icon => ({
			name: icon.icon_name,
			iconName: icon.name,
			prefix: icon.icon_set || 'local',
		}));
	}, [filteredIcons]);

	// Get selected icon data
	const selectedIconData = useMemo(() => {
		return filteredIcons.find(icon => icon.icon_name === selectedIcon);
	}, [filteredIcons, selectedIcon]);

	// Initial load
	useEffect(() => {
		fetchIconSets();
		fetchIcons();
	}, [fetchIconSets, fetchIcons]);

	// Refresh when upload succeeds
	useEffect(() => {
		if (refreshTrigger > 0) {
			handleRefresh();
		}
	}, [refreshTrigger, handleRefresh]);

	// Calculate total count
	const totalCount = useMemo(() => {
		return Object.values(iconSets).reduce((sum, set) => sum + set.total, 0);
	}, [iconSets]);

	// Get target set display name for upload modal
	const targetSetDisplayName = useMemo(() => {
		if (selectedSet === 'all') {
			return __('Local (default)', 'omni-icon');
		}
		return iconSets[selectedSet]?.name || selectedSet;
	}, [selectedSet, iconSets]);

	return (
		<div className="omni-icon-manager">
			<div className="omni-icon-manager-header">
				<div className="header-info">
					<h2>{__('Manage Local Icons', 'omni-icon')}</h2>
					<p className="description">
						{totalCount > 0
							? __(`You have ${totalCount} custom icons across ${Object.keys(iconSets).length} sets.`, 'omni-icon')
							: __('No custom icons uploaded yet.', 'omni-icon')
						}
					</p>
				</div>
				<div className="header-actions">
					<Button
						variant="primary"
						onClick={() => setUploadModal(true)}
						icon={<IconUpload />}
					>
						{__('Upload Icon', 'omni-icon')}
					</Button>
					<Button
						variant="secondary"
						onClick={handleRefresh}
						disabled={isLoading}
						icon={<IconRefresh />}
					>
						{__('Refresh', 'omni-icon')}
					</Button>
				</div>
			</div>

			{error && (
				<Notice status="error" onRemove={() => setError(null)} isDismissible>
					{error}
				</Notice>
			)}

			{/* Icon Set Grid */}
			<div className="omni-icon-set-grid-wrapper">
				<div className="set-grid-label">
					<IconFolder />
					<span>{__('Icon Sets', 'omni-icon')}</span>
				</div>
				<div className="omni-icon-set-grid">
					{/* All Icons Set */}
					<button
						className={`set-card ${selectedSet === 'all' ? 'is-active' : ''}`}
						onClick={() => setSelectedSet('all')}
					>
						<div className="set-card-icon">
							<IconFolder />
						</div>
						<div className="set-card-info">
							<span className="set-card-name">{__('All Icons', 'omni-icon')}</span>
							<span className="set-card-count">{totalCount} {__('icons', 'omni-icon')}</span>
						</div>
					</button>

					{/* Existing Sets */}
					{Object.entries(iconSets).map(([prefix, set]) => (
						<button
							key={prefix}
							className={`set-card ${selectedSet === prefix ? 'is-active' : ''} ${draggedIcon && draggedIcon.icon_set !== prefix ? 'is-drop-target' : ''}`}
							onClick={() => setSelectedSet(prefix)}
							onDragOver={handleDragOver}
							onDrop={(e) => handleDropOnSet(e, prefix)}
						>
							<div className="set-card-icon">
								<IconFolder />
							</div>
							<div className="set-card-info">
								<span className="set-card-name">{set.name}</span>
								<span className="set-card-count">{set.total} {__('icons', 'omni-icon')}</span>
							</div>
							{prefix !== 'local' && (
								<button
									className="set-card-rename"
									onClick={(e) => {
										e.stopPropagation();
										setRenameSet({ oldName: prefix, newName: prefix });
									}}
									title={__('Rename set', 'omni-icon')}
								>
									<IconEdit />
								</button>
							)}
						</button>
					))}

					{/* Create New Set Card */}
					<button
						className="set-card set-card-create"
						onClick={() => setCreateSetModal(true)}
					>
						<div className="set-card-icon">
							<IconFolderPlus />
						</div>
						<div className="set-card-info">
							<span className="set-card-name">{__('Create New Set', 'omni-icon')}</span>
						</div>
					</button>
				</div>
			</div>

			{/* Search */}
			{icons.length > 0 && (
				<div className="omni-icon-search">
					<div className="omni-search-wrapper">
						<IconSearch className="omni-search-icon" />
						<input
							type="text"
							className="omni-search-input"
							value={searchQuery}
							onChange={(e) => setSearchQuery(e.target.value)}
							placeholder={__('Search icons...', 'omni-icon')}
						/>
						{searchQuery && (
							<button
								className="omni-search-clear"
								onClick={() => setSearchQuery('')}
								aria-label={__('Clear search', 'omni-icon')}
							>
								<IconX />
							</button>
						)}
					</div>
				</div>
			)}

			{/* Icons Grid */}
			<div className="omni-icon-content-wrapper">
				{isLoading ? (
					<div className="omni-icon-loading">
						<Spinner />
						<p>{__('Loading icons...', 'omni-icon')}</p>
					</div>
				) : iconItems.length > 0 ? (
					<div className="omni-icon-grid" ref={gridRef}>
						{iconItems.map((icon, index) => (
							<LocalIconItem
								key={icon.name}
								icon={icon}
								isSelected={icon.name === selectedIcon}
								onSelect={handleSelectIcon}
								onDragStart={handleDragStart}
								onDragEnd={handleDragEnd}
								index={index}
							/>
						))}
					</div>
				) : (
					<div className="omni-icon-empty">
						<IconSearch style={{ width: '64px', height: '64px', opacity: 0.3 }} />
						<h3>
							{searchQuery
								? __('No icons found', 'omni-icon')
								: __('No icons uploaded', 'omni-icon')
							}
						</h3>
						<p>
							{searchQuery
								? __('Try a different search term', 'omni-icon')
								: __('Upload your first icon using the "Upload Icons" tab', 'omni-icon')
							}
						</p>
					</div>
				)}
			</div>

			{/* Action Footer - shown when icon is selected */}
			{selectedIcon && selectedIconData && (
				<div className="omni-icon-action-footer">
					<div className="selected-icon-preview">
						<omni-icon
							name={selectedIcon}
							width="32"
							height="32"
						/>
						<div className="selected-icon-info">
							<span className="selected-icon-label">{__('Selected:', 'omni-icon')}</span>
							<span className="selected-icon-name">{selectedIcon}</span>
						</div>
					</div>
					<div className="action-buttons">
						<Button
							variant="secondary"
							onClick={() => setSelectedIcon(null)}
						>
							{__('Cancel', 'omni-icon')}
						</Button>
						<Button
							variant="primary"
							isDestructive
							onClick={() => setDeleteModal({ iconName: selectedIcon, iconData: selectedIconData })}
							icon={<IconTrash />}
						>
							{__('Delete Icon', 'omni-icon')}
						</Button>
					</div>
				</div>
			)}

			{/* Delete Confirmation Modal */}
			{deleteModal && (
				<Modal
					title={__('Delete Icon', 'omni-icon')}
					onRequestClose={() => setDeleteModal(null)}
					className="omni-icon-delete-modal"
				>
					<div className="delete-modal-content">
						<p>{__('Are you sure you want to delete this icon?', 'omni-icon')}</p>
						<div className="delete-preview">
							<omni-icon
								name={deleteModal.iconName}
								width="64"
								height="64"
							/>
							<div>
								<strong>{deleteModal.iconData.name}</strong>
								<br />
								<code>{deleteModal.iconName}</code>
							</div>
						</div>
						<p className="warning">
							{__('This action cannot be undone.', 'omni-icon')}
						</p>
					</div>
					<div className="delete-modal-actions">
						<Button
							variant="secondary"
							onClick={() => setDeleteModal(null)}
							disabled={isDeleting}
						>
							{__('Cancel', 'omni-icon')}
						</Button>
						<Button
							variant="primary"
							isDestructive
							onClick={() => handleDeleteIcon(deleteModal.iconName)}
							isBusy={isDeleting}
							disabled={isDeleting}
						>
							{isDeleting ? __('Deleting...', 'omni-icon') : __('Delete', 'omni-icon')}
						</Button>
					</div>
				</Modal>
			)}

			{/* Rename Set Modal */}
			{renameSet && (
				<Modal
					title={__('Rename Icon Set', 'omni-icon')}
					onRequestClose={() => setRenameSet(null)}
					className="omni-icon-rename-modal"
				>
					<div className="rename-modal-content">
						<TextControl
							label={__('Set Name', 'omni-icon')}
							value={renameSet.newName}
							onChange={(value) => setRenameSet({ ...renameSet, newName: value })}
							placeholder={__('Enter new set name', 'omni-icon')}
							help={__('Use lowercase letters, numbers, and hyphens only.', 'omni-icon')}
						/>
					</div>
					<div className="rename-modal-actions">
						<Button
							variant="secondary"
							onClick={() => setRenameSet(null)}
							disabled={isRenamingSet}
						>
							{__('Cancel', 'omni-icon')}
						</Button>
						<Button
							variant="primary"
							onClick={handleRenameSet}
							isBusy={isRenamingSet}
							disabled={isRenamingSet || !renameSet.newName.trim() || renameSet.newName === renameSet.oldName}
							icon={<IconCheck />}
						>
							{isRenamingSet ? __('Renaming...', 'omni-icon') : __('Rename', 'omni-icon')}
						</Button>
					</div>
				</Modal>
			)}

			{/* Create Set Modal */}
			{createSetModal && (
				<Modal
					title={__('Create New Icon Set', 'omni-icon')}
					onRequestClose={() => {
						setCreateSetModal(false);
						setNewSetName('');
					}}
					className="omni-icon-create-set-modal"
				>
					<div className="create-set-modal-content">
						<TextControl
							label={__('Set Name', 'omni-icon')}
							value={newSetName}
							onChange={setNewSetName}
							placeholder={__('e.g., brand, social, custom', 'omni-icon')}
							help={__('Use lowercase letters, numbers, and hyphens only. This will be used as the icon prefix.', 'omni-icon')}
							autoFocus
						/>
					</div>
					<div className="create-set-modal-actions">
						<Button
							variant="secondary"
							onClick={() => {
								setCreateSetModal(false);
								setNewSetName('');
							}}
							disabled={isCreatingSet}
						>
							{__('Cancel', 'omni-icon')}
						</Button>
						<Button
							variant="primary"
							onClick={handleCreateSet}
							isBusy={isCreatingSet}
							disabled={isCreatingSet || !newSetName.trim()}
							icon={<IconFolderPlus />}
						>
							{isCreatingSet ? __('Creating...', 'omni-icon') : __('Create Set', 'omni-icon')}
						</Button>
					</div>
				</Modal>
			)}

			{/* Upload Icon Modal */}
			{uploadModal && (
				<Modal
					title={__('Upload Icon', 'omni-icon')}
					onRequestClose={() => {
						setUploadModal(false);
						setSelectedFile(null);
						setUploadStatus(null);
						setUploadResult(null);
					}}
					className="omni-icon-upload-modal"
				>
					<div className="upload-modal-content">
						<div className="upload-form-field">
							<FormFileUpload
								accept=".svg,image/svg+xml"
								onChange={handleFileSelect}
								ref={fileInputRef}
								render={({ openFileDialog }) => (
									<Button
										variant="secondary"
										onClick={openFileDialog}
										icon={<IconUpload />}
										className="upload-file-button"
									>
										{selectedFile ? selectedFile.name : __('Choose SVG File', 'omni-icon')}
									</Button>
								)}
							/>
							{selectedFile && (
								<div className="file-info">
									<IconCheck className="icon-success" />
									<span>{selectedFile.name}</span>
									<span className="file-size">
										({(selectedFile.size / 1024).toFixed(2)} KB)
									</span>
								</div>
							)}
						</div>

						<div className="target-set-info">
							<label className="target-set-label">
								<IconFolder />
								<span>{__('Upload to:', 'omni-icon')}</span>
							</label>
							<div className="target-set-display">
								<strong>{targetSetDisplayName}</strong>
							</div>
							<p className="target-set-help">
								{__('Select a different icon set from the grid above to change the upload destination.', 'omni-icon')}
							</p>
						</div>

						{uploadStatus && (
							<Notice
								status={uploadStatus.type}
								onRemove={() => setUploadStatus(null)}
								isDismissible
							>
								{uploadStatus.message}
							</Notice>
						)}

						{uploadResult && uploadResult.icon_name && (
							<div className="upload-result">
								<div className="result-preview">
									<omni-icon
										name={uploadResult.icon_name}
										width="48"
										height="48"
									/>
								</div>
								<div className="result-info">
									<div className="info-row">
										<strong>{__('Icon Name:', 'omni-icon')}</strong>
										<code>{uploadResult.icon_name}</code>
									</div>
								</div>
							</div>
						)}

						<div className="upload-guidelines">
							<h4>{__('Guidelines', 'omni-icon')}</h4>
							<ul>
								<li><IconAlertCircle /> {__('Only SVG files are accepted', 'omni-icon')}</li>
								<li><IconAlertCircle /> {__('Maximum file size: 1MB', 'omni-icon')}</li>
								<li><IconAlertCircle /> {__('Files will be automatically sanitized', 'omni-icon')}</li>
							</ul>
						</div>
					</div>
					<div className="upload-modal-actions">
						<Button
							variant="secondary"
							onClick={() => {
								setUploadModal(false);
								setSelectedFile(null);
								setUploadStatus(null);
								setUploadResult(null);
							}}
							disabled={isUploading}
						>
							{__('Close', 'omni-icon')}
						</Button>
						<Button
							variant="primary"
							onClick={handleUploadIcon}
							isBusy={isUploading}
							disabled={!selectedFile || isUploading}
							icon={<IconUpload />}
						>
							{isUploading ? __('Uploading...', 'omni-icon') : __('Upload Icon', 'omni-icon')}
						</Button>
					</div>
				</Modal>
			)}
		</div>
	);
};

export default IconManager;
