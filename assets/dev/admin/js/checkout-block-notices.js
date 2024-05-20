wp.domReady(() => {
	const { createElement, render, useState, useEffect } = wp.element;
	const { Notice } = wp.components;

	const CustomNotice = () => {
		const [points, setPoints] = useState(0);
		const [divider, setDivider] = useState(0);
		const [multiplier, setMultiplier] = useState(0);

		useEffect(() => {
			const fetchMultiplier = async () => {
				try {
					const response = await jQuery.ajax({
						url: pointsForCheckoutBlock.ajax_url,
						method: 'POST',
						data: {
							action: 'show_loyalty_points_in_checkout',
							security: pointsForCheckoutBlock.nonce
						}
					});

					if (response.success) {
						setDivider(response.data.spendingAmount);
						setMultiplier(response.data.pointAmount);
					}
				} catch (error) {
					console.error('Error fetching multiplier:', error);
				}
			};

			const calculatePoints = () => {
				const totalElement = document.querySelector('.wc-block-components-totals-footer-item-tax-value');
				if (totalElement && divider && multiplier) {
					const totalPrice = parseFloat(totalElement.innerText.replace(/[^\d.-]/g, ''));
					const calculatedPoints = Math.round((totalPrice / divider) * multiplier);
					setPoints(calculatedPoints);
				} else {
					setPoints(0);
				}
			};

			const observeTotalElement = () => {
				const totalElement = document.querySelector('.wc-block-components-totals-footer-item-tax-value');
				if (totalElement) {
					const observer = new MutationObserver(calculatePoints);
					const config = { childList: true, subtree: true, characterData: true };

					observer.observe(totalElement, config);

					return () => {
						observer.disconnect();
					};
				} else {
					// Retry after a short delay if the element is not found
					setTimeout(observeTotalElement, 500);
				}
			};

			const handlePlaceOrderClick = (event) => {
				const latestPoints = points; // Capture the latest points value
				savePoints(pointsForCheckoutBlock.user_id, latestPoints);
			};

			const attachPlaceOrderListener = () => {
				const placeOrderButton = document.querySelector('.wc-block-components-checkout-place-order-button');
				if (placeOrderButton) {
					placeOrderButton.addEventListener('click', handlePlaceOrderClick);
				} else {
					// Retry after a short delay if the button is not found
					setTimeout(attachPlaceOrderListener, 500);
				}
			};

			fetchMultiplier();
			// Initial calculation on page load
			calculatePoints();
			const disconnectObserver = observeTotalElement();
			attachPlaceOrderListener();

			return () => {
				if (disconnectObserver) {
					disconnectObserver();
				}
			};
		}, [divider, multiplier, points]);

		const savePoints = async (userId, points) => {
			try {
				const response = await jQuery.ajax({
					url: pointsForCheckoutBlock.ajax_url,
					method: 'POST',
					data: {
						action: 'save_loyalty_points',
						security: pointsForCheckoutBlock.nonce,
						user_id: userId,
						points: points
					}
				});

				if (!response.success) {
					console.error('Failed to save points:', response.data);
				}
			} catch (error) {
				console.error('Error saving points:', error);
			}
		};

		return createElement(
			Notice,
			{
				status: 'info',
				isDismissible: false,
			},
			`You will earn ${points} points with this order.`
		);
	};

	const checkoutForm = document.querySelector('.wc-block-checkout');
	if (checkoutForm) {
		const noticeWrapper = document.createElement('div');
		noticeWrapper.classList.add('custom-checkout-notice-wrapper');
		checkoutForm.prepend(noticeWrapper);
		render(createElement(CustomNotice), noticeWrapper);
	}
});
