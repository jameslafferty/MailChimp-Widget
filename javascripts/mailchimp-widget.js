(function iife() {
	if (!window.XMLHttpRequest ||
		!document.querySelector ||
		!window.FormData ||
		!('classList' in Element.prototype) ||
		!Array.prototype.slice ||
		!window.ns_mailchimpwidget) {
		return;
	}

	function submit(e) {
		e.preventDefault();
		var form = e.target;
		var formControls = Array.prototype.slice.call(
			form.querySelectorAll('input,button'));
		var xhr = new XMLHttpRequest();

		xhr.addEventListener('load', function(e) {
			formControls.forEach(function(control) {
				control.removeAttribute('disabled');
			});
			var result = JSON.parse(e.target.responseText);
			if (result.success) {
				var p = document.createElement('p');
				p.textContent = result.successMessage;
				form.parentElement.replaceChild(p, form);
			} else {
				for (var prop in result.errors) {
					form.querySelector(
						'[data-ns-mailchimp-widget-error-for*=' + prop + ']')
						.textContent = result.errors[prop];
					form.querySelector(
						'[data-ns-mailchimp-widget-field*=' + prop + ']')
							.classList.add('invalid');
				}
			}
		});

		xhr.open('POST', ns_mailchimpwidget.url);
		var formData = new FormData(e.target);
		formData.append('action', 'ns_mailchimpsignup');
		formControls.forEach(function(control) {
			control.setAttribute('disabled', true);
		});
		xhr.send(formData);
	}

	function init(id) {
		var widget = document.getElementById(id);
		if (widget === null) {
			return;
		}
		widget
			.querySelector('form')
			.addEventListener('submit', submit);
	}

	Array.prototype.slice.call(window.ns_mailchimpwidget.ids)
		.forEach(init);
}());
