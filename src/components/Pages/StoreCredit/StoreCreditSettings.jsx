import React, {useEffect, useState} from "react"
import Switch from "../../utils/switch/Switch";
import axios from "axios";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {getNonce, getPostRequestUrl} from "../../../utils/helper";
import {useI18n} from "@wordpress/react-i18n";
import {Skeleton} from "../../Skeleton";

const StoreCreditSettings = () => {
	const { __ } = useI18n();

	const { nonce, ajaxUrl } = hexCuponData;
	const [isLoading, setIsLoading] = useState(true);

	const [switchState, setSwitchState] = useState(false)

	const handleSwitchChange = (newSwitchState) => {
		setSwitchState(newSwitchState);
	};

	const submitStoreCreditSettings = () => {
		axios
			.post(getPostRequestUrl('store_credit_settings_save'), {
				nonce: getNonce(),
				action: 'store_credit_settings_save',
				enable: switchState,

			}, {
				headers: {
					"Content-Type": "multipart/form-data"
				}
			})
			.then((response) => {
			})
			.catch((error) => {
				console.error('Error:', error);
			});
	}

	const handleButtonClick = () => {
		submitStoreCreditSettings();
		toast.success('Option saved!', {
			position: 'top-center',
			autoClose: 1000,
			hideProgressBar: false,
			closeOnClick: true,
			pauseOnHover: false,
			draggable: true,
		});
	};

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'all_combined_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({ data }) => {
				setSwitchState(data.storeCreditEnable.enable);
			})
			.catch((error) => {
				console.error('Error:', error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce]);

	return (
		<>
			<h2 className="store_credit_enable_title">{__("Store credit settings", "woocommerce")}</h2>
			{isLoading ? (
				<Skeleton height={500} radius={10} />
			) : (
				<>
					<p>
						<span className="store_credit_enable_text">{__("Enable Store credit on Refund", "hex-coupon-for-woocommerce")}</span>
						<Switch isChecked={switchState} onSwitchChange={handleSwitchChange} />
					</p>

					<input
						type="submit"
						value="Save"
						className="store_credit_enable_button py-2.5 pl-4 pr-4 bg-purple-600 text-white cursor-pointer mt-2.5"
						onClick={handleButtonClick}
					/>
					<ToastContainer />
				</>
			)}
		</>
	);
}

export default StoreCreditSettings;
