== upa25 ==

Contributors: hanneslsm, Studio Leismann
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

upa25 is a custom block theme for Unique Pole Art https://poledance-darmstadt.de

== Installation ==

1. Upload the theme to `wp-content/themes/` or clone the repository there.
2. Activate the theme from the WordPress dashboard.

== Development ==

- Install dependencies with `npm install`.
- Run `npm start` for watch/live reload development.
- Run `npm run build` before committing assets.
- Lint and format as needed: `npm run lint:css`, `npm run lint:css:fix`,
  `npm run lint:js`, `npm run format`, `npm run format:reorder`.

== Changelog ==

See https://github.com/hanneslsm/upa25/releases

== Copyright ==

upa25 WordPress Theme, (C) 2025 hanneslsm, Studio Leismann
upa25 is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Build Process ==

This theme uses `@wordpress/scripts` with custom webpack configs
(`webpack.*.js`). See `package.json` for available commands.
