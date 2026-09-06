export function initContactForm() {
	const form = document.getElementById('leaderauto-contact-form');
	if (!form) return;

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
