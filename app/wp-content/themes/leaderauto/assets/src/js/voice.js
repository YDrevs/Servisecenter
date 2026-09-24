// Browser call to the Vapi voice assistant (Contacts page). The SDK pulls in a
// WebRTC client, so it is imported only on the first click, not on page load.

const LABEL_IDLE = 'Поговорити з асистентом';
const LABEL_ACTIVE = 'Завершити розмову';

export function initVoiceAssistant() {
	const box = document.querySelector('[data-vapi-key]');
	if (!box) return;

	const button = box.querySelector('[data-voice-toggle]');
	const label = box.querySelector('[data-voice-label]');
	const status = box.querySelector('[data-voice-status]');
	let vapi = null;
	let active = false;

	const say = (msg) => { status.textContent = msg; };
	const setActive = (on) => {
		active = on;
		button.disabled = false;
		box.classList.toggle('is-active', on);
		label.textContent = on ? LABEL_ACTIVE : LABEL_IDLE;
	};

	async function load() {
		const mod = await import('@vapi-ai/web');
		// CommonJS package — the class sits one level deeper depending on interop.
		const Vapi = mod.default?.default ?? mod.default;
		const client = new Vapi(box.dataset.vapiKey);

		client.on('call-start', () => { setActive(true); say('На зв’язку. Говоріть.'); });
		client.on('call-end', () => { setActive(false); box.classList.remove('is-speaking'); say('Розмову завершено.'); });
		client.on('speech-start', () => box.classList.add('is-speaking'));
		client.on('speech-end', () => box.classList.remove('is-speaking'));
		client.on('error', fail);
		return client;
	}

	const fail = (err) => {
		console.error('[vapi]', err);
		setActive(false);
		say('Не вдалося з’єднатися. Перевірте дозвіл на мікрофон або зателефонуйте нам.');
	};

	button.addEventListener('click', async () => {
		if (active) {
			button.disabled = true;
			vapi.stop();
			return;
		}

		button.disabled = true;
		say('З’єднуємо…');
		try {
			vapi ??= await load();
			// start() resolves to null instead of throwing when the call is refused.
			if (!(await vapi.start(box.dataset.vapiAssistant))) fail('call not started');
		} catch (err) {
			fail(err);
		}
	});
}
