/**
 * Stat tiles count up once, when they first scroll into view — the treatment the
 * reference gets from Divi. The finished value stays in the markup and the target
 * is read back out of it, so there is one source of truth: without JS, or with
 * reduced motion, the real number is simply what renders.
 */

const DURATION = 1200;

const easeOut = (t) => 1 - Math.pow(1 - t, 3);

const countUp = (el, from, to, suffix) => {
	const started = performance.now();

	const step = (now) => {
		const t = Math.min((now - started) / DURATION, 1);
		el.textContent = Math.round(from + (to - from) * easeOut(t)) + suffix;
		if (t < 1) {
			requestAnimationFrame(step);
		}
	};

	requestAnimationFrame(step);
};

export function initCounters() {
	const targets = document.querySelectorAll('[data-count-from]');
	if (!targets.length) return;
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	const observer = new IntersectionObserver(
		(entries, obs) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;

				const el = entry.target;
				obs.unobserve(el);

				/* "100%" -> 100 and "%", "275+" -> 275 and "+". */
				const parsed = el.textContent.trim().match(/^(\d+)(.*)$/);
				if (!parsed) return;

				countUp(el, Number(el.dataset.countFrom), Number(parsed[1]), parsed[2]);
			});
		},
		{ threshold: 0.25 }
	);

	targets.forEach((el) => observer.observe(el));
}
