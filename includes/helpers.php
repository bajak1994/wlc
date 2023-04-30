<?php
/**
 * This file contains helpers functions.
 *
 * @package WLCForm
 */

namespace App\WLCFORM;

/**
 * Includes a template file and returns its output.
 *
 * @param string $template   The name of the template file to include.
 * @param array  $variables  An optional array of variables to make available to the template.
 *
 * @return string            The output of the included template file.
 */
function include_template( $template, $variables = array() ) {
	$file_path = WLCFORM_PLUGIN_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $template . '.php';

	if ( file_exists( $file_path ) ) {
		foreach ( $variables as $key => $value ) {
			${$key} = $value;
		}
		ob_start();
		include $file_path;
		return ob_get_clean();
	}

	return '';
}
