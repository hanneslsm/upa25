/**
 * withkit Webpack configuration
 *
 * @package withkit
 * @version 2.2.0
 *
 * 2.2.0: Auto-detect block JS and style entries; general cleanup for easier block registration
 * 2.1.2: Add support for cleaning and copying SVGs to build folder
 * 2.1.1: Disable performance hints
 * 2.1.0: Add support for automatic block-style entries and recursive block SCSS
 * 2.0.0: Add support for webp images
 * 1.0.0: Initial version
 */

/** External dependencies */
const path = require("path");
const fs = require("fs");
const { merge } = require("webpack-merge");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const sharp = require("sharp");
const { optimize } = require("svgo");

/** WordPress dependencies */
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

/** Read version from package.json */
const packageJson = require("./package.json");

/** Utility: find all SCSS files in a directory */
function getScssFiles(dir) {
	return fs.existsSync(dir)
		? fs
				.readdirSync(dir)
				.filter((f) => f.endsWith(".scss"))
				.map((f) => path.resolve(dir, f))
		: [];
}

/** Utility: recursively get block entries */
function getRecursiveBlockEntries(rootDir, outputDir) {
	if (!fs.existsSync(rootDir)) return {};
	return fs
		.readdirSync(rootDir, { withFileTypes: true })
		.reduce((entries, dirent) => {
			const fullPath = path.join(rootDir, dirent.name);
			if (dirent.isDirectory()) {
				Object.assign(
					entries,
					getRecursiveBlockEntries(fullPath, `${outputDir}/${dirent.name}`),
				);
			} else if (dirent.isFile() && dirent.name.endsWith(".scss")) {
				const name = dirent.name.replace(/\.scss$/, "");
				entries[`${outputDir}/${name}`] = fullPath;
			}
			return entries;
		}, {});
}

/** Utility: get styled block variation entries */
function getStyleBlockEntries(rootDir, outputDir) {
	if (!fs.existsSync(rootDir)) return {};
	return fs
		.readdirSync(rootDir)
		.filter((d) => fs.statSync(path.join(rootDir, d)).isDirectory())
		.reduce((entries, styleName) => {
			const dir = path.join(rootDir, styleName);
			fs.readdirSync(dir)
				.filter((f) => f.endsWith(".scss"))
				.forEach((f) => {
					const name = f.replace(/\.scss$/, "");
					entries[`${outputDir}/${styleName}/${name}`] = path.resolve(dir, f);
				});
			return entries;
		}, {});
}

module.exports = (env) => {
	const isProd = process.env.NODE_ENV === "production";
	const mode = isProd ? "production" : "development";

	const globalEntry = {
		"css/global": path.resolve(__dirname, "src/scss/global.scss"),
	};
	const screenEntry = {
		"css/screen": path.resolve(__dirname, "src/scss/screen.scss"),
	};
	const editorEntry = {
		"css/editor": path.resolve(__dirname, "src/scss/editor.scss"),
	};
	const jsBaseEntry = {
		"js/global": path.resolve(__dirname, "src/js/global.js"),
	};

	// Automatically gather block JS (index.js & view.js) and style.scss (style-index) files
	const blocksRootDir = path.resolve(__dirname, "src/blocks");

	function getBlockJsEntries(rootDir, outputDir = "js/blocks") {
		if (!fs.existsSync(rootDir)) return {};
		return fs
			.readdirSync(rootDir)
			.filter((d) => fs.statSync(path.join(rootDir, d)).isDirectory())
			.reduce((entries, blockName) => {
				const dir = path.join(rootDir, blockName);
				["index", "view"].forEach((file) => {
					const filePath = path.join(dir, `${file}.js`);
					if (fs.existsSync(filePath)) {
						entries[`${outputDir}/${blockName}/${file}`] = filePath;
					}
				});
				return entries;
			}, {});
	}

	function getBlockStyleIndexEntries(rootDir, outputDir = "css/blocks") {
		if (!fs.existsSync(rootDir)) return {};
		return fs
			.readdirSync(rootDir)
			.filter((d) => fs.statSync(path.join(rootDir, d)).isDirectory())
			.reduce((entries, blockName) => {
				const stylePath = path.join(rootDir, blockName, "style.scss");
				if (fs.existsSync(stylePath)) {
					entries[`${outputDir}/${blockName}/style-index`] = stylePath;
				}
				return entries;
			}, {});
	}

	const blockJsEntries = getBlockJsEntries(blocksRootDir);
	const blockStyleIndexEntries = getBlockStyleIndexEntries(blocksRootDir);

	const blockDir = path.resolve(__dirname, "src/scss/blocks");
	const blockEntries = getRecursiveBlockEntries(blockDir, "css/blocks");

	const styleBlocksDir = path.resolve(__dirname, "src/scss/block-styles");
	const styleEntries = getStyleBlockEntries(styleBlocksDir, "css/block-styles");

	const sectionFiles = getScssFiles(
		path.resolve(__dirname, "src/scss/styles/sections"),
	);
	const sectionEntry = sectionFiles.length
		? { "css/styles/sections": sectionFiles }
		: {};

	const entries = Object.assign(
		{},
		globalEntry,
		screenEntry,
		editorEntry,
		jsBaseEntry,
		blockJsEntries,
		blockStyleIndexEntries,
		blockEntries,
		styleEntries,
		sectionEntry,
	);

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
									return img.webp({ quality: 70 }).toBuffer();
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
					{
						from: "**/*.svg",
						context: path.resolve(__dirname, "src/svg"),
						to: "svg/[path][name][ext]",
						noErrorOnMissing: true,
						transform: async (content) => {
							const result = optimize(content.toString(), {
								multipass: true,
								plugins: [
									"removeDimensions",
									{
										name: "removeViewBox",
										active: true,
									},
									"removeTitle",
									"removeDesc",
									"removeUselessDefs",
									"removeXMLNS",
								],
							});
							return Buffer.from(result.data);
						},
					},
				],
			}),
		);
	}

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
		module: {
			rules: [
				{
					test: /\.svg$/i,
					type: "asset/resource",
					generator: {
						filename: "images/[path][name][ext]",
					},
				},
			],
		},
		plugins,
		performance: {
			hints: false,
		},
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
