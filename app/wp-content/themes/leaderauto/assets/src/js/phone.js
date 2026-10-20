// Ukrainian or European phone number. Mirrors Rest_Contact::valid_phone() in
// leaderauto-core — keep the two in step.

/** European calling codes (380 is handled separately). No +7 / +375. */
const EUROPE_CODES = new Set([
	'30', '31', '32', '33', '34', '36', '39', '40', '41', '43', '44', '45', '46', '47', '48', '49',
	'350', '351', '352', '353', '354', '355', '356', '357', '358', '359',
	'370', '371', '372', '373', '376', '377', '378',
	'381', '382', '383', '385', '386', '387', '389',
	'420', '421', '423',
]);

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
		if (EUROPE_CODES.has(digits.slice(0, len))) {
			const rest = digits.length - len;
			return rest >= 6 && rest <= 12;
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
