/**
 * upa25 Webpack configuration
 *
 * @package upa25
 * @version 2.0.0
 *
 * 2.0.0: Add support for webp images
 * 1.0.0: Initial version
 */

/**
 * External dependencies
 */
const path = require("path");
const fs = require("fs");
const { merge } = require("webpack-merge");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const sharp = require("sharp");

/**
 * WordPress dependencies
 */
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

/**
 * Read version from package.json
 */
const packageJson = require("./package.json");

/**
 * Utility: get SCSS files in a directory
 */
function getScssFiles(dir) {
	return fs.existsSync(dir)
		? fs
				.readdirSync(dir)
				.filter((f) => f.endsWith(".scss"))
				.map((f) => path.resolve(dir, f))
		: [];
}

module.exports = (env) => {
	const isProd = process.env.NODE_ENV === "production";
	const mode = isProd ? "production" : "development";

	// SCSS entries
	const blockDir = path.resolve(__dirname, "src/scss/blocks");
	const blockStyles = fs.existsSync(blockDir)
		? fs
				.readdirSync(blockDir)
				.filter((f) => f.endsWith(".scss"))
				.reduce((o, f) => {
					o[`css/blocks/${f.replace(/\.scss$/, "")}`] = path.resolve(
						blockDir,
						f,
					);
					return o;
				}, {})
		: {};

	const styleBlocks = getScssFiles(
		path.resolve(__dirname, "src/scss/styles/blocks"),
	);
	const styleSections = getScssFiles(
		path.resolve(__dirname, "src/scss/styles/sections"),
	);

	const entries = {
		"css/global": path.resolve(__dirname, "src/scss/global.scss"),
		"css/screen": path.resolve(__dirname, "src/scss/screen.scss"),
		"css/editor": path.resolve(__dirname, "src/scss/editor.scss"),
		"js/global": path.resolve(__dirname, "src/js/global.js"),
		...blockStyles,
	};
	if (styleBlocks.length) entries["css/styles/blocks"] = styleBlocks;
	if (styleSections.length) entries["css/styles/sections"] = styleSections;

	const plugins = [
		...(defaultConfig.plugins || []),
		new RemoveEmptyScriptsPlugin({
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		}),
	];

	if (isProd) {
		plugins.push(
			new CopyWebpackPlugin({
				patterns: [
					{
						from: "**/*.{jpg,jpeg,png,avif,webp}",
						context: path.resolve(__dirname, "src/images"),
						to: "images/[path][name][ext]",
						noErrorOnMissing: true,
						transform: async (content, absoluteFrom) => {
							const ext = path.extname(absoluteFrom).toLowerCase();
							const img = sharp(content).resize({
								width: 2560,
								withoutEnlargement: true,
							});
							switch (ext) {
								case ".jpg":
								case ".jpeg":
									return img.jpeg({ quality: 50 }).toBuffer();
								case ".png":
									return img.png({ quality: 50 }).toBuffer();
								case ".avif":
									return img.avif({ quality: 50 }).toBuffer();
								case ".webp":
									return img.webp({ quality: 50 }).toBuffer();
								default:
									return content;
							}
						},
					},
					{
						from: "**/*.{jpg,jpeg,png,avif,webp}",
						context: path.resolve(__dirname, "src/images"),
						to: "webp/[path][name].webp",
						noErrorOnMissing: true,
						transform: async (content) => {
							return sharp(content)
								.resize({ width: 2560, withoutEnlargement: true })
								.webp({ quality: 60 })
								.toBuffer();
						},
					},
				],
			}),
		);
	}

	// bump theme version
	plugins.push({
		apply: (compiler) => {
			compiler.hooks.afterEmit.tap("UpdateThemeVersionPlugin", () => {
				const stylePath = path.resolve(__dirname, "style.css");
				if (!fs.existsSync(stylePath)) return;
				let content = fs.readFileSync(stylePath, "utf-8");
				content = content.replace(
					/(Version:\s*)([^\r\n]+)/,
					`$1${packageJson.version}`,
				);
				fs.writeFileSync(stylePath, content, "utf-8");
			});
		},
	});

	return merge(defaultConfig, {
		mode,
		entry: entries,
		output: {
			path: path.resolve(__dirname, "build"),
			filename: "[name].js",
			assetModuleFilename: "images/[path][name][ext]",
		},
		plugins,
		stats: {

			all: false,
			source: true,
			assets: true,
			errorsCount: true,
			errors: true,
			warningsCount: true,
			warnings: true,
			colors: true,
		},
	});
};
