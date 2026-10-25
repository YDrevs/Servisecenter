/**
 * About page «Наші задоволені клієнти» strip. The track already scrolls by swipe,
 * trackpad and scrollbar; this reveals the ‹ › arrows, which step it by most of
 * its visible width (the CSS makes that smooth), and hides whichever arrow has
 * nothing left to scroll to. Starting a clip pauses any other one playing.
 */

const initOne = (root) => {
	const track = root.querySelector('.reel__track');
	const arrows = [...root.querySelectorAll('[data-reel-step]')];
	if (!track || !arrows.length) return;

	const update = () => {
		const max = track.scrollWidth - track.clientWidth - 1;
		arrows.forEach((arrow) => {
			arrow.hidden = max <= 0;
			arrow.disabled = Number(arrow.dataset.reelStep) < 0 ? track.scrollLeft <= 0 : track.scrollLeft >= max;
		});
	};

	arrows.forEach((arrow) => {
		arrow.addEventListener('click', () => {
			track.scrollBy({ left: Number(arrow.dataset.reelStep) * track.clientWidth * 0.8 });
		});
	});
	track.addEventListener('scroll', update, { passive: true });
	window.addEventListener('resize', update);
	/* Video widths are only known once metadata loads. */
	track.querySelectorAll('img, video').forEach((el) => {
		el.addEventListener(el.tagName === 'VIDEO' ? 'loadedmetadata' : 'load', update);
	});

	track.addEventListener('play', (event) => {
		track.querySelectorAll('video').forEach((video) => {
			if (video !== event.target) video.pause();
		});
	}, true);

	update();
};

export function initReels() {
	document.querySelectorAll('.reel').forEach(initOne);
}
