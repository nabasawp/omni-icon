import { defineConfig } from 'vite';
import { nodePolyfills } from 'vite-plugin-node-polyfills';
import react from '@vitejs/plugin-react';
import { v4wp } from '@kucrut/vite-for-wp';
import { wp_scripts } from '@kucrut/vite-for-wp/plugins';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import svgr from 'vite-plugin-svgr';
import Icons from 'unplugin-icons/vite';
import path from 'path';

export default defineConfig({
	plugins: [
		nodePolyfills({
			// Override the default polyfills for specific modules.
			overrides: {
				fs: 'memfs', // Since `fs` is not supported in browsers, we can use the `memfs` package to polyfill it.
			},
		}),
		v4wp({
			input: {
				'integration/gutenberg/blocks/icon-block': 'resources/integration/gutenberg/blocks/icon-block/index.jsx',
				'integration/gutenberg/blocks/icon-block/css': 'resources/integration/gutenberg/blocks/icon-block/editor.css',
				'integration/gutenberg/blocks/icon-block/iframe': 'resources/integration/gutenberg/blocks/icon-block/iframe.ts',
				'webcomponents/omni-icon': 'resources/webcomponents/omni-icon.ts',

				// admin
				'admin/admin-app/index': 'resources/admin/admin-app/index.jsx',

				// integration
				'integration/bricks/editor': 'resources/integration/bricks/editor.ts',
				'integration/livecanvas/editor': 'resources/integration/livecanvas/editor.ts',
				'integration/elementor/editor': 'resources/integration/elementor/editor.ts',
				'integration/breakdance/editor': 'resources/integration/breakdance/editor.ts',
			},
			// outDir: 'public/build',
		}),
		react(),
		wp_scripts(),
		Icons({ compiler: 'jsx', jsx: 'react', autoInstall: true, scale: 1 }),
		svgr({
			svgrOptions: {
				dimensions: false,
			}
		}),
		viteStaticCopy({
			targets: [
				{
					src: 'resources/integration/gutenberg/blocks/icon-block/block.json',
					dest: 'integration/gutenberg/blocks/icon-block/'
				},
				// {
				//     src: 'assets/integration/gutenberg/common-block/block.json',
				//     dest: 'blocks/common-block/'
				// }
			]
		})
	],
	resolve: {
		alias: {
			'~': path.resolve(__dirname), // root directory
			'@': path.resolve(__dirname, 'resources'),
		},
	},
});
