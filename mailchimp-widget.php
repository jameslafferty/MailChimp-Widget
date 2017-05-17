<?php
/*
Plugin Name: MailChimp Widget
Plugin URI: https://github.com/jameslafferty/MailChimp-Widget
Description:
Author: James Lafferty
Version: 1.0.0
Author URI: https://github.com/jameslafferty
License: GPL2
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

function ns_mailchimp_widget_generic_error() {
	printf('
		<div class="notice notice-error">
			<p>%s</p>
		</div>',
		__('There was an issue with the MailChimp Widget.', NS_MAILCHIMP_WIDGET)
	);
}

require __DIR__ . '/constants.php';

load_plugin_textdomain(NS_MAILCHIMP_WIDGET, plugins_url('language/', __FILE__));

try {

	if (version_compare(PHP_VERSION, '5.6.29') === -1) {
		throw new Error(
			__(
				'Please upgrade to a more recent version of <a href="http://php.net/downloads.php">PHP</a>(at least 5.6.29) to use the MailChimp Widget.',
				NS_MAILCHIMP_WIDGET
			)
		);
	}

	if (!function_exists('curl_init')) {
		throw new Error(
			__(
				'Please install <a href="http://php.net/manual/en/curl.installation.php">PHP with cURL support</a> to use the MailChimp Widget.',
				NS_MAILCHIMP_WIDGET
			)
		);
	}

	require __DIR__ . '/vendor/autoload.php';

	add_action('admin_init', function() {
		register_setting(
			NS_MAILCHIMP_WIDGET,
			NS_MAILCHIMP_WIDGET);

		add_settings_section(
			NS_MAILCHIMP_WIDGET,
			null,
			function() {
				printf
					(__("
						Enter a valid MailChimp API key here to get started. Once you've done that,
						you can use the MailChimp Widget from the <a href='%s'>Widgets admin page</a>.
						You will need to have at least one MailChimp list set up before the using the
						widget.",
						NS_MAILCHIMP_WIDGET
					),
					get_admin_url(null, 'widgets.php'));
			},
			'mailchimp-widget-settings');

		add_settings_field(
			'api-key',
			__('MailChimp API Key', NS_MAILCHIMP_WIDGET),
			function() {
				printf('<input
					class="regular-text"
					name="ns-mailchimp-widget[api-key]"
					type="password"
					value="%s" />', esc_attr(get_option(NS_MAILCHIMP_WIDGET)['api-key']));
			},
			'mailchimp-widget-settings',
			NS_MAILCHIMP_WIDGET);
		
		add_settings_field(
			'api-endoint',
			__('MailChimp API Endpoint', NS_MAILCHIMP_WIDGET),
			function() {
				printf('<input
					class="regular-text"
					name="ns-mailchimp-widget[api-endpoint]"
					type="text"
					value="%s" />', esc_attr(get_option(NS_MAILCHIMP_WIDGET)['api-endpoint']));
			},
			'mailchimp-widget-settings',
			NS_MAILCHIMP_WIDGET);
	});

	add_action('admin_menu', function() {
		add_options_page(
			__('MailChimp Widget Settings', NS_MAILCHIMP_WIDGET),
			__('MailChimp Widget', NS_MAILCHIMP_WIDGET),
			'manage_options',
			'mailchimp-widget-settings',
			function() {
				printf("
					<div class=\"wrap\">
						<h2>%s</h2>
					</div>
					<form action=\"options.php\" method=\"post\">",
					__('MailChimp Widget Settings', NS_MAILCHIMP_WIDGET)
				);
				settings_fields(NS_MAILCHIMP_WIDGET);
				do_settings_sections('mailchimp-widget-settings');
				submit_button();
				echo "
					</form>";
			}
		);
	});

	$options = get_option(NS_MAILCHIMP_WIDGET);
	if (!empty($options['api-key']) && !empty($options['api-endpoint'])) {
		add_action('widgets_init', function() {
			register_widget('MailChimpWidget\\Widget');
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
