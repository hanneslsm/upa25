<?php

/**
 * Remove default css. Use only for development!
 *
 * @package upa25
 * @since 0.1.0
 * @link https://github.com/WordPress/gutenberg/issues/36834#issuecomment-1769802551
 * @author https://github.com/bd-viget
 */


/**
 * Remove bloated inline core color styles.
 *
 * @param \WP_Theme_JSON_Data $theme_json Class to access and update the underlying data.
 *
 * @return \WP_Theme_JSON_Data
 */
add_filter(
	'wp_theme_json_data_default',
	function ($theme_json) {
		$data = $theme_json->get_data();

		// Remove default color palette.
		$data['settings']['color']['palette']['default'] = [];

		// Remove default duotone.
		$data['settings']['color']['duotone']['default'] = [];

		// Remove default gradients.
		$data['settings']['color']['gradients']['default'] = [];

		// Update the theme data.
		$theme_json->update_with($data);

		return $theme_json;
	}
);
