(function () {
	'use strict';

	var activeModal = null;
	var activeDialog = null;
	var lastTrigger = null;
	var lastInputWasKeyboard = false;
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

		if (restoreFocus !== false && lastInputWasKeyboard && lastTrigger && typeof lastTrigger.focus === 'function') {
			lastTrigger.focus({ preventScroll: true });
		} else if (lastTrigger && typeof lastTrigger.blur === 'function') {
			lastTrigger.blur();
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

	function getSliderParts(slider) {
		if (!slider) {
			return null;
		}

		var viewport = slider.querySelector('[data-ccp-slider-viewport]');
		var track = slider.querySelector('.ccp-slider__track');
		var slide = slider.querySelector('.ccp-slider__slide');
		if (!viewport || !track || !slide) {
			return null;
		}

		return {
			viewport: viewport,
			track: track,
			slide: slide
		};
	}

	function getSliderStep(parts) {
		var styles = window.getComputedStyle(parts.track);
		var gap = parseFloat(styles.columnGap || styles.gap || '20') || 20;

		return parts.slide.getBoundingClientRect().width + gap;
	}

	function scrollSlider(slider, direction) {
		var parts = getSliderParts(slider);
		if (!parts) {
			return;
		}

		var viewport = parts.viewport;
		var maxScroll = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
		var target = viewport.scrollLeft + (direction * getSliderStep(parts));

		if (direction > 0 && target >= maxScroll - 2) {
			target = 0;
		} else if (direction < 0 && target <= 2) {
			target = maxScroll;
		}

		viewport.scrollTo({
			left: target,
			behavior: 'smooth'
		});
	}

	function moveSlider(button, direction) {
		scrollSlider(button.closest('[data-ccp-slider]'), direction);
	}

	function initSlider(slider) {
		var parts = getSliderParts(slider);
		var shouldAutoplay = slider.getAttribute('data-ccp-autoplay') === '1';
		var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (!parts || !shouldAutoplay || reducedMotion || slider.querySelectorAll('.ccp-slider__slide').length < 2) {
			return;
		}

		var interval = parseInt(slider.getAttribute('data-ccp-interval'), 10) || 4500;
		var pauseOnHover = slider.getAttribute('data-ccp-pause-hover') === '1';
		var timer = null;

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function start() {
			if (timer) {
				return;
			}

			timer = window.setInterval(function () {
				if (document.hidden || activeModal) {
					return;
				}

				scrollSlider(slider, 1);
			}, interval);
		}

		if (pauseOnHover) {
			slider.addEventListener('pointerenter', stop);
			slider.addEventListener('pointerleave', start);
			slider.addEventListener('focusin', stop);
			slider.addEventListener('focusout', start);
		}

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				stop();
			} else {
				start();
			}
		});

		start();
	}

	document.addEventListener('pointerdown', function () {
		lastInputWasKeyboard = false;
	}, true);

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
		lastInputWasKeyboard = true;

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

	Array.prototype.forEach.call(document.querySelectorAll('[data-ccp-slider]'), initSlider);
}());
