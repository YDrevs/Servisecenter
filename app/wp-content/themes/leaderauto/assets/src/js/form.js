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
		if (!form.reportValidity()) return;

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
				say(data.message || 'Не вдалося надіслати. Спробуйте пізніше або зателефонуйте.', 'error');
			}
		} catch {
			say('Помилка мережі. Спробуйте пізніше або зателефонуйте.', 'error');
		} finally {
			button.disabled = false;
		}
	});
}
