import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
	const orderingStatusUrl = document.body.dataset.orderingStatusUrl;
	const orderingStatusLabel = document.getElementById('ordering-status-label');
	const orderingStatusMessage = document.getElementById('ordering-status-message');
	let orderingStatusTimer;
	let orderingStatusRequestPending = false;

	const applyOrderingAvailability = (status) => {
		const isOpen = status.open === true;
		document.body.dataset.orderingOpen = String(isOpen);

		if (orderingStatusLabel) {
			orderingStatusLabel.textContent = status.label;
			orderingStatusLabel.classList.toggle('text-[#FBAB18]', isOpen);
			orderingStatusLabel.classList.toggle('text-[#FFC247]', !isOpen);
		}

		if (orderingStatusMessage) {
			orderingStatusMessage.textContent = status.message ?? '';
			orderingStatusMessage.classList.toggle('hidden', isOpen);
		}

		document.querySelectorAll('form[data-ordering-action="add-to-cart"] button[type="submit"], [data-ordering-action="place-order"]').forEach((button) => {
			button.disabled = !isOpen;
			button.textContent = isOpen ? button.dataset.orderingOpenLabel : 'Online Ordering Closed';
		});

		document.querySelectorAll('[data-ordering-action="checkout-link"]').forEach((link) => {
			if (isOpen) {
				link.href = link.dataset.orderingOpenHref;
				link.removeAttribute('aria-disabled');
				link.removeAttribute('tabindex');
				link.textContent = link.dataset.orderingOpenLabel;
				link.classList.remove('pointer-events-none', 'cursor-not-allowed', 'bg-[#333333]', 'text-[#B8B8B8]');
			} else {
				link.removeAttribute('href');
				link.setAttribute('aria-disabled', 'true');
				link.tabIndex = -1;
				link.textContent = 'Online Ordering Closed';
				link.classList.add('pointer-events-none', 'cursor-not-allowed', 'bg-[#333333]', 'text-[#B8B8B8]');
			}
		});

		document.dispatchEvent(new CustomEvent('mixeat:ordering-status', { detail: status }));
	};

	const refreshOrderingAvailability = async () => {
		if (!orderingStatusUrl || orderingStatusRequestPending) {
			return;
		}

		orderingStatusRequestPending = true;
		window.clearTimeout(orderingStatusTimer);

		try {
			const response = await fetch(orderingStatusUrl, {
				headers: { Accept: 'application/json' },
				credentials: 'same-origin',
				cache: 'no-store',
			});
			if (!response.ok) {
				throw new Error(`Ordering status request failed (${response.status})`);
			}

			const status = await response.json();
			if (typeof status.open !== 'boolean' || typeof status.label !== 'string') {
				throw new Error('Ordering status response was invalid.');
			}
			applyOrderingAvailability(status);
			orderingStatusTimer = window.setTimeout(
				refreshOrderingAvailability,
				Math.max(0, Number(status.next_transition_in_ms) || 0) + 250,
			);
		} catch (error) {
			console.error('Unable to refresh online ordering status.', error);
			orderingStatusTimer = window.setTimeout(refreshOrderingAvailability, 60000);
		} finally {
			orderingStatusRequestPending = false;
		}
	};

	if (orderingStatusUrl) {
		refreshOrderingAvailability();
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				refreshOrderingAvailability();
			}
		});
	}

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

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				closeMenu();
			}
		});

		document.addEventListener('click', (e) => {
			if (!menu.contains(e.target) && !toggle.contains(e.target)) {
				closeMenu();
			}
		});
	}

	const promotionCarousel = document.querySelector('[data-home-promotion-carousel]');
	if (promotionCarousel) {
		const slides = Array.from(promotionCarousel.querySelectorAll('[data-promotion-slide]'));
		const dots = Array.from(promotionCarousel.querySelectorAll('[data-promotion-dot]'));

		if (slides.length > 1) {
			let activeIndex = 0;
			let rotationTimer;
			const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

			const showSlide = (index) => {
				activeIndex = (index + slides.length) % slides.length;
				slides.forEach((slide, slideIndex) => {
					const isActive = slideIndex === activeIndex;
					slide.classList.toggle('hidden', !isActive);
					slide.setAttribute('aria-hidden', String(!isActive));
				});
				dots.forEach((dot, dotIndex) => {
					const isActive = dotIndex === activeIndex;
					dot.setAttribute('aria-current', String(isActive));
					dot.classList.toggle('bg-white', isActive);
					dot.classList.toggle('bg-white/50', !isActive);
				});
			};

			const stopRotation = () => window.clearInterval(rotationTimer);
			const startRotation = () => {
				stopRotation();
				if (
					!reducedMotion
					&& !document.hidden
					&& !promotionCarousel.matches(':hover')
					&& !promotionCarousel.contains(document.activeElement)
				) {
					rotationTimer = window.setInterval(() => showSlide(activeIndex + 1), 5000);
				}
			};

			promotionCarousel.querySelector('[data-promotion-previous]')?.addEventListener('click', () => {
				showSlide(activeIndex - 1);
				startRotation();
			});
			promotionCarousel.querySelector('[data-promotion-next]')?.addEventListener('click', () => {
				showSlide(activeIndex + 1);
				startRotation();
			});
			dots.forEach((dot, index) => dot.addEventListener('click', () => {
				showSlide(index);
				startRotation();
			}));

			promotionCarousel.addEventListener('mouseenter', stopRotation);
			promotionCarousel.addEventListener('mouseleave', startRotation);
			promotionCarousel.addEventListener('focusin', stopRotation);
			promotionCarousel.addEventListener('focusout', (event) => {
				if (!promotionCarousel.contains(event.relatedTarget)) {
					startRotation();
				}
			});
			document.addEventListener('visibilitychange', startRotation);
			startRotation();
		}
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

		document.querySelectorAll('[data-catering-package]:not([data-catering-tray-category]):not([data-catering-flow]):not([data-catering-service])').forEach((card) => {
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
			const cateringService = document.getElementById('catering-service');
			if (cateringService instanceof HTMLSelectElement && activePackageCard?.dataset.cateringService) {
				cateringService.value = activePackageCard.dataset.cateringService;
				cateringService.dispatchEvent(new Event('change', { bubbles: true }));
			}

			packageDialog.close();
			activePackageCard = null;
			document.getElementById('catering-inquiry')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		packageDialog.addEventListener('close', () => {
			activePackageCard = null;
		});
	}

	const updateCateringPaymentSummary = (total) => {
		const summary = document.querySelector('[data-catering-payment-summary]');
		if (!summary) {
			return;
		}
		const totalCents = Math.max(0, Math.round(Number(total) * 100));
		const depositCents = Math.floor((totalCents * 30 + 50) / 100);
		const format = (cents) => `₱${(cents / 100).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
		summary.querySelector('[data-payment-total]')?.replaceChildren(document.createTextNode(format(totalCents)));
		summary.querySelector('[data-payment-deposit]')?.replaceChildren(document.createTextNode(format(depositCents)));
		summary.querySelector('[data-payment-balance]')?.replaceChildren(document.createTextNode(format(totalCents - depositCents)));
	};
	updateCateringPaymentSummary(0);

	const cateringFlow = document.querySelector('[data-catering-flow-panel]');
	const cateringInquiry = document.querySelector('[data-catering-inquiry]');
	const cateringForm = cateringInquiry?.querySelector('form');
	const cateringService = document.getElementById('catering-service');
	const customBuilder = document.querySelector('[data-custom-catering-builder]');
	const customPackageButton = document.querySelector('[data-custom-catering-package]');

	if (customBuilder && cateringInquiry && cateringForm && cateringService) {
		const paymentSummary = cateringForm.querySelector('[data-catering-payment-summary]');
		const submitButton = cateringForm.querySelector('[data-catering-submit]');
		const customCategories = ['meat', 'vegetables', 'soup', 'pasta', 'dessert', 'rice', 'drinks'];
		const customCategoryLabels = {
			meat: 'Meat Dishes',
			vegetables: 'Vegetables',
			soup: 'Soup',
			pasta: 'Pasta / Noodles',
			dessert: 'Desserts',
			rice: 'Rice',
			drinks: 'Drinks',
		};
		const refreshCustomCategory = (category) => {
			const selected = Array.from(customBuilder.querySelectorAll(`[data-custom-dish="${category}"]:checked`));
			customBuilder.querySelector(`[data-custom-count-label="${category}"]`)?.replaceChildren(document.createTextNode(String(selected.length)));
		};
		const refreshCustomSummary = () => {
			const summary = customBuilder.querySelector('[data-custom-summary-content]');
			if (!summary) return;
			const selectedGroups = customCategories.map((category) => ({
				category,
				dishes: Array.from(customBuilder.querySelectorAll(`[data-custom-dish="${category}"]:checked`)).map((input) => input.value),
			})).filter(({ dishes }) => dishes.length > 0);
			summary.replaceChildren();
			if (!selectedGroups.length) {
				summary.append(document.createElement('p')).textContent = 'No dishes selected yet.';
				return;
			}
			selectedGroups.forEach(({ category, dishes }) => {
				const group = document.createElement('p');
				const label = document.createElement('strong');
				label.className = 'text-white';
				label.textContent = `${customCategoryLabels[category]} — ${dishes.length} selected`;
				group.append(label, document.createTextNode(`: ${dishes.join(', ')}`));
				summary.append(group);
			});
		};
		const refreshCustom = () => {
			customCategories.forEach(refreshCustomCategory);
			refreshCustomSummary();
		};
		const showCustomFlow = () => {
			cateringService.value = 'Custom Catering';
			cateringFlow?.classList.add('hidden');
			customBuilder.classList.remove('hidden');
			cateringInquiry.classList.add('hidden');
			refreshCustom();
			customBuilder.scrollIntoView({ behavior: 'smooth', block: 'start' });
		};
		customPackageButton?.addEventListener('click', showCustomFlow);
		customBuilder.querySelectorAll('[data-custom-dish]').forEach((input) => {
			input.addEventListener('change', () => {
				refreshCustomCategory(input.dataset.customDish);
				refreshCustomSummary();
			});
		});
		customBuilder.querySelector('[data-custom-continue]')?.addEventListener('click', () => {
			refreshCustom();
			const total = customBuilder.querySelectorAll('[data-custom-dish]:checked').length;
			const error = customBuilder.querySelector('[data-custom-error]');
			if (total < 1) {
				if (error) error.textContent = 'Select at least one dish before continuing.';
				return;
			}
			if (error) error.textContent = '';
			cateringInquiry.classList.remove('hidden');
			paymentSummary?.classList.add('hidden');
			paymentSummary?.querySelectorAll('input').forEach((input) => input.removeAttribute('required'));
			if (submitButton) submitButton.textContent = 'Request Quotation';
			cateringInquiry.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
		if (cateringService.value === 'Custom Catering') showCustomFlow();
	}

	if (cateringFlow && cateringInquiry && cateringForm && cateringService) {
		const stepPanels = Array.from(cateringFlow.querySelectorAll('[data-catering-step]'));
		const stepIndicators = Array.from(cateringFlow.querySelectorAll('[data-catering-step-indicator]'));
		const packageButtons = Array.from(cateringFlow.querySelectorAll('[data-select-catering-package]'));
		const selectedPackageField = cateringForm.querySelector('[data-selected-catering-package]');
		const packagePreference = document.getElementById('catering-package');
		const packageContinue = cateringFlow.querySelector('[data-package-continue]');
		const foodContinue = cateringFlow.querySelector('[data-food-continue]');
		const meatOptions = Array.from(cateringFlow.querySelectorAll('[data-meat-option]'));
		const singleOptions = Array.from(cateringFlow.querySelectorAll('[data-single-category]'));
		const packageError = cateringFlow.querySelector('[data-package-error]');
		const foodError = cateringFlow.querySelector('[data-food-error]');
		const summary = cateringForm.querySelector('[data-catering-summary]');
		const formBack = cateringForm.querySelector('[data-catering-form-back]');
		const guestCount = document.getElementById('catering-guests');
		const requiredCategories = ['soup', 'vegetables', 'pasta', 'dessert', 'rice', 'drinks'];
		let selectedPackage = selectedPackageField?.value ?? '';
		let activeStep = 0;

		const chosenPackage = () => packageButtons.find((button) => button.dataset.selectCateringPackage === selectedPackage);
		const meatAllowance = () => Number(chosenPackage()?.dataset.meatCount ?? 0);

		const refreshFoodControls = () => {
			const seenMeat = new Set();
			meatOptions.forEach((option) => {
				if (option.checked && seenMeat.has(option.value)) {
					option.checked = false;
				} else if (option.checked) {
					seenMeat.add(option.value);
				}
			});
			const checkedMeat = meatOptions.filter((option) => option.checked);
			const meatLimit = meatAllowance();
			const counter = cateringFlow.querySelector('[data-meat-counter]');
			if (counter) {
				counter.textContent = `Meat dishes: ${checkedMeat.length}/${meatLimit} selected`;
			}

			meatOptions.forEach((option) => {
				const duplicateSelected = checkedMeat.some((selected) => selected !== option && selected.value === option.value);
				option.disabled = duplicateSelected || (!option.checked && checkedMeat.length >= meatLimit);
			});

			requiredCategories.forEach((category) => {
				const selected = singleOptions.some((option) => option.dataset.singleCategory === category && option.checked);
				const categoryCounter = cateringFlow.querySelector(`[data-category-counter="${category}"]`);
				if (categoryCounter) {
					categoryCounter.textContent = selected ? '1/1 selected' : '0/1 selected';
				}
			});

			if (foodContinue) {
				foodContinue.disabled = !selectedPackage
					|| checkedMeat.length !== meatLimit
					|| requiredCategories.some((category) => !singleOptions.some((option) => option.dataset.singleCategory === category && option.checked));
			}
			if (foodError && !foodContinue?.disabled) {
				foodError.textContent = '';
			} else if (foodError && selectedPackage) {
				foodError.textContent = `Choose exactly ${meatLimit} meat dishes and one dish from each other category to continue.`;
			}
		};

		const updateSummary = () => {
			if (!summary) {
				return;
			}

			const selected = chosenPackage();
			const price = Number(selected?.dataset.packagePrice ?? 0);
			const packageName = selected?.dataset.packageLabel ?? '';
			const guestTotal = Math.max(0, Number(guestCount?.value ?? 0)) * price;
			updateCateringPaymentSummary(guestTotal);
			const summaryPackage = summary.querySelector('[data-summary-package]');
			const estimatedTotal = summary.querySelector('[data-estimated-total]');
			const selectionList = summary.querySelector('[data-summary-selections]');

			if (summaryPackage) {
				summaryPackage.textContent = `${packageName} · ₱${price.toLocaleString('en-PH')} per pax · ${guestCount?.value || 0} guests`;
			}
			if (estimatedTotal) {
				estimatedTotal.textContent = `Estimated total: ₱${guestTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
			}
			if (selectionList) {
				selectionList.replaceChildren();
				const selections = [
					['Meat dishes', meatOptions.filter((option) => option.checked).map((option) => option.value).join(', ')],
					...requiredCategories.map((category) => {
						const option = singleOptions.find((item) => item.dataset.singleCategory === category && item.checked);
						const counter = cateringFlow.querySelector(`[data-category-counter="${category}"]`);
						const label = counter?.closest('legend')?.childNodes[0]?.textContent?.trim() ?? category;
						return [label, option?.value ?? 'Not selected'];
					}),
				];

				selections.forEach(([label, value]) => {
					const item = document.createElement('li');
					item.textContent = `${label}: ${value || 'Not selected'}`;
					selectionList.append(item);
				});
			}
		};

		const setStep = (step) => {
			activeStep = step;
			cateringFlow.classList.remove('hidden');
			stepPanels.forEach((panel) => panel.classList.toggle('hidden', Number(panel.dataset.cateringStep) !== step || step === 3));
			stepIndicators.forEach((indicator) => {
				const isActive = Number(indicator.dataset.cateringStepIndicator) === step;
				indicator.classList.toggle('border-[#FBAB18]', isActive);
				indicator.classList.toggle('text-white', isActive);
				indicator.setAttribute('aria-current', isActive ? 'step' : 'false');
			});
			cateringInquiry.classList.toggle('hidden', step !== 3);
			summary?.classList.toggle('hidden', step !== 3);
			formBack?.classList.toggle('hidden', step !== 3);
			if (step === 3) {
				updateSummary();
				cateringInquiry.scrollIntoView({ behavior: 'smooth', block: 'start' });
			} else {
				cateringFlow.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		};

		const openCateringFlow = () => {
			cateringService.value = 'Full-Service Catering';
			setStep(1);
		};

		document.querySelector('[data-catering-flow][data-package-name]')?.addEventListener('click', openCateringFlow);

		packageButtons.forEach((button) => {
			button.addEventListener('click', () => {
				selectedPackage = button.dataset.selectCateringPackage ?? '';
				if (selectedPackageField) {
					selectedPackageField.value = selectedPackage;
				}
				packageButtons.forEach((packageButton) => {
					const selected = packageButton === button;
					packageButton.setAttribute('aria-pressed', String(selected));
					packageButton.classList.toggle('border-[#FBAB18]', selected);
				});
				if (packagePreference) {
					packagePreference.value = selectedPackage === 'option_550'
						? 'Option 1 (₱550 per pax)'
						: 'Option 2 (₱650 per pax)';
				}
				if (packageContinue) {
					packageContinue.disabled = false;
				}
				if (packageError) {
					packageError.textContent = '';
				}
				refreshFoodControls();
			});
		});

		packageContinue?.addEventListener('click', () => {
			if (!selectedPackage) {
				if (packageError) {
					packageError.textContent = 'Select one package to continue.';
				}
				return;
			}
			refreshFoodControls();
			setStep(2);
		});

		meatOptions.forEach((option) => option.addEventListener('change', () => {
			const count = meatOptions.filter((item) => item.checked).length;
			if (count > meatAllowance()) {
				option.checked = false;
				if (foodError) {
					foodError.textContent = `Choose no more than ${meatAllowance()} meat dishes.`;
				}
			} else if (foodError) {
				foodError.textContent = '';
			}
			refreshFoodControls();
		}));
		singleOptions.forEach((option) => option.addEventListener('change', refreshFoodControls));

		foodContinue?.addEventListener('click', () => {
			refreshFoodControls();
			if (foodContinue.disabled) {
				if (foodError) {
					foodError.textContent = 'Choose the required number of meat dishes and exactly one dish from each other category.';
				}
				return;
			}
			cateringService.value = 'Full-Service Catering';
			if (packagePreference && selectedPackage) {
				packagePreference.value = selectedPackage === 'option_550'
					? 'Option 1 (₱550 per pax)'
					: 'Option 2 (₱650 per pax)';
			}
			setStep(3);
		});

		cateringFlow.querySelectorAll('[data-catering-back="1"]').forEach((button) => button.addEventListener('click', () => setStep(1)));
		formBack?.querySelector('[data-catering-back="2"]')?.addEventListener('click', () => setStep(2));
		guestCount?.addEventListener('input', updateSummary);

		cateringService.addEventListener('change', () => {
			if (cateringService.value === 'Full-Service Catering' && activeStep === 0) {
				openCateringFlow();
			} else if (cateringService.value !== 'Full-Service Catering') {
				activeStep = 0;
				cateringFlow.classList.add('hidden');
				cateringInquiry.classList.toggle('hidden', cateringService.value !== 'Packed Meals');
				summary?.classList.add('hidden');
				formBack?.classList.add('hidden');
			}
		});

		packageButtons.forEach((button) => {
			const isSelected = button.dataset.selectCateringPackage === selectedPackage;
			button.setAttribute('aria-pressed', String(isSelected));
			button.classList.toggle('border-[#FBAB18]', isSelected);
		});
		if (selectedPackage) {
			if (packageContinue) {
				packageContinue.disabled = false;
			}
			refreshFoodControls();
			if (cateringService.value === 'Full-Service Catering') {
				setStep(3);
			}
		} else {
			refreshFoodControls();
			if (cateringService.value === 'Full-Service Catering') {
				setStep(1);
			}
		}
	}

	const packedMealsPanel = document.querySelector('[data-packed-meals-panel]');
	const packedCateringInquiry = document.querySelector('[data-catering-inquiry]');
	const packedCateringForm = packedCateringInquiry?.querySelector('form');
	const packedCateringService = document.getElementById('catering-service');
	const partyTraysPanel = document.querySelector('[data-party-trays-panel]');

	if (packedMealsPanel && packedCateringInquiry && packedCateringForm && packedCateringService) {
		const categoryCards = Array.from(document.querySelectorAll('[data-catering-category-card]'));
		const packageCards = Array.from(packedMealsPanel.querySelectorAll('[data-packed-meal-card]'));
		const packageButtons = Array.from(packedMealsPanel.querySelectorAll('[data-packed-meal-select]'));
		const packageField = packedMealsPanel.querySelector('[data-packed-meal-package]');
		const sideOptions = Array.from(packedMealsPanel.querySelectorAll('[data-packed-meal-side]'));
		const selectedPanel = packedMealsPanel.querySelector('[data-packed-meal-selection]');
		const continueButton = packedMealsPanel.querySelector('[data-packed-meal-continue]');
		const errorMessage = packedMealsPanel.querySelector('[data-packed-meal-error]');
		const guestCount = document.getElementById('catering-guests');
		const countLabel = document.querySelector('[data-packed-meal-count-label]');
		const inquirySummary = packedCateringForm.querySelector('[data-packed-meal-inquiry-summary]');
		const formBack = packedCateringForm.querySelector('[data-packed-meal-form-back]');
		const trayCategoryButtons = Array.from(partyTraysPanel?.querySelectorAll('[data-tray-category]') ?? []);
		let selectedCode = packageField instanceof HTMLInputElement ? packageField.value : '';

		const selectedCard = () => packageCards.find((card) => card.dataset.packedMealCard === selectedCode);
		const selectedSide = () => sideOptions.find((option) => option.checked)?.value ?? '';
		const mealPrice = () => Number(selectedCard()?.dataset.packedMealPrice ?? 0);

		const updatePackedSummary = () => {
			const card = selectedCard();
			const side = selectedSide();
			const quantity = Math.max(0, Number(guestCount?.value ?? 0));
			const price = mealPrice();
			const total = quantity * price;
			const details = inquirySummary?.querySelector('[data-packed-meal-inquiry-details]');
			const sideLine = inquirySummary?.querySelector('[data-packed-meal-inquiry-side]');
			const totalLine = inquirySummary?.querySelector('[data-packed-meal-estimated-total]');

			if (details && card) {
				details.textContent = `Package ${selectedCode} · ${card.dataset.packedMealMainDish} · ${quantity} meal${quantity === 1 ? '' : 's'} at ₱${price.toLocaleString('en-PH')} each`;
			}
			if (sideLine) {
				sideLine.textContent = `Side: ${side || 'Not selected'}`;
			}
			if (totalLine) {
				totalLine.textContent = `Estimated total: ₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
			}
			updateCateringPaymentSummary(total);
		};

		const refreshPackedSelection = () => {
			const card = selectedCard();
			const hasPackage = Boolean(card);
			const hasSide = Boolean(selectedSide());

			packageCards.forEach((packageCard) => {
				const selected = packageCard === card;
				packageCard.classList.toggle('border-[#FBAB18]', selected);
				packageCard.classList.toggle('bg-[#FBAB18]/5', selected);
				packageCard.querySelector('[data-packed-meal-select]')?.setAttribute('aria-pressed', String(selected));
			});
			sideOptions.forEach((option) => {
				option.disabled = !hasPackage;
			});
			selectedPanel?.classList.toggle('hidden', !hasPackage);
			const title = selectedPanel?.querySelector('[data-packed-meal-selected-title]');
			const inclusions = selectedPanel?.querySelector('[data-packed-meal-selected-inclusions]');
			if (title && card) {
				title.textContent = `Package ${selectedCode} — ${card.dataset.packedMealMainDish}`;
			}
			if (inclusions && card) {
				inclusions.textContent = `${card.dataset.packedMealMainDish}, ${selectedSide() || 'Bam-i or Vegetables'}, ${card.dataset.packedMealFixedInclusions} · ₱${mealPrice().toLocaleString('en-PH')} per meal`;
			}
			if (continueButton) {
				continueButton.disabled = !hasPackage || !hasSide;
			}
			if (errorMessage && hasPackage && hasSide) {
				errorMessage.textContent = '';
			} else if (errorMessage && !errorMessage.textContent.trim()) {
				errorMessage.textContent = 'Choose a package and side to continue.';
			}
			updatePackedSummary();
		};

		const setActiveCategory = (category) => {
			categoryCards.forEach((card) => {
				const selected = card.dataset.cateringCategory === category;
				card.setAttribute('aria-pressed', String(selected));
				card.classList.toggle('border-[#FBAB18]', selected);
				card.classList.toggle('bg-[#FBAB18]/10', selected);
			});
		};

		const openPackedMeals = () => {
			packedCateringService.value = 'Packed Meals';
			packedMealsPanel.classList.remove('hidden');
			packedCateringInquiry.classList.add('hidden');
			document.querySelector('[data-catering-flow-panel]')?.classList.add('hidden');
			partyTraysPanel?.classList.add('hidden');
			packedCateringForm.querySelector('[data-party-tray-inquiry-summary]')?.classList.add('hidden');
			packedCateringForm.querySelector('[data-party-tray-form-back]')?.classList.add('hidden');
			setActiveCategory('packed-meals');
			packedMealsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
		};

		categoryCards.forEach((card) => {
			card.addEventListener('click', () => {
				const category = card.dataset.cateringCategory ?? '';
				setActiveCategory(category);
				if (category === 'packed-meals') {
					openPackedMeals();
				} else {
					packedMealsPanel.classList.add('hidden');
					packedCateringForm.querySelector('[data-party-tray-inquiry-summary]')?.classList.add('hidden');
					packedCateringForm.querySelector('[data-party-tray-form-back]')?.classList.add('hidden');
					if (category === 'party-trays') {
						packedCateringService.value = 'Party Trays';
						partyTraysPanel?.classList.remove('hidden');
						packedCateringInquiry.classList.add('hidden');
						document.querySelector('[data-catering-flow-panel]')?.classList.add('hidden');
						partyTraysPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
					} else {
						partyTraysPanel?.classList.add('hidden');
					}
				}

				const eventTypeOptions = Array.from(document.querySelectorAll('[data-event-type-option]'));
				const eventTypeOther = document.querySelector('[data-event-type-other]');
				const eventTypeOtherContainer = document.querySelector('[data-event-type-other-container]');

				if (eventTypeOptions.length && eventTypeOther instanceof HTMLInputElement && eventTypeOtherContainer) {
					const updateEventTypeDetails = () => {
						const isOther = eventTypeOptions.some((option) => option.checked && option.value === 'Other');
						eventTypeOtherContainer.classList.toggle('hidden', !isOther);
						eventTypeOther.required = isOther;
						if (!isOther) {
							eventTypeOther.value = '';
						}
					};

					eventTypeOptions.forEach((option) => option.addEventListener('change', updateEventTypeDetails));
					updateEventTypeDetails();
				}
			});
		});

		packageButtons.forEach((button) => {
			button.addEventListener('click', () => {
				selectedCode = button.dataset.packedMealSelect ?? '';
				if (packageField instanceof HTMLInputElement) {
					packageField.value = selectedCode;
				}
				if (errorMessage) {
					errorMessage.textContent = '';
				}
				refreshPackedSelection();
			});
		});
		sideOptions.forEach((option) => option.addEventListener('change', refreshPackedSelection));

		continueButton?.addEventListener('click', () => {
			if (!selectedCard() || !selectedSide()) {
				if (errorMessage) {
					errorMessage.textContent = 'Select one package and choose Bam-i or Vegetables to continue.';
				}
				return;
			}
			packedCateringService.value = 'Packed Meals';
			packedCateringInquiry.classList.remove('hidden');
			inquirySummary?.classList.remove('hidden');
			packedCateringForm.querySelector('[data-party-tray-inquiry-summary]')?.classList.add('hidden');
			packedCateringForm.querySelector('[data-party-tray-form-back]')?.classList.add('hidden');
			formBack?.classList.remove('hidden');
			packedCateringForm.querySelector('[data-catering-summary]')?.classList.add('hidden');
			document.querySelector('[data-catering-form-back]')?.classList.add('hidden');
			countLabel?.classList.remove('hidden');
			updatePackedSummary();
			packedCateringInquiry.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		packedMealsPanel.querySelector('[data-packed-meal-back-categories]')?.addEventListener('click', () => {
			packedMealsPanel.classList.add('hidden');
			packedCateringInquiry.classList.add('hidden');
			setActiveCategory('');
			document.getElementById('catering')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
		formBack?.querySelector('[data-packed-meal-back]')?.addEventListener('click', () => {
			packedCateringInquiry.classList.add('hidden');
			inquirySummary?.classList.add('hidden');
			formBack.classList.add('hidden');
			packedMealsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		if (partyTraysPanel) {
			const catalog = JSON.parse(atob(partyTraysPanel.dataset.trayCatalog ?? ''));
			const initialItems = JSON.parse(atob(partyTraysPanel.dataset.trayInitial ?? 'W10='));
			const dishSelect = partyTraysPanel.querySelector('[data-tray-dish]');
			const sizeOptions = Array.from(partyTraysPanel.querySelectorAll('[data-tray-size]'));
			const quantityInput = partyTraysPanel.querySelector('[data-tray-quantity]');
			const addButton = partyTraysPanel.querySelector('[data-tray-add]');
			const trayError = partyTraysPanel.querySelector('[data-tray-error]');
			const trayList = partyTraysPanel.querySelector('[data-tray-items]');
			const traySummary = partyTraysPanel.querySelector('[data-tray-selection-summary]');
			const trayTotal = partyTraysPanel.querySelector('[data-tray-total]');
			const trayContinue = partyTraysPanel.querySelector('[data-tray-continue]');
			const trayInquirySummary = packedCateringForm.querySelector('[data-party-tray-inquiry-summary]');
			const inquiryLines = trayInquirySummary?.querySelector('[data-party-tray-inquiry-lines]');
			const inquiryTotal = trayInquirySummary?.querySelector('[data-party-tray-inquiry-total]');
			const trayFormBack = packedCateringForm.querySelector('[data-party-tray-form-back]');
			const trayItems = [];
			let selectedCategory = '';

			const formatPrice = (price) => `₱${Number(price).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
			const selectedSize = () => sizeOptions.find((option) => option.checked)?.value ?? '';
			const selectedDish = () => dishSelect instanceof HTMLSelectElement ? dishSelect.value : '';
			const selectedCategoryData = () => catalog[selectedCategory] ?? null;

			const refreshBuilder = () => {
				const category = selectedCategoryData();
				const hasDish = Boolean(category && selectedDish());
				if (dishSelect instanceof HTMLSelectElement) {
					dishSelect.disabled = !category;
				}
				sizeOptions.forEach((option) => {
					option.disabled = !hasDish;
				});
				if (quantityInput instanceof HTMLInputElement) {
					quantityInput.disabled = !hasDish;
				}
				const quantity = Number(quantityInput?.value ?? 0);
				const canAdd = hasDish && Boolean(selectedSize()) && Number.isInteger(quantity) && quantity > 0 && quantity <= 5000;
				if (addButton instanceof HTMLButtonElement) {
					addButton.disabled = !canAdd;
				}
				if (trayError && canAdd) {
					trayError.textContent = '';
				}
			};

			const createFormInput = (name, value) => {
				const input = document.createElement('input');
				input.type = 'hidden';
				input.name = name;
				input.value = value;
				input.setAttribute('form', 'catering-inquiry-form');
				return input;
			};

			const renderTrayItems = () => {
				trayList?.replaceChildren();
				inquiryLines?.replaceChildren();
				let total = 0;
				let inquiryTotalAmount = 0;

				trayItems.forEach((item, index) => {
					const category = catalog[item.category];
					const unitPrice = item.size === 'full' ? category.full_price : category.half_price;
					const subtotal = unitPrice * item.quantity;
					total += subtotal;

					const row = document.createElement('div');
					row.className = 'grid gap-3 rounded-xl border border-[#333333] bg-[#1A1A1A] p-4 md:grid-cols-[1fr_auto_auto_auto] md:items-center';
					row.append(createFormInput(`tray_items[${index}][category]`, item.category));
					row.append(createFormInput(`tray_items[${index}][dish]`, item.dish));

					const title = document.createElement('div');
					title.className = 'font-semibold text-white';
					title.textContent = `${item.category} · ${item.dish}`;
					const price = document.createElement('p');
					price.className = 'mt-1 text-sm text-[#B8B8B8]';
					price.textContent = `${formatPrice(unitPrice)} each · ${formatPrice(subtotal)}`;
					title.append(price);
					row.append(title);

					const sizeSelect = document.createElement('select');
					sizeSelect.className = 'rounded-lg border border-[#555555] bg-[#201D19] px-3 py-2 text-sm text-white';
					sizeSelect.name = `tray_items[${index}][size]`;
					sizeSelect.setAttribute('form', 'catering-inquiry-form');
					[['full', `Full · ₱${category.full_price.toLocaleString('en-PH')}`], ['half', `Half · ₱${category.half_price.toLocaleString('en-PH')}`]].forEach(([value, label]) => {
						sizeSelect.add(new Option(label, value, false, value === item.size));
					});
					sizeSelect.addEventListener('change', () => {
						item.size = sizeSelect.value;
						renderTrayItems();
					});
					row.append(sizeSelect);

					const quantity = document.createElement('input');
					quantity.type = 'number';
					quantity.min = '1';
					quantity.max = '5000';
					quantity.value = String(item.quantity);
					quantity.name = `tray_items[${index}][quantity]`;
					quantity.setAttribute('form', 'catering-inquiry-form');
					quantity.className = 'w-24 rounded-lg border border-[#555555] bg-[#201D19] px-3 py-2 text-white';
					quantity.setAttribute('aria-label', `Quantity of ${item.dish}`);
					quantity.addEventListener('input', () => {
						const amount = Number(quantity.value);
						item.quantity = Number.isInteger(amount) && amount > 0 && amount <= 5000 ? amount : 0;
						renderTrayItems();
					});
					row.append(quantity);

					const remove = document.createElement('button');
					remove.type = 'button';
					remove.className = 'text-sm font-bold text-[#FBAB18] underline';
					remove.textContent = 'Remove';
					remove.addEventListener('click', () => {
						trayItems.splice(index, 1);
						renderTrayItems();
					});
					row.append(remove);
					trayList?.append(row);

					const summaryLine = document.createElement('li');
					summaryLine.textContent = `${item.category} · ${item.dish} · ${item.size === 'full' ? 'Full' : 'Half'} tray × ${item.quantity} · ${formatPrice(unitPrice)} each = ${formatPrice(subtotal)}`;
					inquiryLines?.append(summaryLine);
					inquiryTotalAmount += subtotal;
				});

				traySummary?.classList.toggle('hidden', trayItems.length === 0);
				if (trayTotal) {
					trayTotal.textContent = `Estimated total: ${formatPrice(total)}`;
				}
				if (inquiryTotal) {
					inquiryTotal.textContent = `Estimated total: ${formatPrice(inquiryTotalAmount)}`;
				}
				updateCateringPaymentSummary(inquiryTotalAmount);
				if (trayContinue instanceof HTMLButtonElement) {
					trayContinue.disabled = trayItems.length === 0 || trayItems.some((item) => item.quantity < 1);
				}
			};

			const selectCategory = (categoryName) => {
				selectedCategory = categoryName;
				trayCategoryButtons.forEach((button) => {
					const selected = button.dataset.trayCategory === categoryName;
					button.setAttribute('aria-pressed', String(selected));
					button.classList.toggle('border-[#FBAB18]', selected);
					button.classList.toggle('bg-[#FBAB18]/10', selected);
				});
				if (dishSelect instanceof HTMLSelectElement) {
					dishSelect.replaceChildren(new Option('Choose a dish', ''));
					(selectedCategoryData()?.dishes ?? []).forEach((dish) => dishSelect.add(new Option(dish, dish)));
				}
				sizeOptions.forEach((option) => {
					option.checked = false;
				});
				const category = selectedCategoryData();
				const fullPrice = partyTraysPanel.querySelector('[data-tray-full-price]');
				const halfPrice = partyTraysPanel.querySelector('[data-tray-half-price]');
				if (fullPrice) {
					fullPrice.textContent = `₱${category?.full_price.toLocaleString('en-PH') ?? ''}`;
				}
				if (halfPrice) {
					halfPrice.textContent = `₱${category?.half_price.toLocaleString('en-PH') ?? ''}`;
				}
				refreshBuilder();
			};

			trayCategoryButtons.forEach((button) => button.addEventListener('click', () => selectCategory(button.dataset.trayCategory ?? '')));
			dishSelect?.addEventListener('change', refreshBuilder);
			sizeOptions.forEach((option) => option.addEventListener('change', refreshBuilder));
			quantityInput?.addEventListener('input', refreshBuilder);
			addButton?.addEventListener('click', () => {
				const category = selectedCategoryData();
				const quantity = Number(quantityInput?.value ?? 0);
				if (!category || !selectedDish() || !selectedSize() || !Number.isInteger(quantity) || quantity < 1 || quantity > 5000) {
					if (trayError) {
						trayError.textContent = 'Choose a category, dish, tray size, and a quantity greater than zero.';
					}
					return;
				}

				const existingItem = trayItems.find((item) => item.category === selectedCategory && item.dish === selectedDish() && item.size === selectedSize());
				if (existingItem) {
					if (existingItem.quantity + quantity > 5000) {
						if (trayError) {
							trayError.textContent = 'The combined quantity for this dish and tray size cannot exceed 5,000.';
						}
						return;
					}
					existingItem.quantity += quantity;
				} else {
					trayItems.push({ category: selectedCategory, dish: selectedDish(), size: selectedSize(), quantity });
				}
				if (quantityInput instanceof HTMLInputElement) {
					quantityInput.value = '1';
				}
				renderTrayItems();
				refreshBuilder();
			});

			const openTrayBooking = () => {
				if (trayItems.length === 0 || trayItems.some((item) => item.quantity < 1)) {
					if (trayError) {
						trayError.textContent = 'Add at least one valid tray selection before continuing.';
					}
					return;
				}
				packedCateringService.value = 'Party Trays';
				packedCateringInquiry.classList.remove('hidden');
				partyTraysPanel.classList.add('hidden');
				inquirySummary?.classList.add('hidden');
				trayInquirySummary?.classList.remove('hidden');
				packedCateringForm.querySelector('[data-catering-summary]')?.classList.add('hidden');
				packedCateringForm.querySelector('[data-catering-form-back]')?.classList.add('hidden');
				formBack?.classList.add('hidden');
				trayFormBack?.classList.remove('hidden');
				renderTrayItems();
				packedCateringInquiry.scrollIntoView({ behavior: 'smooth', block: 'start' });
			};

			trayContinue?.addEventListener('click', openTrayBooking);
			trayFormBack?.querySelector('[data-party-tray-back]')?.addEventListener('click', () => {
				packedCateringInquiry.classList.add('hidden');
				trayInquirySummary?.classList.add('hidden');
				trayFormBack.classList.add('hidden');
				partyTraysPanel.classList.remove('hidden');
				partyTraysPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
			partyTraysPanel.querySelector('[data-tray-back-categories]')?.addEventListener('click', () => {
				partyTraysPanel.classList.add('hidden');
				setActiveCategory('');
				document.getElementById('catering')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});

			if (Array.isArray(initialItems)) {
				initialItems.forEach((item) => {
					const category = catalog[item.category];
					if (
						category
						&& category.dishes.includes(item.dish)
						&& ['full', 'half'].includes(item.size)
						&& Number.isInteger(Number(item.quantity))
						&& Number(item.quantity) > 0
						&& Number(item.quantity) <= 5000
					) {
						const existingItem = trayItems.find((selected) => selected.category === item.category && selected.dish === item.dish && selected.size === item.size);
						if (existingItem) {
							existingItem.quantity = Math.min(5000, existingItem.quantity + Number(item.quantity));
						} else {
							trayItems.push({ category: item.category, dish: item.dish, size: item.size, quantity: Number(item.quantity) });
						}
					}
				});
			}
			renderTrayItems();
			refreshBuilder();

			if (packedCateringService.value === 'Party Trays') {
				partyTraysPanel.classList.remove('hidden');
				setActiveCategory('party-trays');
				if (trayItems.length > 0) {
					trayInquirySummary?.classList.remove('hidden');
					trayFormBack?.classList.remove('hidden');
					packedCateringInquiry.classList.remove('hidden');
				}
			}

			packedCateringService.addEventListener('change', () => {
				if (packedCateringService.value === 'Party Trays') {
					partyTraysPanel.classList.remove('hidden');
					packedCateringInquiry.classList.add('hidden');
					inquirySummary?.classList.add('hidden');
					trayInquirySummary?.classList.add('hidden');
					trayFormBack?.classList.add('hidden');
					partyTraysPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
				} else {
					partyTraysPanel.classList.add('hidden');
				}
			});
		}

		packedCateringService.addEventListener('change', () => {
			if (packedCateringService.value === 'Packed Meals') {
				openPackedMeals();
			} else {
				packedMealsPanel.classList.add('hidden');
			}
		});
		guestCount?.addEventListener('input', updatePackedSummary);
		packedCateringForm.addEventListener('submit', (event) => {
			if (packedCateringService.value !== 'Packed Meals') {
				return;
			}
			if (!selectedCard() || !selectedSide()) {
				event.preventDefault();
				if (errorMessage) {
					errorMessage.textContent = 'Select one package and choose Bam-i or Vegetables before sending your inquiry.';
				}
				packedMealsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
				return;
			}
			const submitButton = packedCateringForm.querySelector('button[type="submit"]');
			if (submitButton instanceof HTMLButtonElement) {
				submitButton.disabled = true;
				submitButton.textContent = 'Sending Catering Inquiry...';
			}
		});

		refreshPackedSelection();
		const initialCategory = packedCateringService.value === 'Packed Meals'
			? 'packed-meals'
			: packedCateringService.value === 'Party Trays'
				? 'party-trays'
				: packedCateringService.value === 'Full-Service Catering'
					? 'catering'
					: '';
		setActiveCategory(initialCategory);

		if (packedCateringService.value === 'Packed Meals' && selectedCode && selectedSide()) {
			packedMealsPanel.classList.remove('hidden');
			packedCateringInquiry.classList.remove('hidden');
			inquirySummary?.classList.remove('hidden');
			formBack?.classList.remove('hidden');
			countLabel?.classList.remove('hidden');
			updatePackedSummary();
		}
	}
});
