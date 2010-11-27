<?php
/*
Plugin Name: MailChimp Widget
Plugin URI: https://github.com/kalchas
Description: 
Author: James Lafferty
Version: 0.1
Author URI: https://github.com/kalchas
*/

/**
 * Set up the autoloader.
*/

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/lib/'));

spl_autoload_extensions('.class.php');

if (! function_exists('buffered_autoloader')) {
	
	function buffered_autoloader ($c) {

		spl_autoload($c);

	}
	
}

spl_autoload_register('buffered_autoloader');

/**
 * Get the plugin object. All the bookkeeping and other setup stuff happens here.
 */

$ns_mc_plugin = NS_MC_Plugin::get_instance();

register_deactivation_hook(__FILE__, array(&$ns_mc_plugin, 'remove_options'));

?>