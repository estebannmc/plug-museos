(function () {
	'use strict';

	var activeModal = null;
	var activeDialog = null;
	var lastTrigger = null;
	var focusableSelector = [
		'a[href]',
		'button:not([disabled])',
		'textarea:not([disabled])',
		'input:not([disabled])',
		'select:not([disabled])',
		'[tabindex]:not([tabindex="-1"])'
	].join(',');

	function getFocusableElements(container) {
		return Array.prototype.slice.call(container.querySelectorAll(focusableSelector)).filter(function (element) {
			return element.offsetParent !== null || element === document.activeElement;
		});
	}

	function openModal(modal, trigger) {
		if (!modal) {
			return;
		}

		if (activeModal) {
			closeModal(false);
		}

		activeModal = modal;
		activeDialog = modal.querySelector('.ccp-modal__dialog');
		lastTrigger = trigger;

		modal.hidden = false;
		document.body.classList.add('ccp-modal-open');

		if (activeDialog) {
			activeDialog.focus({ preventScroll: true });
		}
	}

	function closeModal(restoreFocus) {
		if (!activeModal) {
			return;
		}

		activeModal.hidden = true;
		document.body.classList.remove('ccp-modal-open');

		if (restoreFocus !== false && lastTrigger && typeof lastTrigger.focus === 'function') {
			lastTrigger.focus({ preventScroll: true });
		}

		activeModal = null;
		activeDialog = null;
		lastTrigger = null;
	}

	function trapFocus(event) {
		if (!activeDialog || event.key !== 'Tab') {
			return;
		}

		var focusable = getFocusableElements(activeDialog);
		if (!focusable.length) {
			event.preventDefault();
			activeDialog.focus();
			return;
		}

		var first = focusable[0];
		var last = focusable[focusable.length - 1];

		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	}

	function moveSlider(button, direction) {
		var slider = button.closest('[data-ccp-slider]');
		if (!slider) {
			return;
		}

		var viewport = slider.querySelector('[data-ccp-slider-viewport]');
		var slide = slider.querySelector('.ccp-slider__slide');
		if (!viewport || !slide) {
			return;
		}

		var gap = 20;
		var slideWidth = slide.getBoundingClientRect().width;
		viewport.scrollBy({
			left: direction * (slideWidth + gap),
			behavior: 'smooth'
		});
	}

	document.addEventListener('click', function (event) {
		var trigger = event.target.closest('[data-ccp-modal-target]');
		if (trigger) {
			event.preventDefault();
			openModal(document.getElementById(trigger.getAttribute('data-ccp-modal-target')), trigger);
			return;
		}

		if (event.target.closest('[data-ccp-close]')) {
			event.preventDefault();
			closeModal(true);
			return;
		}

		var prevButton = event.target.closest('[data-ccp-slider-prev]');
		if (prevButton) {
			event.preventDefault();
			moveSlider(prevButton, -1);
			return;
		}

		var nextButton = event.target.closest('[data-ccp-slider-next]');
		if (nextButton) {
			event.preventDefault();
			moveSlider(nextButton, 1);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (!activeModal) {
			return;
		}

		if (event.key === 'Escape') {
			event.preventDefault();
			closeModal(true);
			return;
		}

		trapFocus(event);
	});
}());
