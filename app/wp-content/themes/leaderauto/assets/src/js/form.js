import { bindPhoneField, checkPhoneField, PHONE_ERROR } from './phone.js';

/** Inputs the visitor fills in; the honeypot and buttons don't count. */
function isField(el) {
	return el.matches('input, textarea, select') && el.willValidate && !el.closest('.contact-form__hp');
}

/** Check one field and show or clear its error. Returns true when it's valid. */
function validateField(el) {
	if (el.name === 'phone') checkPhoneField(el);
	if (el.validity.valid) {
		markField(el, '');
		return true;
	}
	let msg = el.validationMessage;
	if (el.validity.valueMissing) msg = 'Заповніть це поле.';
	else if (el.validity.customError && el.name === 'phone') msg = PHONE_ERROR;
	markField(el, msg);
	return false;
}

/** Red outline + message under the field; an empty msg removes both. */
function markField(el, msg) {
	const id = `${el.id}-error`;
	let note = document.getElementById(id);
	if (!msg) {
		el.removeAttribute('aria-invalid');
		el.removeAttribute('aria-describedby');
		note?.remove();
		return;
	}
	if (!note) {
		note = document.createElement('span');
		note.className = 'contact-form__error';
		note.id = id;
		el.after(note);
	}
	note.textContent = msg;
	el.setAttribute('aria-invalid', 'true');
	el.setAttribute('aria-describedby', id);
}

export function initContactForm() {
	// Every .contact-form on the page: the Contacts page has one, the home page
	// renders a second copy inside the services dialog.
	document.querySelectorAll('.contact-form').forEach(bindForm);
	prefillFromQuery();
}

/** Text put in the message box when a visitor arrives from a service link. */
export function serviceMessage(name) {
	return `Цікавить послуга: ${name}`;
}

/** A ?service= in the address means the visitor came from a service link. */
function prefillFromQuery() {
	const name = new URLSearchParams(window.location.search).get('service');
	if (!name) return;

	const field = document.querySelector('.contact-form [name="message"]');
	if (field && !field.value) field.value = serviceMessage(name);
}

function bindForm(form) {
	const cfg = window.LEADERAUTO || {};
	const status = form.querySelector('.contact-form__status');
	const button = form.querySelector('button[type="submit"]');
	const phone = form.querySelector('[name="phone"]');
	if (phone) bindPhoneField(phone);

	// Leaving a field shows its error; once shown, it clears as soon as the value is fixed.
	form.addEventListener('change', (e) => {
		if (isField(e.target)) validateField(e.target);
	});
	form.addEventListener('input', (e) => {
		if (isField(e.target) && e.target.getAttribute('aria-invalid') === 'true') validateField(e.target);
	});

	const say = (msg, kind) => {
		if (!status) return;
		status.textContent = msg;
		status.hidden = false;
		status.classList.remove('is-error', 'is-ok');
		status.classList.add(kind === 'ok' ? 'is-ok' : 'is-error');
	};

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		if (!cfg.restUrl) {
			say('Форма недоступна. Зателефонуйте нам, будь ласка.', 'error');
			return;
		}
		const invalid = [...form.elements].filter(isField).filter((el) => !validateField(el));
		if (invalid.length) {
			invalid[0].focus();
			return;
		}

		const payload = Object.fromEntries(new FormData(form).entries());
		// Fields the endpoint has no param for (the parts form's VIN, category, …)
		// carry data-message-label and ride along at the top of the message.
		const extra = [...form.querySelectorAll('[data-message-label]')]
			.filter((el) => el.value.trim())
			.map((el) => `${el.dataset.messageLabel}: ${el.value.trim()}`);
		if (extra.length) {
			payload.message = [...extra, payload.message].filter(Boolean).join('\n');
		}
		button.disabled = true;
		say('Надсилаємо…', 'ok');

		try {
			const res = await fetch(cfg.restUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': cfg.nonce || '',
				},
				body: JSON.stringify(payload),
			});
			const data = await res.json().catch(() => ({}));
			if (res.ok) {
				form.reset();
				say(data.message || 'Дякуємо! Ми зв’яжемось найближчим часом.', 'ok');
			} else {
				// A 422 carries per-field reasons; show them instead of the generic line.
				const fields = data.data?.fields || {};
				for (const [name, msg] of Object.entries(fields)) {
					const el = form.querySelector(`[name="${name}"]`);
					if (el) markField(el, msg);
				}
				say(Object.values(fields).join(' ') || data.message || 'Не вдалося надіслати. Спробуйте пізніше або зателефонуйте.', 'error');
			}
		} catch {
			say('Помилка мережі. Спробуйте пізніше або зателефонуйте.', 'error');
		} finally {
			button.disabled = false;
		}
	});
}
