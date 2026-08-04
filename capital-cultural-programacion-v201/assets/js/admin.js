(function () {
	'use strict';

	function updatePreview(container) {
		var image = container.querySelector('.ccp-admin-focus__image');
		var xInput = container.querySelector('[data-ccp-focus-x]');
		var yInput = container.querySelector('[data-ccp-focus-y]');
		if (!image || !xInput || !yInput) {
			return;
		}

		image.style.objectPosition = xInput.value + '% ' + yInput.value + '%';
	}

	Array.prototype.forEach.call(document.querySelectorAll('[data-ccp-focus-preview]'), function (container) {
		container.addEventListener('input', function () {
			updatePreview(container);
		});

		updatePreview(container);
	});
}());
