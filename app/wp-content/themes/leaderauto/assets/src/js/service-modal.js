import { serviceMessage } from './form.js';

/**
 * Turns the service links in the "Наші послуги" cards into an on-page dialog.
 * Progressive enhancement: without this the links still go to /contacts/?service=…,
 * so anything that stops the click being handled here (no JS, no <dialog>,
 * middle-click, ctrl-click) leaves a working link behind.
 */
export function initServiceModal() {
	const dialog = document.getElementById('service-modal');
	if (!dialog || typeof dialog.showModal !== 'function') return;

	const form = dialog.querySelector('.contact-form');
	const message = form?.querySelector('[name="message"]');
	const firstField = form?.querySelector('[name="name"]');

	document.addEventListener('click', (e) => {
		const link = e.target.closest('.card__link');
		if (!link) return;
		// Let the browser handle new-tab / new-window clicks as plain links.
		if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;

		e.preventDefault();

		const name = link.dataset.service || link.textContent.trim();
		// Keep whatever the visitor typed; replace only our own prefilled line.
		if (message && (!message.value || message.value.startsWith('Цікавить послуга:'))) {
			message.value = serviceMessage(name);
		}

		dialog.showModal();
		firstField?.focus();
	});

	// Clicking the backdrop — i.e. the dialog element itself, outside its content.
	dialog.addEventListener('click', (e) => {
		if (e.target === dialog) dialog.close();
	});
}
