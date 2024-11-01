<?php
/**
 * WP-GraphViz Plugin.
 *
 * @package   WP_GraphViz
 * @author    De B.A.A.T. <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2014 - 2023 De B.A.A.T.
 */

 
function wpg_get_option($option_key = '') {
	$wp_graphviz_options = get_option('wp_graphviz_options');
	return isset( $wp_graphviz_options[$option_key] ) ? $wp_graphviz_options[$option_key] : false;
}

function wpg_update_option($option_key = '', $option_value = '') {
	$wp_graphviz_options = get_option('wp_graphviz_options');
	if ( isset( $wp_graphviz_options[$option_key] ) ) {
		$wp_graphviz_options[$option_key] = $option_value;
	}
	return update_option('wp_graphviz_options', $wp_graphviz_options);
}

function wpg_string_to_bool($in_value) {

	// Check boolean in_value
	if ($in_value === true)  { return true; }
	if ($in_value === false) { return false; }

	// Check string in_value
	$value = strtolower($in_value);
	if ($value == 'true'  || $value == 't' || $value == 'yes' || $value == 'y' || $value == '1') {
		return true;
	}
	if ($value == 'false' || $value == 'f' || $value == 'no'  || $value == 'n' || $value == '0') {
		return false;
	}

	return false;
}

