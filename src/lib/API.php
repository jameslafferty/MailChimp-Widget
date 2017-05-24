<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class API {
	public static $apiKey;
	public static $apiEndpoint;

	public static function get($path) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(
			$ch,
			CURLOPT_URL,
			join('', array(
				self::$apiEndpoint,
				$path
			)));
		curl_setopt(
			$ch,
			CURLOPT_USERPWD,
			sprintf('ns-mailchimp-widget:%s', self::$apiKey));
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		return $response;
	}

	public static function post($path, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(
			$ch,
			CURLOPT_URL,
			join('', array(
				self::$apiEndpoint,
				$path
			)));
		curl_setopt(
			$ch,
			CURLOPT_USERPWD,
			sprintf('ns-mailchimp-widget:%s', self::$apiKey));
		$payload = json_encode($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		return $response;
	}
}

API::$apiKey = get_option('ns-mailchimp-widget')['api-key'];
API::$apiEndpoint = sprintf('https://%s.api.mailchimp.com/3.0/', split('-', API::$apiKey)[1]);
