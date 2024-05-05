import metadata from './block.json';
import { __ } from '@wordpress/i18n';
import { CheckboxControl } from '@wordpress/components';
import { useEffect, useState, useCallback, useRef } from "react";
import axios from "axios";

const { nonce, postUrl } = storeCreditData;
// const nonce = window.storeCreditData?.nonce;
// const postUrl = window.storeCreditData?.postUrl;

function getPostRequestUrl(action) {
	return `${postUrl}?action=${action}`;
}

function getNonce() {
	return nonce;
}

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;

const Block = ({ children, checkoutExtensionData }) => {
	const remainingCredit = parseFloat(window.storeCreditData.total_remaining_store_credit);
	const cartTotal = parseFloat(window.storeCreditData.cart_total);

	const deductedTotal = parseFloat(remainingCredit) > parseFloat(cartTotal) ? cartTotal : remainingCredit;

	const [storeCredit, setStoreCredit] = useState('');
	const { setExtensionData } = checkoutExtensionData;
	const myRef = useRef(null);

	// Function to handle checkbox change
	useEffect(() => {
		setExtensionData('hex-coupon-for-woocommerce', 'use_store_credit', storeCredit);
	}, [storeCredit, setExtensionData]);

	const onInputChange = useCallback(
		(value) => {
			setStoreCredit(value);
			setExtensionData('hex-coupon-for-woocommerce', 'use_store_credit', value);
		},
		[setStoreCredit, setExtensionData]
	)

	useEffect(() => {
		// Ensure that submitStoreCreditSettings is called with the updated text content
		if (myRef.current) {
			submitStoreCreditSettings(myRef.current.textContent);
		}
	}, [myRef.current, storeCredit]);

	const submitStoreCreditSettings = (value) => {
		axios
			.post(getPostRequestUrl('store_credit_deduction_save'), {
				nonce: getNonce(),
				action: 'store_credit_deduction_save',
				deductedStoreCredit: value,
			}, {
				headers: {
					"Content-Type": "multipart/form-data"
				}
			})
			.then((response) => {
				// Handle response if needed
			})
			.catch((error) => {
				console.error('Error:', error);
			});
	}

	return (
		<>
			<div className="wc-block-components">
				<h5>{__("Available Store Credit: ", "hex-coupon-for-woocommerce") + remainingCredit.toFixed(2)}</h5>
				<CheckboxControl className="store_credit_chckbox" label={__("Use Store Credit", "hex-coupon-for-woocommerce")} onChange={onInputChange} name="use_store_credit" style={{marginRight:"5px"}}/>

				{storeCredit && (
					<span style={{fontWeight:"bold"}}>
					{storeCredit && __("Store Credit Used: -", "hex-coupon-for-woocommerce")}
						<b ref={myRef}>{storeCredit && deductedTotal.toFixed(2)}</b>
				</span>
				)}
			</div>
		</>
	);
};

const options = {
	metadata,
	component: Block,
};

registerCheckoutBlock(options);


