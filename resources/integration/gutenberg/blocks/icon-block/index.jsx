import './editor.css';

import { registerBlockType } from '@wordpress/blocks';
import OmniIconSvg from '~/omni-icon.svg?react';
import Edit from './components/Edit';
import Save from './components/Save';
import metadata from './block.json';

// Omni Icon component
const icon = () => (
	<OmniIconSvg width={24} height={24} aria-hidden="true" focusable="false" />
);

// Remove editorScript from metadata before registering
// (it's needed in block.json for WordPress Block Directory validation,
// but not needed here since we're registering via JS)
const { editorScript, ...blockMetadata } = metadata;

registerBlockType(blockMetadata.name, {
	...blockMetadata,
	icon,
	edit: Edit,
	save: Save,
});