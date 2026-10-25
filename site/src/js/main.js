(function () {
	'use strict';

	// Header shadow on scroll
	var header = document.querySelector('[data-header]');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	// Mobile menu
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav = document.querySelector('[data-nav]');
	if (toggle && nav) {
		var setOpen = function (open) {
			toggle.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
		};
		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') setOpen(false);
		});
		document.addEventListener('click', function (e) {
			if (!nav.contains(e.target) && !toggle.contains(e.target)) setOpen(false);
		});
	}

	// Map – loads Google iframe only after click
	var mapBtn = document.querySelector('[data-map-load]');
	if (mapBtn) {
		mapBtn.addEventListener('click', function () {
			var frame = document.createElement('iframe');
			frame.src = mapBtn.getAttribute('data-src');
			frame.title = 'Mapa – HS Elektrostav s.r.o.';
			frame.loading = 'lazy';
			frame.referrerPolicy = 'no-referrer-when-downgrade';
			var card = document.querySelector('[data-map]');
			card.innerHTML = '';
			card.appendChild(frame);
		});
	}

	// Lightbox
	var lb = document.querySelector('[data-lightbox]');
	var links = Array.prototype.slice.call(document.querySelectorAll('[data-gallery] a'));
	if (!lb || !links.length) return;

	var img = lb.querySelector('[data-lb-img]');
	var counter = lb.querySelector('[data-lb-counter]');
	var current = 0;
	var lastFocus = null;

	function show(i) {
		current = (i + links.length) % links.length;
		img.src = links[current].getAttribute('href');
		img.alt = links[current].querySelector('img').alt;
		counter.textContent = (current + 1) + ' / ' + links.length;
		// preload neighbours
		[current + 1, current - 1].forEach(function (n) {
			new Image().src = links[(n + links.length) % links.length].getAttribute('href');
		});
	}

	function open(i) {
		lastFocus = document.activeElement;
		show(i);
		lb.hidden = false;
		document.body.classList.add('lb-open');
		lb.querySelector('[data-lb-close]').focus();
	}

	function close() {
		lb.hidden = true;
		document.body.classList.remove('lb-open');
		if (lastFocus) lastFocus.focus();
	}

	links.forEach(function (a, i) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			open(i);
		});
	});

	lb.querySelector('[data-lb-close]').addEventListener('click', close);
	lb.querySelector('[data-lb-prev]').addEventListener('click', function () { show(current - 1); });
	lb.querySelector('[data-lb-next]').addEventListener('click', function () { show(current + 1); });
	lb.addEventListener('click', function (e) {
		if (e.target === lb) close();
	});

	document.addEventListener('keydown', function (e) {
		if (lb.hidden) return;
		if (e.key === 'Escape') close();
		else if (e.key === 'ArrowLeft') show(current - 1);
		else if (e.key === 'ArrowRight') show(current + 1);
	});

	// Swipe on touch devices
	var startX = null;
	lb.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
	lb.addEventListener('touchend', function (e) {
		if (startX === null) return;
		var dx = e.changedTouches[0].clientX - startX;
		if (Math.abs(dx) > 50) show(current + (dx < 0 ? 1 : -1));
		startX = null;
	});
})();
