// Ukrainian or European phone number. Mirrors Rest_Contact::valid_phone() in
// leaderauto-core — keep the two in step.

/**
 * European calling codes (380 is handled separately) → [min, max] digits of the
 * national number after the code, without the leading 0. No +7 / +375.
 */
const EUROPE_CODES = {
	'30': [10, 10], // Greece
	'31': [9, 9], // Netherlands
	'32': [8, 9], // Belgium
	'33': [9, 9], // France
	'34': [9, 9], // Spain
	'36': [8, 9], // Hungary
	'39': [6, 11], // Italy
	'40': [9, 9], // Romania
	'41': [9, 9], // Switzerland
	'43': [7, 13], // Austria
	'44': [9, 10], // United Kingdom
	'45': [8, 8], // Denmark
	'46': [7, 10], // Sweden
	'47': [8, 8], // Norway
	'48': [9, 9], // Poland
	'49': [6, 13], // Germany
	'350': [8, 8], // Gibraltar
	'351': [9, 9], // Portugal
	'352': [6, 11], // Luxembourg
	'353': [7, 9], // Ireland
	'354': [7, 7], // Iceland
	'355': [8, 9], // Albania
	'356': [8, 8], // Malta
	'357': [8, 8], // Cyprus
	'358': [5, 12], // Finland
	'359': [8, 9], // Bulgaria
	'370': [8, 8], // Lithuania
	'371': [8, 8], // Latvia
	'372': [7, 8], // Estonia
	'373': [8, 8], // Moldova
	'376': [6, 9], // Andorra
	'377': [8, 9], // Monaco
	'378': [6, 10], // San Marino
	'381': [7, 10], // Serbia
	'382': [8, 8], // Montenegro
	'383': [8, 9], // Kosovo
	'385': [8, 9], // Croatia
	'386': [8, 8], // Slovenia
	'387': [8, 9], // Bosnia and Herzegovina
	'389': [8, 8], // North Macedonia
	'420': [9, 9], // Czechia
	'421': [9, 9], // Slovakia
	'423': [7, 9], // Liechtenstein
};

export const PHONE_ERROR = 'Вкажіть номер у форматі +380 XX XXX XX XX або європейський номер з кодом країни.';

export function isValidPhone(value) {
	const phone = value.trim();
	if (!/^\+?[\d\s().-]+$/.test(phone)) return false;
	let digits = phone.replace(/\D+/g, '');

	if (phone[0] !== '+' && !digits.startsWith('00')) {
		// No international prefix: only Ukrainian forms are accepted.
		return /^(?:380|0)[1-9]\d{8}$/.test(digits);
	}

	if (phone[0] !== '+') digits = digits.slice(2);
	if (digits.startsWith('380')) return /^380[1-9]\d{8}$/.test(digits);
	for (const len of [3, 2]) {
		const range = EUROPE_CODES[digits.slice(0, len)];
		if (range) {
			const rest = digits.length - len;
			return rest >= range[0] && rest <= range[1] && digits[len] !== '0';
		}
	}
	return false;
}

/** Drop anything a phone number can't contain; "+" only as the first character. */
export function cleanPhoneInput(value) {
	return value.replace(/[^\d\s()+.-]/g, '').replace(/(?!^)\+/g, '');
}

/** Filter keystrokes as the visitor types. form.js shows the error. */
export function bindPhoneField(input) {
	input.addEventListener('input', () => {
		const clean = cleanPhoneInput(input.value);
		if (clean !== input.value) input.value = clean;
		input.setCustomValidity('');
	});
}

export function checkPhoneField(input) {
	const ok = !input.value.trim() || isValidPhone(input.value);
	input.setCustomValidity(ok ? '' : PHONE_ERROR);
	return ok;
}
