import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
	const toggle = document.querySelector('[data-menu-toggle]');
	const menu = document.getElementById('mobile-navigation');

	const closeMenu = () => {
		if (menu && toggle) {
			menu.classList.add('hidden');
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-label', 'Open menu');
		}
	};

	if (toggle && menu) {
		toggle.addEventListener('click', () => {
			const isOpen = !menu.classList.toggle('hidden');
			toggle.setAttribute('aria-expanded', String(isOpen));
			toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
		});

		menu.querySelectorAll('[data-menu-link]').forEach((link) => {
			link.addEventListener('click', closeMenu);
		});
	}

	const packageDialog = document.getElementById('catering-package-dialog');
	const packageTitle = document.getElementById('catering-package-title');
	const packageDescription = document.getElementById('catering-package-description');
	const packagePrice = document.getElementById('catering-package-price');
	const bookPackageButton = document.querySelector('[data-book-catering-package]');

	if (packageDialog instanceof HTMLDialogElement && packageTitle && packageDescription && packagePrice && bookPackageButton) {
		let activePackageCard = null;

		const positionPackageDialog = () => {
			if (!packageDialog.open || !activePackageCard) {
				return;
			}

			const cardBounds = activePackageCard.getBoundingClientRect();
			const dialogWidth = packageDialog.offsetWidth;
			const left = Math.max(16, Math.min(
				cardBounds.left + (cardBounds.width - dialogWidth) / 2,
				window.innerWidth - dialogWidth - 16,
			));
			const top = cardBounds.bottom + 12;

			packageDialog.style.left = `${left}px`;
			packageDialog.style.top = `${top}px`;
		};

		document.querySelectorAll('[data-catering-package]').forEach((card) => {
			card.addEventListener('click', () => {
				activePackageCard = card;
				packageTitle.textContent = card.dataset.packageName ?? '';
				packageDescription.textContent = card.dataset.packageDescription ?? '';

				const price = card.dataset.packagePrice;
				packagePrice.textContent = price ? `Starting from ₱${price}` : '';
				packagePrice.classList.toggle('hidden', !price);

				packageDialog.showModal();
				const bounds = card.getBoundingClientRect();
				const requiredScroll = bounds.bottom + packageDialog.offsetHeight + 28 - window.innerHeight;
				if (requiredScroll > 0) {
					window.scrollBy({ top: requiredScroll, behavior: 'smooth' });
				}
				positionPackageDialog();
			});
		});

		window.addEventListener('scroll', positionPackageDialog, { passive: true });
		window.addEventListener('resize', positionPackageDialog);

		packageDialog.querySelector('[data-close-catering-package]')?.addEventListener('click', () => {
			packageDialog.close();
		});

		packageDialog.addEventListener('click', (event) => {
			if (event.target === packageDialog) {
				packageDialog.close();
			}
		});

		bookPackageButton.addEventListener('click', () => {
			const packageSelect = document.getElementById('catering-package');
			const packageOption = packageSelect instanceof HTMLSelectElement
				? Array.from(packageSelect.options).find((option) => option.value === packageTitle.textContent)
				: null;

			if (packageSelect instanceof HTMLSelectElement && packageOption) {
				packageSelect.value = packageOption.value;
			}

			packageDialog.close();
			activePackageCard = null;
			document.getElementById('catering-inquiry')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		packageDialog.addEventListener('close', () => {
			activePackageCard = null;
		});
	}
});
