<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class MergeFieldRenderers {
	static $renderers;
	public static function get() {
		return self::$renderers;
	}

	public static function render_error_message($errorMessage, $tag) {
		return sprintf('<span
			data-ns-mailchimp-widget-error-%s
			class="error">%s</span>',
			$tag,
			$errorMessage);
	}

	public static function render_help_text($helpText) {
		return !empty($helpText) ? sprintf('<span class="help-text">%s</span>', $helpText) : '';
	}

	public static function render_input_field(
		$name, $tag, $required, $type, $errorMessage, $helpText) {
		return sprintf('
			<label
				data-ns-mailchimp-widget-field="%s">
				<span class="name">%s</span>
				<input
					name="%s"
					%s
					type="%s"
				/>
				%s
				%s
			</label>',
			$tag,
			$name,
			$tag,
			$required ? 'required' : '',
			$type,
			MergeFieldRenderers::render_error_message($errorMessage, $tag),
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
			$errorMessage,
			$helpText);
	},
	'address' => function($mergeField, $helpText, $errorMessage='') {
		return sprintf('
			<div
				data-ns-mailchimp-widget-field="%s">
				<span class="name">%s</span>
				%s
				%s
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][addr1]"
						%s
						type="text" />
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][addr2]"
						type="text" />
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][city]"
						%s
						type="text" />
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][state]"
						%s
						type="text"/>
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][zip]"
						%s
						type="text" />
				</label>
				<label>
					<span class="name">%s</span>
					<input
						name="mergeFields[%s][country]"
						%s
						type="text" />
				</label>
			</div>',
			$mergeField->tag,
			$mergeField->name,
			MergeFieldRenderers::render_error_message($errorMessage, $mergeField->tag),
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
	'dropdown' => function($mergeField, $helpText, $errorMessage='') {
		$renderOptions = function($choices) {
			return join('', array_map(function($choice) {
				return sprintf('<option>%s</option>', $choice);
			}, $choices));
		};
		return sprintf('
			<label
				data-ns-mailchimp-widget-field="%s">
				<span class="name">%s</span>
				<select
					name="mergeFields[%s]"
					%s
				>%s</select>
				%s
				%s
			</label>',
			$mergeField->tag,
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$renderOptions($mergeField->options->choices),
			MergeFieldRenderers::render_error_message($errorMessage, $mergeField->tag),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'radio' => function($mergeField, $helpText, $errorMessage='') {
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
			<div
				data-ns-mailchimp-widget-field="%s">
				<span class="name">%s</span>
				%s
				%s
				%s
			</div>',
			$mergeField->tag,
			$mergeField->name,
			MergeFieldRenderers::render_error_message($errorMessage, $mergeField->tag),
			MergeFieldRenderers::render_help_text($helpText),
			$renderRadios(
				$mergeField->tag,
				$mergeField->required,
				$mergeField->options->choices));
	},
	'birthday' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'date',
			$errorMessage,
			$helpText);
	},
	'date' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'date',
			$errorMessage,
			$helpText);
	},
	'email' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'email',
			$errorMessage,
			$helpText);
	},
	'imageurl' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'url',
			$errorMessage,
			$helpText);
	},
	'number' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'number',
			$errorMessage,
			$helpText);
	},
	'phone' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'tel',
			$errorMessage,
			$helpText);
	},
	'text' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'text',
			$errorMessage,
			$helpText);
	},
	'url' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'url',
			$errorMessage,
			$helpText);
	},
	'zip' => function($mergeField, $helpText, $errorMessage='') {
		return MergeFieldRenderers::render_input_field(
			$mergeField->name,
			"mergeFields[{$mergeField->tag}]",
			$mergeField->required,
			'text',
			$errorMessage,
			$helpText);
	},
);
