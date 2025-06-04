const fs = require('fs');
const path = require('path');
const glob = require('glob');
const { parse } = require('@wordpress/block-serialization-default-parser');

const files = glob.sync(path.join(__dirname, '../patterns/**/*.php'));
let hasError = false;

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
    parse(markup);
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
