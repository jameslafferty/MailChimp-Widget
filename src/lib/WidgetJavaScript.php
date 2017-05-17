<?php
namespace MailChimpWidget;

function get_registered_widgets() {
	global $wp_registered_widgets;
	return $wp_registered_widgets;
}

class WidgetJavaScript {

	public static function init() {
		
		$handler =  function() {
			if (Widget::verifyNonce()) {
				header("Content-Type: application/json");
				$widget = get_registered_widgets()[$_POST['widgetId']]['callback'][0];
				exit(json_encode($widget->registerUser($_POST)));
			}
		};

		if (is_user_logged_in()) {
			add_action('wp_ajax_ns_mailchimpsignup', $handler);
		} else {
			add_action('wp_ajax_nopriv_ns_mailchimpsignup', $handler);
		}

		$widgets = [];
		add_action('wp_register_sidebar_widget', function($widget) use (&$widgets) {
			if ($widget['callback'][0] instanceof Widget) {
				$widgets[] = $widget['callback'][0];
			}
		});
		add_action('wp_footer', function() use (&$widgets) {
			$widgetIds = array_map(function($item) {
				return $item->id;
			}, $widgets);
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
