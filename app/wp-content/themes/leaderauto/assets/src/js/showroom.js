/**
 * Dealer showroom. The server renders every model's panel plus a rail of anchor
 * thumbnails, which already works without JS via :target. This upgrades the rail
 * to a real tablist (buttons, aria-selected, roving tabindex, ←/→/Home/End) and
 * reveals the ‹ › arrows. Always starts on the first model; nothing is persisted.
 */

const upgradeThumb = (link, panel) => {
	const tab = document.createElement('button');
	tab.type = 'button';
	tab.className = link.className;
	tab.id = `${panel.id}-tab`;
	tab.setAttribute('role', 'tab');
	tab.setAttribute('aria-controls', panel.id);
	tab.append(...link.childNodes);
	link.replaceWith(tab);
	return tab;
};

const initOne = (root) => {
	const panels = [...root.querySelectorAll('.showroom__panel')];
	const rail = root.querySelector('.showroom__rail');
	if (!panels.length || !rail) return;

	const tabs = [...rail.querySelectorAll('.showroom__thumb')].map((link, i) => upgradeThumb(link, panels[i]));
	rail.setAttribute('role', 'tablist');
	rail.setAttribute('aria-label', 'Моделі BYD');

	panels.forEach((panel, i) => {
		panel.setAttribute('role', 'tabpanel');
		panel.setAttribute('aria-labelledby', tabs[i].id);
	});
	root.querySelectorAll('.showroom__arrow').forEach((arrow) => { arrow.hidden = false; });

	let current = 0;

	const select = (index) => {
		current = (index + panels.length) % panels.length;
		tabs.forEach((tab, i) => {
			const active = i === current;
			tab.setAttribute('aria-selected', String(active));
			tab.tabIndex = active ? 0 : -1;
			panels[i].hidden = !active;
		});
		/* Bring the tab into the scrolling rail (mobile) without moving the page. */
		const r = rail.getBoundingClientRect();
		const t = tabs[current].getBoundingClientRect();
		if (t.left < r.left || t.right > r.right) {
			rail.scrollLeft += t.left - r.left - (r.width - t.width) / 2;
		}
	};

	tabs.forEach((tab, i) => tab.addEventListener('click', () => select(i)));

	rail.addEventListener('keydown', (event) => {
		const next = {
			ArrowRight: current + 1,
			ArrowLeft: current - 1,
			Home: 0,
			End: panels.length - 1,
		}[event.key];
		if (next === undefined) return;
		event.preventDefault();
		select(next);
		tabs[current].focus();
	});

	root.querySelector('.showroom__stage').addEventListener('click', (event) => {
		const arrow = event.target.closest('[data-showroom-step]');
		if (!arrow) return;
		const step = Number(arrow.dataset.showroomStep);
		select(current + step);
		/* The clicked arrow is now inside a hidden panel — keep focus on its twin. */
		panels[current].querySelector(`[data-showroom-step="${step}"]`).focus();
	});

	root.classList.add('is-ready');
	select(0);
};

export function initShowroom() {
	document.querySelectorAll('.showroom').forEach(initOne);
}
