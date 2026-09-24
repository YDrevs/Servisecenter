/**
 * Parts page: category tiles, model chips and "ask about" buttons each drop their
 * value into the request form and scroll to it.
 */
export function initParts() {
	const form = document.getElementById('leaderauto-parts-form');
	if (!form) return;

	const field = (suffix) => document.getElementById(`leaderauto-parts-form-${suffix}`);

	const fill = (el, value) => {
		if (!el) return;
		el.value = value;
		el.classList.add('is-prefilled');
		setTimeout(() => el.classList.remove('is-prefilled'), 1200);
	};

	const goToForm = (focusEl) => {
		document.getElementById('request')?.scrollIntoView({ behavior: 'smooth' });
		focusEl?.focus({ preventScroll: true });
	};

	document.querySelectorAll('[data-parts-category]').forEach((btn) => {
		btn.addEventListener('click', () => {
			fill(field('category'), btn.dataset.partsCategory);
			goToForm(field('car'));
		});
	});

	document.querySelectorAll('[data-parts-model]').forEach((btn) => {
		btn.addEventListener('click', () => {
			fill(field('car'), btn.dataset.partsModel);
			goToForm(field('vin'));
		});
	});

	document.querySelectorAll('[data-parts-part]').forEach((btn) => {
		btn.addEventListener('click', () => {
			fill(field('message'), btn.dataset.partsPart);
			goToForm(field('car'));
		});
	});

	document.querySelectorAll('[data-parts-vin]').forEach((link) => {
		link.addEventListener('click', (e) => {
			e.preventDefault();
			goToForm(field('vin'));
		});
	});
}
