/**
 * External dependencies
 */
const path = require('path');
const fs = require('fs');
const { mergeWithRules } = require('webpack-merge');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

/**
 * WordPress dependencies
 */
const wpDefaultConfig = require('@wordpress/scripts/config/webpack.config');

/**
 * Read version from package.json
 */
const { version } = require('./package.json');

/**
 * Utility: get all *.scss files inside a directory
 */
const getScssFiles = (dir) =>
	fs.existsSync(dir)
		? fs
				.readdirSync(dir)
				.filter((f) => f.endsWith('.scss'))
				.map((f) => path.resolve(dir, f))
		: [];

/**
 * Build dynamic entry map
 */
const makeEntries = () => {
	const entries = {
		'css/global': path.resolve(__dirname, 'src/scss/global.scss'),
		'css/screen': path.resolve(__dirname, 'src/scss/screen.scss'),
		'css/editor': path.resolve(__dirname, 'src/scss/editor.scss'),
		'js/global': path.resolve(__dirname, 'src/js/global.js'),
	};

	/* single‑file block styles */
	const blockDir = path.resolve(__dirname, 'src/scss/blocks');
	if (fs.existsSync(blockDir)) {
		fs.readdirSync(blockDir).forEach((file) => {
			if (file.endsWith('.scss')) {
				entries[`css/blocks/${file.replace('.scss', '')}`] = path.resolve(blockDir, file);
			}
		});
	}

	/* bundled “styles/blocks” and “styles/sections” */
	const stylesBlocksDir = path.resolve(__dirname, 'src/scss/styles/blocks');
	const stylesSectionsDir = path.resolve(__dirname, 'src/scss/styles/sections');

	const blocksBundle = getScssFiles(stylesBlocksDir);
	const sectionsBundle = getScssFiles(stylesSectionsDir);

	if (blocksBundle.length) entries['css/styles/blocks'] = blocksBundle;
	if (sectionsBundle.length) entries['css/styles/sections'] = sectionsBundle;

	return entries;
};

/* Remove the single‑string entry that ships with @wordpress/scripts */
delete wpDefaultConfig.entry;

module.exports = mergeWithRules({
	entry: 'replace',   // overwrite, don’t merge
	plugins: 'append',  // keep default plugins and add ours
})(wpDefaultConfig, {
	mode: process.env.NODE_ENV || 'development',

	entry: makeEntries(),

	plugins: [
		new RemoveEmptyScriptsPlugin({
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		}),

		/* Update the “Version:” header in style.css after every build */
		{
			apply: (compiler) => {
				compiler.hooks.afterEmit.tap('UpdateThemeVersionPlugin', () => {
					const styleCss = path.resolve(__dirname, 'style.css');

					if (!fs.existsSync(styleCss)) {
						// eslint-disable-next-line no-console
						console.warn(`No style.css at ${styleCss}; skipping version bump.`);
						return;
					}

					try {
						let content = fs.readFileSync(styleCss, 'utf8');
						content = content.replace(/(Version:\s*)([^\r\n]+)/, `$1${version}`);
						fs.writeFileSync(styleCss, content, 'utf8');
						// eslint-disable-next-line no-console
						console.info(`style.css version updated to ${version}.`);
					} catch (err) {
						// eslint-disable-next-line no-console
						console.error('Failed to update style.css version:', err);
					}
				});
			},
		},
	],

	optimization: {
		splitChunks: {
			chunks: 'all',
			cacheGroups: {
				vendors: {
					test: /[\\/]node_modules[\\/]/,
					name: 'js/vendors',
					enforce: true,
				},
			},
		},
	},

	stats: {
		all: false,
		source: true,
		assets: true,
		errors: true,
		errorsCount: true,
		warnings: true,
		warningsCount: true,
		colors: true,
	},
});
