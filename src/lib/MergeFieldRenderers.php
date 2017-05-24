<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class MergeFieldRenderers {
	static $renderers;
	public static function get() {
		return self::$renderers;
	}

	public static function render_error_message($errorMessage) {
		return !empty($errorMessage) ? sprintf('<span class="error">%s</span>', $errorMessage) : '';
	}

	public static function render_help_text($helpText) {
		return !empty($helpText) ? sprintf('<span class="help-text">%s</span>', $helpText) : '';
	}

	public static function render_input_field(
		$name, $tag, $required, $type, $errorMessage, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="%s"
					%s
					type="%s"
				/>
				%s
				%s
			</label>',
			$name,
			$tag,
			$required ? 'required' : '',
			$type,
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	}
}

MergeFieldRenderers::$renderers = array(
	'primary_email' => function($mergeField, $helpText, $errorMessage) {
		return MergeFieldRenderers::render_input_field(
			__('Email Address', 'ns-mailchimp-widget'),
			'email',
			true,
			'email',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'address' => function($mergeField, $helpText, $errorMessage='') {
		return sprintf('
			<div>
				<span class="name">%s</span>
				%s
				%s
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][addr1]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						data-mc-type="address"
						name="mergeFields[%s][addr2]"
						type="text"
					/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						data-mc-type="address"
						name="mergeFields[%s][city]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][state]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][zip]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][country]"
						%s
						type="text"
					/>
				</label>
			</div>',
			$mergeField->name,
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText),
			__('Address Line 1', 'ns-mailchimp-widget'),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('Address Line 2', 'ns-mailchimp-widget'),
			$mergeField->tag,
			__('City', 'ns-mailchimp-widget'),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('State', 'ns-mailchimp-widget'),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('ZIP Code', 'ns-mailchimp-widget'),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('Country', 'ns-mailchimp-widget'),
			$mergeField->tag,
			$mergeField->required ? 'required' : '');
	},
	'birthday' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'date',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'date' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'date',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'dropdown' => function($mergeField, $helpText, $errorMessage='') {
		$renderOptions = function($choices) {
			return join('', array_map(function($choice) {
				return sprintf('<option>%s</option>', $choice);
			}, $choices));
		};
		return sprintf('
			<label>
				<span class="name">%s</span>
				<select
					name="mergeFields[%s]"
					%s
				>%s</select>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$renderOptions($mergeField->options->choices),
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'email' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'email',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'imageurl' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'url',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'number' => function($mergeField, $helpText) {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'number',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'phone' => function($mergeField, $helpText) {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'tel',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'radio' => function($mergeField, $helpText) {
		$renderRadios = function($tag, $required, $choices) {
			return join('', array_map(function($choice) use ($tag, $required) {
				return sprintf('
					<label>
						<input
							name="mergeFields[%s]"
							%s
							type="radio"
							value="%s" />
						<span>%s</span>
					</label>',
					$tag,
					$required ? 'required' : '',
					$choice,
					$choice);
			}, $choices));
		};
		return sprintf('
			<div>
				<span class="name">%s</span>
				%s
				%s
				%s
			</div>',
			$mergeField->name,
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText),
			$renderRadios(
				$mergeField->tag,
				$mergeField->required,
				$mergeField->options->choices));
	},
	'text' => function($mergeField, $helpText) {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'text',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'url' => function($mergeField, $helpText) {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'url',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'zip' => function($mergeField, $helpText) {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'text',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
);
