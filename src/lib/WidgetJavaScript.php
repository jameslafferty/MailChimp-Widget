<?php
namespace MailChimpWidget;

class WidgetJavaScript {

	public static function init($widgetInstance) {
		print_r($widgetInstance);
		$handler = function() {
			if (wp_verify_nonce(
				$_REQUEST['ns-mailchimp-signup'], 'ns-mailchimp-signup')) {
				header("Content-Type: application/json");
				$response = API::post(sprintf('lists/%s/members/', $_POST['mailingListId']),
					(object) array(
					'email_address' => $_POST['email'],
					'merge_fields' => array_filter($_POST['mergeFields'], function($mergeField) {
						return !empty($mergeField) && $mergeField !== '';
					}),
					'status' => 'pending',
				));
				if (isset($response->id) && !empty($response->id)) {
					exit(json_encode(array(
						'msg' => 'success!!',
					)));
				}
				exit(json_encode($response));
			}
			header("Content-Type: application/json");
			exit(json_encode(
				array('message' => 'Something is fishy here.')));
		};
		add_action('wp_ajax_ns_mailchimpsignup', $handler);
		add_action('wp_ajax_nopriv_ns_mailchimpsignup', $handler);
		add_action('wp_footer', function() {
			print_r($widgetInstance);
			wp_localize_script(
				'ns_mailchimpwidget',
				'ns_mailchimpwidget',
				array(
					'url' => admin_url('admin-ajax.php'),
					'ids' => $widgetInstance->widgetIds,
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
