/**
 * Surface theme front end. Vanilla JS, no dependencies, deferred.
 *
 *   Menu toggle · header over hero · click-to-play video · gallery filter + lightbox ·
 *   Drift: Surface forms (AJAX to the plugin) · cookie consent for optional GA4
 */
(function () {
	'use strict';

	var cfg = window.Surface || {};
	var t = cfg.i18n || {};

	/* ── Menu ─────────────────────────────────────────────────────────── */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');

	function setMenu(open) {
		if (!toggle || !nav) { return; }
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		nav.classList.toggle('is-open', open);
		document.body.classList.toggle('nav-open', open);
		if (open) {
			var first = nav.querySelector('a');
			if (first) { first.focus(); }
		}
	}

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			setMenu(toggle.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				setMenu(false);
				toggle.focus();
			}
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) { setMenu(false); }
		});
	}

	/* ── Header over a hero: solid once the page scrolls ──────────────── */
	var header = document.querySelector('.has-hero .site-header');
	if (header) {
		var ticking = false;
		var updateHeader = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
			ticking = false;
		};
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(updateHeader);
			}
		}, { passive: true });
		updateHeader(); // A reload mid-page starts solid.
	}

	/* ── Click-to-play video ──────────────────────────────────────────── */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.video__play');
		if (!btn) { return; }
		var box = btn.closest('.video');
		var iframe = document.createElement('iframe');
		iframe.src = box.getAttribute('data-embed');
		iframe.title = btn.getAttribute('aria-label') || '';
		iframe.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
		iframe.allowFullscreen = true;
		box.innerHTML = '';
		box.appendChild(iframe);
		iframe.focus();
	});

	/* ── Click-to-load embeds (Live / Merch Embed) ────────────────────── */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.embed__load');
		if (!btn) { return; }
		var box = btn.closest('.embed');
		var tpl = box.querySelector('template');
		if (!tpl) { return; }
		var label = btn.textContent;
		box.innerHTML = '';
		box.appendChild(tpl.content.cloneNode(true));
		box.classList.add('is-loaded');
		var frames = box.querySelectorAll('iframe');
		frames.forEach(function (f) { if (!f.title) { f.title = label; } });
		if (frames[0]) { frames[0].focus(); }
	});

	/* ── Gallery filter ───────────────────────────────────────────────── */
	document.querySelectorAll('.filter').forEach(function (group) {
		var grid = group.parentElement.querySelector('.photo-grid');
		group.addEventListener('click', function (e) {
			var btn = e.target.closest('.filter__btn');
			if (!btn || !grid) { return; }
			var slug = btn.getAttribute('data-filter');
			group.querySelectorAll('.filter__btn').forEach(function (b) {
				b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
			});
			grid.querySelectorAll('.photo').forEach(function (photo) {
				var albums = (photo.getAttribute('data-albums') || '').split(' ');
				photo.hidden = !!slug && albums.indexOf(slug) === -1;
			});
		});
	});

	/* ── Lightbox ─────────────────────────────────────────────────────── */
	var dialog = null;

	function lightbox(href, caption, alt) {
		if (typeof HTMLDialogElement === 'undefined') {
			window.location.href = href;
			return;
		}
		if (!dialog) {
			dialog = document.createElement('dialog');
			dialog.className = 'lightbox';
			dialog.innerHTML = '<button type="button" class="btn btn--ghost lightbox__close"></button><img alt=""><p class="lightbox__caption"></p>';
			dialog.querySelector('.lightbox__close').textContent = t.close || 'Close';
			dialog.querySelector('.lightbox__close').addEventListener('click', function () { dialog.close(); });
			dialog.addEventListener('click', function (e) { if (e.target === dialog) { dialog.close(); } });
			document.body.appendChild(dialog);
		}
		var img = dialog.querySelector('img');
		img.src = href;
		img.alt = alt || '';
		dialog.querySelector('.lightbox__caption').textContent = caption || '';
		dialog.showModal();
	}

	document.addEventListener('click', function (e) {
		var link = e.target.closest('[data-lightbox]');
		if (!link || e.metaKey || e.ctrlKey) { return; }
		e.preventDefault();
		var img = link.querySelector('img');
		lightbox(link.href, link.getAttribute('data-caption'), img ? img.alt : '');
	});

	/* ── Drift: Surface forms ─────────────────────────────────────────── */
	document.querySelectorAll('form.surface-form').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var status = form.querySelector('.form-status');
			var button = form.querySelector('button[type="submit"]');
			var label = button ? button.textContent : '';

			form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
			if (status) { status.textContent = ''; status.classList.remove('is-error'); }
			if (button) { button.disabled = true; button.textContent = t.sending || 'Sending…'; }

			// getAttribute, not form.action: the form has an <input name="action">
			// (WordPress's AJAX action), and form.action returns that element.
			fetch(form.getAttribute('action') || cfg.ajaxUrl, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
				// Read as text: a PHP error, security plugin or login redirect can
				// answer with HTML, and the visitor should get a readable message
				// rather than the browser's JSON.parse error.
				.then(function (r) { return r.text(); })
				.then(function (body) {
					try {
						return JSON.parse(body);
					} catch (parseError) {
						throw new Error(t.error || 'Something went wrong. Please try again.');
					}
				})
				.then(function (res) {
					var data = (res && res.data) || {};
					if (res && res.success) {
						form.classList.add('is-sent');
						if (status) { status.textContent = data.message || ''; status.setAttribute('tabindex', '-1'); status.focus(); }
						return;
					}
					(data.fields || []).forEach(function (name) {
						var input = form.querySelector('[name="' + name + '"]');
						var field = input && input.closest('.field');
						if (field) { field.classList.add('is-invalid'); input.setAttribute('aria-invalid', 'true'); }
					});
					var firstBad = form.querySelector('.is-invalid input, .is-invalid textarea, .is-invalid select');
					if (firstBad) { firstBad.focus(); }
					throw new Error(data.message || '');
				})
				.catch(function (err) {
					if (status) { status.textContent = (err && err.message) || t.error || 'Something went wrong.'; status.classList.add('is-error'); }
				})
				.then(function () {
					if (button) { button.disabled = false; button.textContent = label; }
				});
		});
	});

	/* ── Consent (only rendered when a GA4 ID is set) ─────────────────── */
	var banner = document.querySelector('.consent');
	var COOKIE = 'surface_consent';

	function readConsent() {
		var m = document.cookie.match(new RegExp('(?:^|; )' + COOKIE + '=([^;]*)'));
		return m ? m[1] : '';
	}

	function saveConsent(value) {
		var maxAge = 60 * 60 * 24 * 180; // Six months.
		document.cookie = COOKIE + '=' + value + '; max-age=' + maxAge + '; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
	}

	function loadAnalytics() {
		if (!cfg.ga4 || window.surfaceGaLoaded) { return; }
		window.surfaceGaLoaded = true;
		window.dataLayer = window.dataLayer || [];
		window.gtag = function () { window.dataLayer.push(arguments); };
		window.gtag('js', new Date());
		window.gtag('config', cfg.ga4, { anonymize_ip: true });
		var s = document.createElement('script');
		s.async = true;
		s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.ga4);
		document.head.appendChild(s);
	}

	if (banner && cfg.ga4) {
		var choice = readConsent();
		if (choice === 'granted') {
			loadAnalytics();
		} else if (!choice) {
			banner.hidden = false;
		}

		banner.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-consent]');
			if (!btn) { return; }
			var value = btn.getAttribute('data-consent') === 'granted' ? 'granted' : 'denied';
			saveConsent(value);
			banner.hidden = true;
			if (value === 'granted') {
				loadAnalytics();
			} else if (window.surfaceGaLoaded) {
				// Withdrawn after accepting: stop on the next page load.
				window.location.reload();
			}
		});

		document.addEventListener('click', function (e) {
			if (e.target.closest('[data-consent-open]')) { banner.hidden = false; }
		});
	}
})();
