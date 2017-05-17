<?php
namespace MailChimpWidget;

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
}

MergeFieldRenderers::$renderers = array(
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
			__('Address Line 1', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('Address Line 2', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			__('City', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('State', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('ZIP Code', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			__('Country', NS_MAILCHIMP_WIDGET),
			$mergeField->tag,
			$mergeField->required ? 'required' : '');
	},
	'birthday' => function($mergeField, $helpText, $errorMessage='') {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="date"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'date' => function($mergeField, $helpText, $errorMessage='') {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="date"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="email"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'imageurl' => function($mergeField, $helpText, $errorMessage='') {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="url"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'number' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="number"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'phone' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="tel"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="text"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'url' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="url"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));

	},
	'zip' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="text"
				/>
				%s
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_error_message($errorMessage),
			MergeFieldRenderers::render_help_text($helpText));
	},
);
