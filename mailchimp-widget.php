<?php
/*
Plugin Name: MailChimp Widget
Plugin URI: https://github.com/jameslafferty/MailChimp-Widget
Description:
Author: James Lafferty
Text Domain: ns-mailchimp-widget
Domain Path: /languages
Version: 1.0.0
Author URI: https://github.com/jameslafferty
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2017  James Lafferty  (email : james@nearlysensical.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function ns_mailchimp_widget_generic_error() {
	printf('
		<div class="notice notice-error">
			<p>%s</p>
		</div>',
		__('There was an issue with the MailChimp Widget.', 'ns-mailchimp-widget')
	);
}

load_plugin_textdomain('ns-mailchimp-widget', plugins_url('language/', __FILE__));

try {

	if (version_compare(PHP_VERSION, '5.6.29') === -1) {
		throw new Error(
			__(
				'Please upgrade to a more recent version of <a href="http://php.net/downloads.php">PHP</a>(at least 5.6.29) to use the MailChimp Widget.',
				'ns-mailchimp-widget'
			)
		);
	}

	if (!function_exists('curl_init')) {
		throw new Error(
			__(
				'Please install <a href="http://php.net/manual/en/curl.installation.php">PHP with cURL support</a> to use the MailChimp Widget.',
				'ns-mailchimp-widget'
			)
		);
	}

	require __DIR__ . '/vendor/autoload.php';

	MailChimpWidget\Settings::init();

	$options = get_option('ns-mailchimp-widget');
	if (!empty($options['api-key'])) {
		add_action('widgets_init', function() {
			register_widget(new MailChimpWidget\Widget);
		});
	} else {
		add_action('admin_notices', function() {
			printf('
			<div class="notice notice-warning is-dismissible">
				<p>%s</p>
			</div>',
			sprintf(
				__("You'll need to set up the MailChimp Widget plugin settings before using it.
				You can do that <a href='%s'>here</a>.", NS_MAILCHIMP),
				admin_url('/options-general.php?page=mailchimp-widget-settings')));
		});
	}

} catch(Error $e) {
	add_action('admin_notices', function() use ($e) {
		printf('
		<div class="notice notice-error">
			<p>%s</p>
		</div>
		',
		$e->getMessage());
	});
} catch(Error $e) {
	add_action('admin_notices', 'ns_mailchimp_widget_generic_error');
}
