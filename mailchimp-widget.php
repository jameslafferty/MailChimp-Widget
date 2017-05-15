<?php
/*
Plugin Name: MailChimp Widget
Plugin URI: https://github.com/kalchas
Description:
Author: James Lafferty
Version: 0.8.12
Author URI: https://github.com/kalchas
License: GPL2
*/

/*  Copyright 2010  James Lafferty  (email : james@nearlysensical.com)

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
try {

	require __DIR__ . '/vendor/autoload.php';

	add_action('admin_init', function() {
		register_setting(
			'ns-mailchimp-widget',
			'ns-mailchimp-widget');

		add_settings_section(
			'ns-mailchimp-widget',
			null,
			function() {
				echo "Enter a valid MailChimp API key here to get started. Once you've done that, you can use the MailChimp Widget from the Widgets menu. You will need to have at least one MailChimp list set up before the using the widget.";
			},
			'mailchimp-widget-settings');

		add_settings_field(
			'api-key',
			'MailChimp API Key',
			function() {
				printf('<input
					class="regular-text"
					name="ns-mailchimp-widget[api-key]"
					type="password"
					value="%s" />', esc_attr(get_option('ns-mailchimp-widget')['api-key']));
			},
			'mailchimp-widget-settings',
			'ns-mailchimp-widget');
		
		add_settings_field(
			'api-endoint',
			'MailChimp API Endpoint',
			function() {
				printf('<input
					class="regular-text"
					name="ns-mailchimp-widget[api-endpoint]"
					type="text"
					value="%s" />', esc_attr(get_option('ns-mailchimp-widget')['api-endpoint']));
			},
			'mailchimp-widget-settings',
			'ns-mailchimp-widget');
	});

	add_action('admin_menu', function() {
		add_options_page(
			'MailChimp Widget Settings',
			'MailChimp Widget',
			'manage_options',
			'mailchimp-widget-settings',
			function() {
				echo "
					<div class=\"wrap\">
						<h2>MailChimp Widget Settings</h2>
					</div>
					<form action=\"options.php\" method=\"post\">";
				settings_fields('ns-mailchimp-widget');
				do_settings_sections('mailchimp-widget-settings');
				submit_button();
				echo "
					</form>";
			}
		);
	});

	if (function_exists('curl_init')) {
		add_action('widgets_init', function() {
			register_widget('MailChimpWidget\\Widget');
		});
	}

} catch(Error $e) {

}
