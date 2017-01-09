<?php
namespace MailChimpWidget;

class MergeFieldRenderers {
	static $renderers;
	public static function get() {
		return self::$renderers;
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
				<label>
					<span class="name">Address Line 1</span>
					<input
						name="mergeFields[%s][addr1]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">Address Line 2</span>
					<input
						data-mc-type="address"
						name="mergeFields[%s][addr2]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">City</span>
					<input
						data-mc-type="address"
						name="mergeFields[%s][city]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">State</span>
					<input
						name="mergeFields[%s][state]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">ZIP Code</span>
					<input
						name="mergeFields[%s][zip]"
						%s
						type="text"
					/>
				</label>
				<label>
					<span class="name">Country</span>
					<input
						name="mergeFields[%s][country]"
						%s
						type="text"
					/>
				</label>
			</div>',
			$mergeField->name,
			MergeFieldRenderers::render_help_text($helpText),
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$mergeField->tag,
			$mergeField->required ? 'required' : '');
	},
	'birthday' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="date"
				/>
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_help_text($helpText));
	},
	'date' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="date"
				/>
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_help_text($helpText));
	},
	'dropdown' => function($mergeField, $helpText) {
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			$renderOptions($mergeField->options->choices),
			MergeFieldRenderers::render_help_text($helpText));
	},
	'email' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="email"
				/>
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_help_text($helpText));
	},
	'imageurl' => function($mergeField, $helpText) {
		return sprintf('
			<label>
				<span class="name">%s</span>
				<input
					name="mergeFields[%s]"
					%s
					type="url"
				/>
				%s
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
			</div>',
			$mergeField->name,
			MergeFieldRenderers::render_help_text($helpText)
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
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
			</label>',
			$mergeField->name,
			$mergeField->tag,
			$mergeField->required ? 'required' : '',
			MergeFieldRenderers::render_help_text($helpText));
	},
);
