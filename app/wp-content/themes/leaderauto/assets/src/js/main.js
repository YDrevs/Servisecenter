import '../css/main.css';
import { initMenu } from './menu.js';
import { initContactForm } from './form.js';
import { initCounters } from './counters.js';
import { initShowroom } from './showroom.js';
import { initReels } from './reel.js';
import { initServiceModal } from './service-modal.js';
import { initParts } from './parts.js';
import { initVoiceAssistant } from './voice.js';
import { initQr } from './qr.js';

const ready = (fn) =>
	document.readyState !== 'loading'
		? fn()
		: document.addEventListener('DOMContentLoaded', fn);

ready(() => {
	initMenu();
	initContactForm();
	initCounters();
	initShowroom();
	initReels();
	initServiceModal();
	initParts();
	initVoiceAssistant();
	initQr();
});
