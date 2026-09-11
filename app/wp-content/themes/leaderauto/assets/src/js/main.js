import '../css/main.css';
import { initMenu } from './menu.js';
import { initContactForm } from './form.js';
import { initCounters } from './counters.js';

const ready = (fn) =>
	document.readyState !== 'loading'
		? fn()
		: document.addEventListener('DOMContentLoaded', fn);

ready(() => {
	initMenu();
	initContactForm();
	initCounters();
});
