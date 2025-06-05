const fs = require('fs');
const path = require('path');
const glob = require('glob');
const { parse } = require('@wordpress/block-serialization-default-parser');
const { getBlockType, unstable__bootstrapServerSideBlockDefinitions } = require('@wordpress/blocks');
const blockJsonFiles = glob.sync(path.join(__dirname, '../node_modules/@wordpress/block-library/src/**/block.json'));
const definitions = {};
for (const json of blockJsonFiles) {
  const meta = JSON.parse(fs.readFileSync(json, 'utf8'));
  if (meta.name && meta.attributes) {
    definitions[meta.name] = { attributes: meta.attributes };
  }
}
unstable__bootstrapServerSideBlockDefinitions(definitions);

const files = glob.sync(path.join(__dirname, '../patterns/**/*.php'));
let hasError = false;

function isValidType(value, schema) {
  if (schema.enum) {
    return schema.enum.includes(value);
  }
  switch (schema.type) {
    case 'boolean':
      return typeof value === 'boolean';
    case 'string':
    case 'rich-text':
      return typeof value === 'string';
    case 'number':
    case 'integer':
      return typeof value === 'number';
    case 'array':
      return Array.isArray(value);
    case 'object':
      return value && typeof value === 'object' && !Array.isArray(value);
    default:
      return true;
  }
}

function validateBlock(block) {
  const blockType = getBlockType(block.blockName);
  if (blockType && blockType.attributes) {
    const attrs = block.attrs || {};
    for (const [key, value] of Object.entries(attrs)) {
      const schema = blockType.attributes[key];
      if (!schema) {
        throw new Error(`Unknown attribute "${key}" in ${block.blockName}`);
      }
      if (!isValidType(value, schema)) {
        throw new Error(`Invalid value for attribute "${key}" in ${block.blockName}`);
      }
    }
  }
  if (block.innerBlocks) {
    block.innerBlocks.forEach((inner) => validateBlock(inner));
  }
}

function sanitize(content) {
  if (content.startsWith('<?php')) {
    const idx = content.indexOf('?>');
    if (idx !== -1) {
      content = content.slice(idx + 2);
    }
  }
  return content.replace(/<\?php[\s\S]*?\?>/g, '');
}

for (const file of files) {
  const data = fs.readFileSync(file, 'utf8');
  const markup = sanitize(data);
  try {
    const blocks = parse(markup);
    blocks.forEach((b) => {
      try {
        validateBlock(b);
      } catch (err) {
        hasError = true;
        console.error(`Invalid markup in ${file}: ${err.message}`);
      }
    });
  } catch (e) {
    hasError = true;
    console.error(`Invalid markup in ${file}: ${e.message}`);
  }
}

if (hasError) {
  console.error('Pattern validation failed.');
  process.exit(1);
} else {
  console.log('All pattern markup is valid.');
}
