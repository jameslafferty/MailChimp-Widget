<?php
namespace MailChimpWidget;

class Widget extends \WP_Widget {

	public static function verifyNonce() {
		return wp_verify_nonce(
				$_REQUEST['ns-mailchimp-signup'], 'ns-mailchimp-signup');
	}

	function __construct() {
		parent::__construct(
			'mailchimp-widget',
			esc_html__('MailChimp Widget', 'mailchimp-widget'),
			array(
				'description' => esc_html__(
					'A MailChimp sign up widget.',
					'mailchimp-widget'
				)
			)
		);
		WidgetJavaScript::init();
	}

	function form($instance) {
		$settings = (object) wp_parse_args($instance, array(
			'title' => 'Sign Up For Our Mailing List',
			'mailingList' => '',
			'displayOptionalFields' => '',
			'successMessage' => 'You have signed up successfully.',
		));
		echo "
		<p>
			<label for=\"{$this->get_field_id('title')}\"></label>
			<input
				class=\"widefat\"
				id=\"{$this->get_field_id('title')}\"
				name=\"{$this->get_field_name('title')}\"
				type=\"text\"
				value=\"{$settings->title}\" />
		</p>
		<p>
			<label for=\"{$this->get_field_id('mailingList')}\">Select a Mailing List:</label>
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
				{$settings->displayOptionalFields}
				class=\"checkbox\"
				id=\"{$this->get_field_id('displayOptionalFields')}\"
				name=\"{$this->get_field_name('displayOptionalFields')}\"
				type=\"checkbox\">
			<label for=\"{$this->get_field_id('displayOptionalFields')}\">Show optional fields?</label>
		</p>
		<p>
			<label for=\"{$this->get_field_id('successMessage')}\">Success Message:</label>
			<textarea
				class=\"widefat\"
				id=\"{$this->get_field_id('successMessage')}\"
				name=\"{$this->get_field_name('successMessage')}\"
				type=\"text\"
				value=\"{$settings->successMessage}\"></textarea>
		</p>
		";
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

	function render_merge_field($mergeField, $displayOptionalFields) {
		if (!$mergeField->public) {
			return '';
		}
		if (!$mergeField->required && !$displayOptionalFields) {
			return '';
		}
		$mergeFieldRenderers = MergeFieldRenderers::get();
		if (array_key_exists($mergeField->type, $mergeFieldRenderers)) {
			return $mergeFieldRenderers[$mergeField->type](
				$mergeField,
				MergeFieldRenderers::render_help_text($mergeField->help_text));
		}
		return '';
	}

	function get_list_merge_fields($listId, $displayOptionalFields) {
		$mergeFields = API::get(sprintf('lists/%s/merge-fields/', $listId));
		if (count($mergeFields->merge_fields) < $mergeFields->total_items) {
			$mergeFields = API::get(
				sprintf(
					'lists/%s/merge-fields/?count=%s',
					$listId,
					$mergeFields->total_items));
		}
		return join('', array_map(
			array($this, 'render_merge_field'), $mergeFields->merge_fields,
				array_fill(
					0,
					count($mergeFields->merge_fields),
					$displayOptionalFields === 'checked'
				)
			)
		);
	}

	function update($newInstance, $oldInstance) {
		$newInstance['displayOptionalFields'] = $newInstance['displayOptionalFields'] ? 'checked' : '';
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
		if (self::verifyNonce() &&
			$_POST['widgetId'] === $this->id) {
			$registration = $this->registerUser($_POST);
			if ($registration->success) {
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
		}
		$nonceField = wp_nonce_field(
			'ns-mailchimp-signup', 'ns-mailchimp-signup', true, false);
		return printf('
			%s
			%s
			<form method="post">
				%s
				<input
					name="widgetId"
					type="hidden"
					value="%s"/>
				%s
				<label>
					<span>Email Address</span>
					<input
						name="email"
						required
						type="email" />
				</label>
				<button type="submit">
					<span>Sign Up!</span>
				</button>
			</form>
			%s',
			$args->before_widget,
			$title,
			$nonceField,
			$args->widget_id,
			$this->get_list_merge_fields($instance->mailingList, $instance->displayOptionalFields),
			$args->after_widget);
	}

	function registerUser($post) {
		$settings = $this->get_settings()[$this->number];
		$mailingListId = $settings['mailingList'];
		$mergeFields = is_array($_POST['mergeFields']) ? array_filter($post['mergeFields'], function($mergeField) {
				return !empty($mergeField) && $mergeField !== '';
			}) : [];
		$response = API::post(sprintf('lists/%s/members/', $mailingListId),
			(object) array(
			'email_address' => $post['email'],
			'merge_fields' => (object) $mergeFields,
			'status' => 'pending',
		));
		if (isset($response->id) && !empty($response->id)) {
			return (object) array(
				'success' => true,
				'successMessage' => $settings['successMessage'],
			);
		}
		$response->success = false;
		return $response;
	}
}
