<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function get_registered_widgets() {
	global $wp_registered_widgets;
	return $wp_registered_widgets;
}

class WidgetJavaScript {

	public static function init() {
		$handler =  function() {
			if (Widget::verifyNonce()) {
				header("Content-Type: application/json");
				$settings = (object)(new Widget())->get_settings()[$_POST['mailChimpWidgetNumber']];
				exit(json_encode(
					Widget::registerUser(
						$_POST,
						$_POST['mailChimpWidgetNumber'],
						$settings->successMessage,
						$settings->mailingList)));
			}
		};

		if (is_user_logged_in()) {
			add_action('wp_ajax_ns_mailchimpsignup', $handler);
		} else {
			add_action('wp_ajax_nopriv_ns_mailchimpsignup', $handler);
		}

		$widgetIds = [];
		add_action('wp_register_sidebar_widget', function($widget) use (&$widgetIds) {
			if ($widget['callback'][0] instanceof Widget) {
				$widgetIds[] = $widget['callback'][0]->id;
			}
		});
		add_action('wp_footer', function() use (&$widgetIds) {
			wp_localize_script(
				'ns_mailchimpwidget',
				'ns_mailchimpwidget',
				array(
					'url' => admin_url('admin-ajax.php'),
					'ids' => $widgetIds,
				));
		});
		wp_enqueue_script(
			'ns_mailchimpwidget',
			plugins_url('../../javascripts/mailchimp-widget.js', __FILE__),
			array(),
			false,
			true
		);
	}
}
