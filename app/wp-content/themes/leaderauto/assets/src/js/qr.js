/**
 * Loyalty band QR: drawn from the link in data-qr, which the client edits in
 * wp-admin, so a new link never needs a new image. Data modules are one path in
 * the card's black; the three finder eyes are drawn as rounded squares, the
 * look of the PNG this replaced.
 */
import qrcode from 'qrcode-generator';

const SVG = 'http://www.w3.org/2000/svg';

const eye = (x, y) =>
	`<rect x="${x + 0.5}" y="${y + 0.5}" width="6" height="6" rx="1.5" fill="none" stroke="#000"/>` +
	`<rect x="${x + 2}" y="${y + 2}" width="3" height="3" rx="0.6" fill="#000"/>`;

export function initQr() {
	document.querySelectorAll('[data-qr]').forEach((el) => {
		qrcode.stringToBytes = qrcode.stringToBytesFuncs['UTF-8'];
		const qr = qrcode(0, 'M');
		qr.addData(el.dataset.qr);
		qr.make();

		const n = qr.getModuleCount();
		const inEye = (r, c) => (r < 7 || r >= n - 7) && (c < 7 || c >= n - 7) && !(r >= n - 7 && c >= n - 7);

		let d = '';
		for (let r = 0; r < n; r++) {
			for (let c = 0; c < n; c++) {
				if (qr.isDark(r, c) && !inEye(r, c)) d += `M${c} ${r}h1v1h-1z`;
			}
		}

		const svg = document.createElementNS(SVG, 'svg');
		svg.setAttribute('viewBox', `0 0 ${n} ${n}`);
		svg.setAttribute('shape-rendering', 'crispEdges');
		svg.innerHTML = `<path d="${d}" fill="currentColor"/>` + eye(0, 0) + eye(n - 7, 0) + eye(0, n - 7);
		el.replaceChildren(svg);
	});
}
