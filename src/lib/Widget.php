<?php
namespace MailChimpWidget;

class Widget extends \WP_Widget {
	public static $registration = [];

	public static function parseErrors($response) {
		if (is_array($response->errors)) {
			array_walk($response->errors, function($error) use (&$errors) {
				$errors[$error->field] = $error->message;
			});
		} else {
			$errors = array();
			if ($response->title === 'Member Exists') {
				$errors['email_address'] = __("You've already signed up for this list", NS_MAILCHIMP_WIDGET);
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
			'email_address' => $post['email'],
			'merge_fields' => (object) $mergeFields,
			'status' => 'pending',
		));

		if (isset($response->id) && !empty($response->id)) {
			setcookie(
				sprintf('ns-mailchimp-widget[%s]', $widgetId),
				'registered');
			Widget::$registration[$widgetId] =(object) array(
				'success' => true,
				'successMessage' => $settings['successMessage'],
			);
		} else {
			Widget::$registration[$widgetId] = (object) array(
				'success' => false,
				'errors' => self::parseErrors($response),
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
			esc_html__('MailChimp Widget', NS_MAILCHIMP_WIDGET),
			array(
				'description' => esc_html__(
					'A MailChimp sign up widget.',
					NS_MAILCHIMP_WIDGET
				),
			)
		);
		WidgetJavaScript::init();
	}

	public function _register_one($number) {
		add_action('init', $this->process_request());
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
			'title' => __('Sign Up For Our Mailing List', NS_MAILCHIMP_WIDGET),
			'mailingList' => '',
			'hideOnSuccess' => 'checked',
			'displayOptionalFields' => '',
			'emailLast' => '',
			'successMessage' => __('You have signed up successfully.', NS_MAILCHIMP_WIDGET),
			'signUpButtonText' => __('Sign Up!', NS_MAILCHIMP_WIDGET),
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
		__('Title:', NS_MAILCHIMP_WIDGET),
		__('Select a Mailing List:', NS_MAILCHIMP_WIDGET),
		__('Hide widget after successful sign up?', NS_MAILCHIMP_WIDGET),
		__('Show optional fields?', NS_MAILCHIMP_WIDGET),
		__('Show email field last?', NS_MAILCHIMP_WIDGET),
		__('Success Message:', NS_MAILCHIMP_WIDGET),
		__('Sign Up Button Text:', NS_MAILCHIMP_WIDGET));
	}

	function get_lists($activeList) {
		$options = array_map(function($list) use ($activeList) {
			return sprintf(
				'<option %s value="%s">%s</option>',
				$activeList === $list->id ? 'selected' : '',
				$list->id,
				$list->name);
		}, API::get('lists/')->lists);
		array_unshift($options, '<option value="">Choose one</option>');
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
		$title = !empty($instance->title) ?
			join('', array(
				$args->before_title,
				$instance->title,
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
			<form method="post">
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
			$nonceField,
			$this->number,
			Widget::get_list_merge_fields(
				$instance->mailingList,
				$instance->displayOptionalFields,
				$this->registration ? $this->registration->errors : [],
				$instance->emailLast),
			$instance->signUpButtonText,
			$args->after_widget);
	}
}
