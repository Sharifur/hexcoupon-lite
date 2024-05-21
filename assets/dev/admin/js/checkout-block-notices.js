wp.domReady(() => {
	const { createElement, render, useState, useEffect, useRef } = wp.element;
	const { Notice } = wp.components;

	const CustomNotice = () => {
		const [points, setPoints] = useState(0);
		const [divider, setDivider] = useState(0);
		const [multiplier, setMultiplier] = useState(0);
		const pointsRef = useRef(null);
		const placeOrderListenerAttached = useRef(false);

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

			const handlePlaceOrderClick = async (event) => {
				try {
					const pointsValue = pointsRef.current ? parseInt(pointsRef.current.innerText) : 0;
					const response = await jQuery.ajax({
						url: pointsForCheckoutBlock.ajax_url,
						method: 'POST',
						data: {
							action: 'save_loyalty_points',
							security: pointsForCheckoutBlock.nonce,
							user_id: pointsForCheckoutBlock.user_id,
							points: pointsValue // Use the latest points value from the span
						}
					});

					if (!response.success) {
						console.error('Failed to save points:', response.data);
					}
				} catch (error) {
					console.error('Error saving points:', error);
				}
			};

			const attachPlaceOrderListener = () => {
				const placeOrderButton = document.querySelector('.wc-block-components-checkout-place-order-button');
				if (placeOrderButton && !placeOrderListenerAttached.current) {
					placeOrderButton.addEventListener('click', handlePlaceOrderClick);
					placeOrderListenerAttached.current = true;
				} else if (!placeOrderButton) {
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
		}, [divider, multiplier]);

		return createElement(
			Notice,
			{
				status: 'info',
				isDismissible: false,
			},
			`You will earn `,
			createElement('span', { className: 'points-value', ref: pointsRef }, points),
			` points with this order.`
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
