import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
	const toggle = document.querySelector('[data-menu-toggle]');
	const menu = document.getElementById('mobile-navigation');

	if (!toggle || !menu) {
		return;
	}

	const closeMenu = () => {
		menu.classList.add('hidden');
		toggle.setAttribute('aria-expanded', 'false');
		toggle.setAttribute('aria-label', 'Open menu');
	};

	toggle.addEventListener('click', () => {
		const isOpen = !menu.classList.toggle('hidden');
		toggle.setAttribute('aria-expanded', String(isOpen));
		toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
	});

	menu.querySelectorAll('[data-menu-link]').forEach((link) => {
		link.addEventListener('click', closeMenu);
	});
});
