(function iife() {
	if (window.ns_mailchimpwidget === undefined) {
		return;
	}
	window.ns_mailchimpwidget.ids.forEach(function(id) {
		var widget = document.getElementById(id);
		if (widget === null) {
			return;
		}
		widget
			.querySelector('form')
			.addEventListener('submit', function(e) {
				e.preventDefault();
				var form = e.target;
				var xhr = new XMLHttpRequest();
				xhr.addEventListener('load', function(e) {
					var result = JSON.parse(e.target.responseText);
					console.log(result);
					if (result.success) {
						var p = document.createElement('p');
						p.textContent = result.successMessage;
						form.parentElement.replaceChild(p, form);
					}
				});
				xhr.open('POST', ns_mailchimpwidget.url);
				var formData = new FormData(e.target);
				formData.append('action', 'ns_mailchimpsignup')
				xhr.send(formData);
			});
	});
}());
