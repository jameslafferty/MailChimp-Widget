<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Widget extends \WP_Widget {
	public static $registration = [];

	public static function parseErrors($response) {
		if (is_array($response->errors) && count($response->errors) > 0) {
			array_walk($response->errors, function($error) use (&$errors) {
				$errors[$error->field] = esc_html($error->message);
			});
		} else {
			$errors = array();
			if ($response->title === 'Member Exists') {
				$errors['email_address'] = esc_html__('You have already signed up for this list', 'ns-mailchimp-widget');
			}
		}
		return $errors;
	}

	public static function registerUser($post, $widgetId, $successMessage, $mailingListId) {
		$mergeFields = is_array($post['mergeFields']) ? array_filter($post['mergeFields'], function($mergeField) {
				return !empty($mergeField) && $mergeField !== '';
			}) : [];

		$response = API::post(sprintf('lists/%s/members/', $mailingListId),
			(object) array(
			'email_address' => $post['email_address'],
			'merge_fields' => (object) $mergeFields,
			'status' => 'pending',
		));

		if (isset($response->id) && !empty($response->id)) {
			setcookie(
				sprintf('ns-mailchimp-widget[%s]', $widgetId),
				'registered',
				strtotime('+10 years'),
				'/');
			Widget::$registration[$widgetId] =(object) array(
				'success' => true,
				'successMessage' => $successMessage,
			);
		} else {
			$errors = Widget::parseErrors($response);
			Widget::$registration[$widgetId] = (object) array(
				'success' => false,
				'errors' => $errors,
			);
		}
		return Widget::$registration[$widgetId];
	}

	public static function verifyNonce() {
		return wp_verify_nonce(
			$_REQUEST['ns-mailchimp-signup'], 'ns-mailchimp-signup');
	}

	public static function render_merge_field($mergeField, $errorMessage) {
		if (!$mergeField->public) {
			return '';
		}
		$mergeFieldRenderers = MergeFieldRenderers::get();
		if (array_key_exists($mergeField->type, $mergeFieldRenderers)) {
			return $mergeFieldRenderers[$mergeField->type](
				$mergeField,
				MergeFieldRenderers::render_help_text($mergeField->help_text),
				$errorMessage
			);
		}
		return '';
	}

	public static function get_list_merge_fields($listId, $displayOptionalFields, array $errors, $emailLast) {
		$mergeFields = API::get(sprintf('lists/%s/merge-fields/?count=-1&required=%s',
			$listId,
			$displayOptionalFields ? 'false' : 'true'));

		if (count($mergeFields->merge_fields) < $mergeFields->total_items) {
			$mergeFields = API::get(
				sprintf(
					'lists/%s/merge-fields/?count=%s&required=%s',
					$listId,
					$mergeFields->total_items,
					$displayOptionalFields ? 'false' : 'true'));
		}

		$primaryEmail = (object) array(
			'public' => true,
			'required' => true,
			'tag' => 'email_address',
			'type' => 'primary_email',
		);

		if ($emailLast) {
			array_push($mergeFields->merge_fields, $primaryEmail);
		} else {
			array_unshift($mergeFields->merge_fields, $primaryEmail);
		}
		return join('', array_map(
			[__CLASS__, 'render_merge_field'], $mergeFields->merge_fields,
				array_map(function($mergeField) use (&$errors) {
					if (array_key_exists($mergeField->tag, $errors)) {
						return $errors[$mergeField->tag];
					}
					return '';
				}, $mergeFields->merge_fields)
			)
		);
	}

	function __construct() {
		parent::__construct(
			'mailchimp-widget',
			esc_html__('MailChimp Widget', 'ns-mailchimp-widget'),
			array(
				'description' => esc_html__(
					'A MailChimp sign up widget.',
					'ns-mailchimp-widget'
				),
			)
		);
		WidgetJavaScript::init();
	}

	public function _register_one($number) {
		add_action('parse_request', $this->process_request());
		return parent::_register_one($number);
	}

	public function process_request() {
		$number = "{$this->number}";
		$settings = (object) $this->get_settings()[$this->number];
		$mailingListId = $settings->mailingList;
		$successMessage = $settings->successMessage;
		return function() use ($number, $successMessage, $mailingListId) {
			if (Widget::verifyNonce() &&
				$_POST['mailChimpWidgetNumber'] === $number) {
				Widget::registerUser($_POST, $number, $successMessage, $mailingListId);
			}
		};
	}

	function form($instance) {
		$settings = (object) wp_parse_args($instance, array(
			'title' => esc_html__('Sign Up For Our Mailing List', 'ns-mailchimp-widget'),
			'mailingList' => '',
			'hideOnSuccess' => 'checked',
			'displayOptionalFields' => '',
			'emailLast' => '',
			'successMessage' => esc_html__('You have signed up successfully.', 'ns-mailchimp-widget'),
			'signUpButtonText' => esc_html__('Sign Up!', 'ns-mailchimp-widget'),
		));
		printf("
		<p>
			<label for=\"{$this->get_field_id('title')}\">%s</label>
			<input
				class=\"widefat\"
				id=\"{$this->get_field_id('title')}\"
				name=\"{$this->get_field_name('title')}\"
				type=\"text\"
				value=\"{$settings->title}\" />
		</p>
		<p>
			<label for=\"{$this->get_field_id('mailingList')}\">%s</label>
			<select
				class=\"widefat\"
				id=\"{$this->get_field_id('mailingList')}\"
				name=\"{$this->get_field_name('mailingList')}\"
				required>
				{$this->get_lists($settings->mailingList)}
			</select>
		</p>
		<p>
			<input
				{$settings->hideOnSuccess}
				class=\"checkbox\"
				id=\"{$this->get_field_id('hideOnSuccess')}\"
				name=\"{$this->get_field_name('hideOnSuccess')}\"
				type=\"checkbox\">
			<label for=\"{$this->get_field_id('hideOnSuccess')}\">%s</label>
		</p>
		<p>
			<input
				{$settings->displayOptionalFields}
				class=\"checkbox\"
				id=\"{$this->get_field_id('displayOptionalFields')}\"
				name=\"{$this->get_field_name('displayOptionalFields')}\"
				type=\"checkbox\">
			<label for=\"{$this->get_field_id('displayOptionalFields')}\">%s</label>
		</p>
		<p>
			<input
				{$settings->emailLast}
				class=\"checkbox\"
				id=\"{$this->get_field_id('emailLast')}\"
				name=\"{$this->get_field_name('emailLast')}\"
				type=\"checkbox\">
			<label for=\"{$this->get_field_id('emailLast')}\">%s</label>
		</p>
		<p>
			<label for=\"{$this->get_field_id('successMessage')}\">%s</label>
			<textarea
				class=\"widefat\"
				id=\"{$this->get_field_id('successMessage')}\"
				name=\"{$this->get_field_name('successMessage')}\">{$settings->successMessage}</textarea>
		</p>
		<p>
			<label for=\"{$this->get_field_id('signUpButtonText')}\">%s</label>
			<input
				class=\"widefat\"
				id=\"{$this->get_field_id('signUpButtonText')}\"
				name=\"{$this->get_field_name('signUpButtonText')}\"
				type=\"text\"
				value=\"{$settings->signUpButtonText}\" />
		</p>
		",
		esc_html__('Title:', 'ns-mailchimp-widget'),
		esc_html__('Select a Mailing List:', 'ns-mailchimp-widget'),
		esc_html__('Hide widget after successful sign up?', 'ns-mailchimp-widget'),
		esc_html__('Show optional fields?', 'ns-mailchimp-widget'),
		esc_html__('Show email field last?', 'ns-mailchimp-widget'),
		esc_html__('Success Message:', 'ns-mailchimp-widget'),
		esc_html__('Sign Up Button Text:', 'ns-mailchimp-widget'));
	}

	function get_lists($activeList) {
		$options = array_map(function($list) use ($activeList) {
			return sprintf(
				'<option %s value="%s">%s</option>',
				$activeList === $list->id ? 'selected' : '',
				esc_attr($list->id),
				esc_html($list->name));
		}, API::get('lists/')->lists);
		array_unshift($options, sprintf(
			'<option value="">%s</option>', esc_html__('Choose one', 'ns-mailchimp-widget')));
		return join('', $options);
	}

	function update($newInstance, $oldInstance) {
		$newInstance['hideOnSuccess'] = $newInstance['hideOnSuccess'] ? 'checked' : '';
		$newInstance['displayOptionalFields'] = $newInstance['displayOptionalFields'] ? 'checked' : '';
		$newInstance['emailLast'] = $newInstance['emailLast'] ? 'checked' : '';
		return array_map(function($value) {
			return sanitize_text_field($value);
		}, array_merge($oldInstance, $newInstance));
	}

	function widget($args, $instance) {
		$args = (object) $args;
		$instance = (object) $instance;
		if ($instance->hideOnSuccess &&
			$_COOKIE['ns-mailchimp-widget'][$this->number]) {
			return $this;
		}
		$title = !empty($instance->title) ?
			join('', array(
				$args->before_title,
				esc_html($instance->title),
				$args->after_title,
			)) : '';
		$this->registration = Widget::$registration[$this->number];
		if ($this->registration->success) {
			return printf('
				%s
				%s
				%s
				%s
			',
			$args->before_widget,
			$title,
			esc_html($registration->successMessage),
			$args->after_widget);
		}

		$nonceField = wp_nonce_field(
			'ns-mailchimp-signup', 'ns-mailchimp-signup', true, false);
		return printf('
			%s
			%s
			<form
				action="#%s"
				method="post">
				%s
				<input
					name="mailChimpWidgetNumber"
					type="hidden"
					value="%s"/>
				%s
				<button type="submit">
					<span>%s</span>
				</button>
			</form>
			%s',
			$args->before_widget,
			$title,
			$this->id,
			$nonceField,
			$this->number,
			Widget::get_list_merge_fields(
				$instance->mailingList,
				$instance->displayOptionalFields,
				$this->registration ? $this->registration->errors : [],
				$instance->emailLast),
			esc_html($instance->signUpButtonText),
			$args->after_widget);
	}
}
