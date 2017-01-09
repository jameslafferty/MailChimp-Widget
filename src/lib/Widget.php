<?php
namespace MailChimpWidget;

class Widget extends \WP_Widget {
	public $widgetIds = array();

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
		WidgetJavaScript::init($this);
	}

	function form($instance) {
		$settings = (object) wp_parse_args($instance, array(
			'title' => 'Sign Up For Our Mailing List',
			'mailingList' => ''
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

	function render_merge_field($mergeField) {
		if (!$mergeField->public) {
			return '';
		}
		if (!$mergeField->required) {
			return '';
		}
		$mergeFieldRenderers = MergeFieldRenderers::get();
		if (array_key_exists($mergeField->type, $mergeFieldRenderers)) {
			return $mergeFieldRenderers[$mergeField->type](
				$mergeField,
				$this->render_help_text($mergeField->help_text));
		}
		return '';
	}

	function get_list_merge_fields($listId) {
		$mergeFields = API::get(sprintf('lists/%s/merge-fields/', $listId));
		if (count($mergeFields->merge_fields) < $mergeFields->total_items) {
			$mergeFields = API::get(
				sprintf(
					'lists/%s/merge-fields/?count=%s',
					$listId,
					$mergeFields->total_items));
		}
		return join('', array_map(
			array($this, 'render_merge_field'), $mergeFields->merge_fields));
	}

	function update($newInstance, $oldInstance) {
		return array_map(function($value) {
			return sanitize_text_field($value);
		}, array_merge($oldInstance, $newInstance));
	}

	function widget($args, $instance) {
		$this->widgetIds[] = $args['widget_id'];
		$nonceField = wp_nonce_field(
			'ns-mailchimp-signup', 'ns-mailchimp-signup', true, false);
		$args = (object) $args;
		$instance = (object) $instance;
		$title = !empty($instance->title) ?
			join('', array(
				$args->before_title,
				$instance->title,
				$args->after_title
			)) : '';
		echo sprintf('
			%s
			%s
			<form>
				%s
				<input
					name="mailingListId"
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
			$instance->mailingList,
			$this->get_list_merge_fields($instance->mailingList),
			$args->after_widget);
	}
}
