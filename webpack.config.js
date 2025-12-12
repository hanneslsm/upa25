/**
 * ProLooks webpack configuration.
 *
 * @package ProLooks
 * @version 3.7.0
 * @docs docs/webpack.md
 */

// Core dependencies
const path = require('path');
const fs = require('fs');
const { merge } = require('webpack-merge');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const sharp = require('sharp');
const { optimize } = require('svgo');
// Optional BrowserSync plugin (only available when installed)
let BrowserSyncPlugin = null;
try {
  BrowserSyncPlugin = require('browser-sync-webpack-plugin');
} catch (error) {
  BrowserSyncPlugin = null;
}

// WordPress default webpack configuration (array with scriptConfig and moduleConfig)
const defaultConfigs = require('@wordpress/scripts/config/webpack.config');
const [scriptConfig, moduleConfig] = Array.isArray(defaultConfigs)
  ? defaultConfigs
  : [defaultConfigs, null];

// Used to sync the theme header version after each build
const packageJson = require('./package.json');

// Environment-driven toggles
const CONFIG = {
  IMG_MAX_WIDTH: Number(process.env.PROLOOKS_IMG_MAX_WIDTH || 2560),
  QUALITY_JPEG: Number(process.env.PROLOOKS_QUALITY_JPEG || 50),
  QUALITY_PNG: Number(process.env.PROLOOKS_QUALITY_PNG || 50),
  QUALITY_AVIF: Number(process.env.PROLOOKS_QUALITY_AVIF || 50),
  QUALITY_WEBP: Number(process.env.PROLOOKS_QUALITY_WEBP || 70),
  QUALITY_WEBP_CONVERT: Number(process.env.PROLOOKS_QUALITY_WEBP_CONVERT || 60),
  BS_PROXY: process.env.PROLOOKS_BS_PROXY || '',
  BS_HOST: process.env.PROLOOKS_BS_HOST || 'localhost',
  BS_PORT: Number(process.env.PROLOOKS_BS_PORT || 3000),
  COPY_IMAGES_IN_PROD: (process.env.PROLOOKS_COPY_IMAGES_IN_PROD || 'true').toLowerCase() === 'true',
};

// Resolved paths reused across helpers
const PATHS = {
  root: __dirname,
  build: path.resolve(__dirname, 'build'),
  imagesSrc: path.resolve(__dirname, 'src/images'),
  svgSrc: path.resolve(__dirname, 'src/svg'),
  blocksJs: path.resolve(__dirname, 'src/blocks'),
  cssGlobal: path.resolve(__dirname, 'src/global.scss'),
  cssScreen: path.resolve(__dirname, 'src/scss/screen.scss'),
  cssEditor: path.resolve(__dirname, 'src/scss/editor.scss'),
  jsGlobal: path.resolve(__dirname, 'src/global.js'),
  themeStyle: path.resolve(__dirname, 'style.css'),
};

// Helper utilities for repeated filesystem checks
const isDir = (p) => fs.existsSync(p) && fs.statSync(p).isDirectory();

/**
 * Check if a block directory contains a block.json file (custom block).
 *
 * @param {string} blockDir - The block directory path.
 * @return {boolean} True if the directory contains a block.json file.
 */
const isCustomBlock = (blockDir) => fs.existsSync(path.join(blockDir, 'block.json'));

// Replace the default MiniCssExtractPlugin to remove the "style-" filename prefix.
function withPlainCssFilenames(config) {
  if (!config || !config.plugins) return config;

  config.plugins = config.plugins.map((plugin) => {
    const isMiniCss = plugin && plugin.constructor && plugin.constructor.name === 'MiniCssExtractPlugin';
    if (!isMiniCss) return plugin;

    return new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[name].css',
    });
  });

  return config;
}

// Rename assets like styles/{block}/style-*.css to drop the redundant "style-" prefix.
class StripStylePrefixPlugin {
  apply(compiler) {
    compiler.hooks.thisCompilation.tap('StripStylePrefixPlugin', (compilation) => {
      compilation.hooks.processAssets.tap(
        {
          name: 'StripStylePrefixPlugin',
          stage: compiler.webpack.Compilation.PROCESS_ASSETS_STAGE_SUMMARIZE,
        },
        () => {
          const { Compilation } = compiler.webpack;
          const regex = /^styles\/([^/]+)\/style-(.+)$/;

          Object.keys(compilation.assets).forEach((filename) => {
            const match = regex.exec(filename);
            if (!match) return;

            const newName = filename.replace(regex, (all, block, rest) => `styles/${block}/${rest}`);

            // Avoid clobbering an existing asset name.
            if (compilation.assets[newName]) return;

            compilation.renameAsset(filename, newName);
            // Mark the new asset as processed to keep asset info intact.
            compilation.updateAsset(newName, (source, info) => source, (info) => info);
          });
        }
      );
    });
  }
}

// Remove empty files from the build output after compilation.
class RemoveEmptyFilesPlugin {
  apply(compiler) {
    compiler.hooks.afterEmit.tap('RemoveEmptyFilesPlugin', () => {
      const buildDir = PATHS.build;
      const walk = (dir) => {
        if (!fs.existsSync(dir)) return;
        fs.readdirSync(dir, { withFileTypes: true }).forEach((dirent) => {
          const fullPath = path.join(dir, dirent.name);
          if (dirent.isDirectory()) {
            walk(fullPath);
          } else if (dirent.isFile() && fs.statSync(fullPath).size === 0) {
            fs.unlinkSync(fullPath);
          }
        });
        // Remove directory if empty after file removal.
        try {
          const entries = fs.readdirSync(dir);
          if (entries.length === 0) {
            fs.rmdirSync(dir);
          }
        } catch {
          // Directory not empty or doesn't exist; skip.
        }
      };
      walk(buildDir);
    });
  }
}

// Utility to collect files matching extensions from a directory
function collectFiles(rootDir, extensions = []) {
  const entries = {};
  if (!isDir(rootDir)) return entries;

  const extSet = new Set(extensions);
  const walk = (dir, relPath = '') => {
    if (!fs.existsSync(dir)) return;
    fs.readdirSync(dir, { withFileTypes: true }).forEach((d) => {
      const fullPath = path.join(dir, d.name);
      const newRelPath = relPath ? `${relPath}/${d.name}` : d.name;

      if (d.isDirectory()) {
        walk(fullPath, newRelPath);
      } else {
        const ext = path.extname(d.name);
        if (extSet.has(ext)) {
          const key = newRelPath.replace(ext, '');
          entries[key] = fullPath;
        }
      }
    });
  };
  walk(rootDir);
  return entries;
}

// Add entries for block JS files (index.js, view.js) skipping custom blocks
function blockJsEntries(rootDir, outBase = 'blocks') {
  const entries = {};
  if (!isDir(rootDir)) return entries;

  fs.readdirSync(rootDir).forEach((name) => {
    const blockDir = path.join(rootDir, name);
    if (!isDir(blockDir) || isCustomBlock(blockDir)) return;

    ['index.js', 'view.js'].forEach((filename) => {
      const fp = path.join(blockDir, filename);
      if (fs.existsSync(fp)) {
        const base = filename.replace('.js', '');
        entries[`${outBase}/${name}/${base}`] = fp;
      }
    });
  });
  return entries;
}

// Add nested JS/SCSS files from blocks directory (excluding style.scss and styles/ subdirs)
function blocksRootJsEntries(rootDir, outBase = 'blocks') {
  const entries = {};
  if (!isDir(rootDir)) return entries;

  const walk = (dir, relPath = '') => {
    const block = relPath ? relPath.split('/')[0] : '';
    if (block && isCustomBlock(path.join(rootDir, block))) return;

    fs.readdirSync(dir, { withFileTypes: true }).forEach((d) => {
      const fullPath = path.join(dir, d.name);
      const newRelPath = relPath ? `${relPath}/${d.name}` : d.name;

      if (d.isDirectory() && d.name !== 'styles' && !isCustomBlock(fullPath)) {
        walk(fullPath, newRelPath);
      } else if (d.isFile()) {
        if (d.name.endsWith('.js')) {
          entries[`${outBase}/${newRelPath.replace(/\.js$/, '')}`] = fullPath;
        } else if (d.name.endsWith('.scss') && d.name !== 'style.scss') {
          entries[`${outBase}/${newRelPath.replace(/\.scss$/, '')}-styles`] = fullPath;
        }
      }
    });
  };
  walk(rootDir);
  return entries;
}

// Add style.scss entries for blocks (skipping custom blocks)
function blockStyleIndexEntries(rootDir, outBase = 'styles') {
  const entries = {};
  if (!isDir(rootDir)) return entries;

  fs.readdirSync(rootDir).forEach((name) => {
    const blockDir = path.join(rootDir, name);
    if (!isDir(blockDir) || isCustomBlock(blockDir)) return;

    const fp = path.join(blockDir, 'style.scss');
    if (fs.existsSync(fp)) entries[`${outBase}/${name}/base`] = fp;
  });
  return entries;
}

// Add block style variant entries from styles/ subdirectories (skipping custom blocks)
function blockStyleVariantsEntries(rootDir, outBase = 'styles') {
  const entries = {};
  if (!isDir(rootDir)) return entries;

  fs.readdirSync(rootDir).forEach((name) => {
    const blockDir = path.join(rootDir, name);
    if (!isDir(blockDir) || isCustomBlock(blockDir)) return;

    const stylesDir = path.join(blockDir, 'styles');
    if (!isDir(stylesDir)) return;

    fs.readdirSync(stylesDir).forEach((f) => {
      if (f.endsWith('.scss')) {
        const styleName = f.replace(/\.scss$/, '');
        entries[`${outBase}/${name}/${styleName}`] = path.join(stylesDir, f);
      }
    });
  });
  return entries;
}

// Image transformation helpers
const imageTransforms = {
  raster: (content, absolutePath) => {
    const ext = path.extname(absolutePath).toLowerCase();
    const img = sharp(content).resize({ width: CONFIG.IMG_MAX_WIDTH, withoutEnlargement: true });
    const qualityMap = {
      '.jpg': CONFIG.QUALITY_JPEG,
      '.jpeg': CONFIG.QUALITY_JPEG,
      '.png': CONFIG.QUALITY_PNG,
      '.avif': CONFIG.QUALITY_AVIF,
      '.webp': CONFIG.QUALITY_WEBP,
    };
    if (ext === '.webp') return img.webp({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.avif') return img.avif({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.png') return img.png({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.jpg' || ext === '.jpeg') return img.jpeg({ quality: qualityMap[ext] }).toBuffer();
    return content;
  },
  webp: (content) => sharp(content)
    .resize({ width: CONFIG.IMG_MAX_WIDTH, withoutEnlargement: true })
    .webp({ quality: CONFIG.QUALITY_WEBP_CONVERT })
    .toBuffer(),
  svg: (content) => {
    const result = optimize(content.toString(), {
      multipass: true,
      plugins: [
        'removeDimensions',
        { name: 'removeViewBox', active: true },
        'removeTitle',
        'removeDesc',
        'removeUselessDefs',
        'removeXMLNS',
      ],
    });
    return Buffer.from(result.data);
  },
};

// Base plugin stack shared by dev + prod builds
function commonPlugins() {
  return [
    ...(scriptConfig.plugins || []),
    new RemoveEmptyScriptsPlugin({
      stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
    }),
    new StripStylePrefixPlugin(),
    new RemoveEmptyFilesPlugin(),
    new CopyWebpackPlugin({
      patterns: [
        // Parts and sections are now manually imported via global.scss and global.js
        // so we no longer copy their PHP files separately
        {
          from: '**/*.php',
          context: 'src/blocks',
          to: 'blocks/[path][name][ext]',
          noErrorOnMissing: true,
          globOptions: { ignore: ['**/block.json', '**/render.php'] },
        },
      ],
    }),
    {
      apply: (compiler) => {
        compiler.hooks.afterEmit.tap('UpdateThemeVersionPlugin', () => {
          if (!fs.existsSync(PATHS.themeStyle)) return;
          let content = fs.readFileSync(PATHS.themeStyle, 'utf-8');
          content = content.replace(/(Version:\s*)([^\r\n]+)/, `$1${packageJson.version}`);
          fs.writeFileSync(PATHS.themeStyle, content, 'utf-8');
        });
      },
    },
  ];
}

// Enable BrowserSync when a proxy is provided
function devPlugins({ proxy, host, port }) {
  if (!proxy || !BrowserSyncPlugin) return [];
  return [
    new BrowserSyncPlugin(
      { host, port, proxy, files: ['theme.json','**/*.php', 'build/**/*.css', 'build/**/*.js'], open: false, injectChanges: true },
      { reload: false }
    ),
  ];
}

// Copy and optimize static assets in production builds
function prodPlugins() {
  if (!CONFIG.COPY_IMAGES_IN_PROD) return [];
  return [
    new CopyWebpackPlugin({
      patterns: [
        { from: '**/*.{jpg,jpeg,png,avif,webp}', context: PATHS.imagesSrc, to: 'images/[path][name][ext]', noErrorOnMissing: true, transform: imageTransforms.raster },
        { from: '**/*.{jpg,jpeg,png,avif,webp}', context: PATHS.imagesSrc, to: 'webp/[path][name].webp', noErrorOnMissing: true, transform: imageTransforms.webp },
        { from: '**/*.svg', context: PATHS.svgSrc, to: 'svg/[path][name][ext]', noErrorOnMissing: true, transform: imageTransforms.svg },
      ],
    }),
  ];
}

// Check if any custom blocks exist (directories with block.json)
function hasCustomBlocks() {
  if (!isDir(PATHS.blocksJs)) return false;
  return fs.readdirSync(PATHS.blocksJs).some((name) => {
    const blockDir = path.join(PATHS.blocksJs, name);
    return isCustomBlock(blockDir);
  });
}

// Additional entries for global assets, core block customizations.
// Custom blocks are handled by wp-scripts default entry() function.
// Parts and sections are now manually imported via global.scss and global.js
function makeAdditionalEntries() {
  return {
    'theme/global-styles': PATHS.cssGlobal,
    'theme/screen': PATHS.cssScreen,
    'theme/editor': PATHS.cssEditor,
    'theme/global': PATHS.jsGlobal,
    ...blockJsEntries(PATHS.blocksJs),
    ...blocksRootJsEntries(PATHS.blocksJs),
    ...blockStyleIndexEntries(PATHS.blocksJs),
    ...blockStyleVariantsEntries(PATHS.blocksJs),
  };
}

// Export the merged config consumed by npm scripts
module.exports = () => {
  const isProd = process.env.NODE_ENV === 'production';

  // Script config for traditional scripts and core block customizations
  const customScriptConfig = merge(scriptConfig, {
    mode: isProd ? 'production' : 'development',
    entry: {
      // Include default block entries from wp-scripts (handles custom blocks with block.json)
      // Only call if custom blocks exist to avoid "No entry file discovered" error
      ...(hasCustomBlocks() ? scriptConfig.entry() : {}),
      // Add our additional entries
      ...makeAdditionalEntries(),
    },
    output: {
      path: PATHS.build,
      filename: '[name].js',
      assetModuleFilename: 'images/[path][name][ext]',
      clean: false,
    },
    module: {
      rules: [
        {
          test: /\.svg$/i,
          type: 'asset/resource',
          generator: { filename: 'images/[path][name][ext]' },
        },
      ],
    },
    cache: {
      type: 'filesystem',
      cacheDirectory: path.resolve(PATHS.root, '.webpack-cache'),
      buildDependencies: { config: [__filename] },
    },
    plugins: [
      ...commonPlugins(),
      ...(isProd ? prodPlugins() : devPlugins({
        proxy: CONFIG.BS_PROXY,
        host: CONFIG.BS_HOST,
        port: CONFIG.BS_PORT,
      })),
    ],
    performance: { hints: false },
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
    infrastructureLogging: { level: 'warn' },
  });

  // Remove the default "style-" prefix from CSS output filenames.
  withPlainCssFilenames(customScriptConfig);

  // Return array of configs if moduleConfig exists (for Interactivity API support)
  if (moduleConfig) {
    const moduleWithCss = withPlainCssFilenames(moduleConfig);
    return [customScriptConfig, moduleWithCss];
  }

  return customScriptConfig;
};
