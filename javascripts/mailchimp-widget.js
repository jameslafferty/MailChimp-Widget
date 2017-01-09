(function iife() {
	window.ns_mailchimpwidget.ids.forEach(function(id) {
		document.getElementById(id)
			.querySelector('form')
			.addEventListener('submit', function(e) {
				e.preventDefault();
				var xhr = new XMLHttpRequest();
				xhr.addEventListener('load', function() {
					console.log(arguments);
				});
				xhr.open('POST', ns_mailchimpwidget.url);
				var formData = new FormData(e.target);
				formData.append('action', 'ns_mailchimpsignup')
				xhr.send(formData);
			});
	});
}());
