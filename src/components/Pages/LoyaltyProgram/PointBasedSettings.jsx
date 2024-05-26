import React, {useEffect, useState} from "react";
import { IconChevronLeft, IconInfoCircle } from "@tabler/icons-react";
import { useNavigate } from "react-router-dom";
import Switch from "../../utils/switch/Switch";
import { useI18n } from "@wordpress/react-i18n";
import { toast, ToastContainer } from "react-toastify";
import axios from "axios";
import { getNonce, getPostRequestUrl } from "../../../utils/helper";
import {Skeleton} from "../../Skeleton";
import Tooltip from '@mui/material/Tooltip';

const PointBasedLoyaltySettings = () => {
	const { nonce, ajaxUrl } = loyaltyProgramData;
	const { __ } = useI18n();
	const [isLoading, setIsLoading] = useState(true);
	const navigate = useNavigate();

	const goBack = () => {
		navigate(-1);
	};

	const [settings, setSettings] = useState({
		pointsOnPurchase: { enable: false, pointAmount: 100, spendingAmount: 1 },
		pointsForSignup: { enable: false, pointAmount: 100 },
		pointsForReferral: { enable: false, pointAmount: 100 },
		conversionRate: { points: 100, credit: 1 },
	});

	const handleSwitchChange = (field) => (newSwitchState) => {
		setSettings((prevSettings) => ({
			...prevSettings,
			[field]: {
				...prevSettings[field],
				enable: newSwitchState,
			},
		}));
	};

	const handleInputChange = (field, subField) => (event) => {
		const value = event.target.value;
		setSettings((prevSettings) => ({
			...prevSettings,
			[field]: {
				...prevSettings[field],
				[subField]: value,
			},
		}));
	};

	const submitPointsLoyaltySettings = () => {
		axios
			.post(getPostRequestUrl('points_loyalty_settings_save'), {
				nonce: getNonce(),
				action: 'points_loyalty_settings_save',
				settings: settings,
			}, {
				headers: {
					"Content-Type": "multipart/form-data"
				}
			})
			.then((response) => {
				// Handle the successful response here
			})
			.catch((error) => {
				console.error('Error:', error);
			});
	};

	const handleSave = () => {
		submitPointsLoyaltySettings();
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
					action: 'point_loyalty_program_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({ data }) => {
				setSettings(data.pointLoyaltyProgramData)
			})
			.catch((error) => {
				console.error('Error:', error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce]);

	return (
		<div className="point-based-loyalty-settings">
			<h1>
				<IconChevronLeft onClick={goBack} className="back-icon" /> {__("Point Loyalty Settings")}
			</h1>

			<div className="settings-section">
				{isLoading ? (
					<Skeleton height={500} radius={10} />
				) : (
					<>
						<div className="setting-item">
							<div className="setting-header">
								<div className="switch-container">
									<span>{__("Point on Purchase")}</span>
									<span className="switch-enabled">{__("Enabled")}</span>
									<Switch
										isChecked={settings.pointsOnPurchase.enable}
										onSwitchChange={handleSwitchChange("pointsOnPurchase")}
									/>
								</div>

								<div className="setting-body">
									<label>
										{__("Point Amount")}
										<input
											type="number"
											value={settings.pointsOnPurchase.pointAmount}
											onChange={handleInputChange("pointsOnPurchase", "pointAmount")}
										/>
									</label>
									<label>
										{__("Spending Amount")}
										<Tooltip title={__("Amount to spend to earn points on every purchase")}>
											<IconInfoCircle style={{ marginLeft: "5px" }} />
										</Tooltip>
										<input
											type="number"
											value={settings.pointsOnPurchase.spendingAmount}
											onChange={handleInputChange("pointsOnPurchase", "spendingAmount")}
											placeholder="$"
										/>
									</label>
								</div>
							</div>
						</div>

						<div className="setting-item">
							<div className="setting-header">
								<div className="switch-container">
									<span>{__("Points for Signup")}</span>
									<span className="switch-enabled">{__("Enabled")}</span>
									<Switch
										isChecked={settings.pointsForSignup.enable}
										onSwitchChange={handleSwitchChange("pointsForSignup")}
									/>
								</div>
								<div className="setting-body">
									<label>
										{__("Point Amount")}
										<input
											type="number"
											value={settings.pointsForSignup.pointAmount}
											onChange={handleInputChange("pointsForSignup", "pointAmount")}
										/>
									</label>
								</div>
							</div>
						</div>

						<div className="setting-item">
							<div className="setting-header">
								<div className="switch-container">
									<span>{__("Points for Referral")}</span>
									<span className="switch-enabled">{__("Enabled")}</span>
									<Switch
										isChecked={settings.pointsForReferral.enable}
										onSwitchChange={handleSwitchChange("pointsForReferral")}
									/>
								</div>
								<div className="setting-body">
									<label>
										{__("Point Amount")}
										<input
											type="number"
											value={settings.pointsForReferral.pointAmount}
											onChange={handleInputChange("pointsForReferral", "pointAmount")}
										/>
									</label>
								</div>
							</div>
						</div>

						<div className="conversion-rate">
							<p>{__("Points")}</p>
							<label>
								<input
									type="number"
									value={settings.conversionRate.points}
									onChange={handleInputChange("conversionRate", "points")}
								/>
								<span>{settings.conversionRate.points} POINTS = {settings.conversionRate.credit} S.CREDIT</span>
							</label>
							<p>{__("No. of points required to convert in 1 store credit")}</p>
						</div>

						<div className="save-button-area">
							<button className="save-button" onClick={handleSave}>
								{__("Save Changes")}
							</button>
							<ToastContainer />
						</div>
					</>
				)}
			</div>
		</div>
	);
};

export default PointBasedLoyaltySettings;
