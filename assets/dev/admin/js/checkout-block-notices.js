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
						url: custom_ajax_object.ajax_url,
						method: 'POST',
						data: {
							action: 'get_points_multiplier',
							security: custom_ajax_object.nonce
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
				const totalElement = document.querySelector('.wc-block-components-totals-item__value');
				if (totalElement && divider && multiplier) {
					const totalPrice = parseFloat(totalElement.innerText.replace(/[^\d.-]/g, ''));
					const calculatedPoints = Math.round((totalPrice / divider) * multiplier);
					setPoints(calculatedPoints);
				}
			};

			const checkAndCalculatePoints = () => {
				const totalElement = document.querySelector('.wc-block-components-totals-item__value');
				if (totalElement) {
					calculatePoints();
					// Observe for changes to the total price element
					const observer = new MutationObserver(calculatePoints);
					const config = { childList: true, subtree: true };
					observer.observe(totalElement, config);

					return () => {
						if (totalElement) {
							observer.disconnect();
						}
					};
				} else {
					// Retry after a short delay if the element is not found
					setTimeout(checkAndCalculatePoints, 500);
				}
			};

			fetchMultiplier();
			checkAndCalculatePoints();
		}, [divider, multiplier]);

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
