import '../css/main.css';
import { initMenu } from './menu.js';
import { initContactForm } from './form.js';
import { initCounters } from './counters.js';
import { initShowroom } from './showroom.js';
import { initServiceModal } from './service-modal.js';
import { initParts } from './parts.js';
import { initVoiceAssistant } from './voice.js';

const ready = (fn) =>
	document.readyState !== 'loading'
		? fn()
		: document.addEventListener('DOMContentLoaded', fn);

ready(() => {
	initMenu();
	initContactForm();
	initCounters();
	initShowroom();
	initServiceModal();
	initParts();
	initVoiceAssistant();
});
