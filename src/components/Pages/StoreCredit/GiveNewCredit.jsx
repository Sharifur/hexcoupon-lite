import React, { useEffect, useState } from "react";
import { IconChevronLeft } from "@tabler/icons-react";
import { useNavigate } from "react-router-dom";
import BodyCard from "../../Pagebody/card/BodyCard";
import BodyCardHeaderLeft from "../../Pagebody/card/BodyCardHeaderLeft";
import BodyCardHeaderLeftItem from "../../Pagebody/card/BodyCardHeaderLeftItem";
import BodyCardHeaderTItle from "../../Pagebody/card/BodyCardHeaderTItle";
import Button from "../../utils/button/Button";
import BodyCardHeader from "../../Pagebody/card/BodyCardHeader";
import PageBody from "../../Pagebody/PageBody";
import axios from "axios";
import { getNonce, getPostRequestUrl } from "../../../utils/helper";
import { toast, ToastContainer } from "react-toastify";
import { __ } from '@wordpress/i18n';
import Select from 'react-select';

const GiveNewCredit = () => {
	const { nonce, ajaxUrl } = hexCuponData;
	const [isLoading, setIsLoading] = useState(true);
	const navigate = useNavigate();
	const [adminInfo, setAdminInfo] = useState([]);

	const [customers, setCustomers] = useState([]);

	const customersInfoForSelect2 = Object.keys(customers).map(customerId => ({
		value: customerId,
		label: customers[customerId]
	}));

	const goToPreviousPage = () => {
		navigate(-1);
	}

	const [storeCreditAmount, setStoreCreditAmount] = useState("");

	const [note, setNote] = useState("");

	const [selectedUsers, setSelectedUsers] = useState([]);
	const [selectedCustomersCount, setSelectedCustomersCount] = useState("");

	const handleUserSelect = (selectedOptions) => {
		const selectedIds = selectedOptions.map(option => option.value);
		setSelectedUsers(selectedIds);
		setSelectedCustomersCount(selectedOptions.length);
	}

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
				setAdminInfo(data.adminData);
				setCustomers(data.allCustomersInfo);
			})
			.catch((error) => {
				console.error('Error:', error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce]);

	const sendStoreCreditInfo = () => {
		toast.error(
			({ closeToast }) => (
				<div>
					Upgrade to <a href="https://hexcoupon.com/pricing/" target="_blank" rel="noopener noreferrer"><b style={{color:"#A760FE"}}>Pro</b></a> to use this feature!
				</div>
			),
			{
				position: 'top-center',
				autoClose: false,
				hideProgressBar: false,
				closeOnClick: true,
				pauseOnHover: false,
				draggable: true,
			}
		);
	}

	return (
		<>
			<PageBody>
				<BodyCard className="p-0">
					<BodyCardHeader className="p-4" isFlex={true}>
						<BodyCardHeaderLeft isFlex={true}>
							<BodyCardHeaderLeftItem>
								<BodyCardHeaderTItle icon={<IconChevronLeft onClick={goToPreviousPage} />} children={__("Give new credits", "hex-coupon-for-woocommerce")} />
							</BodyCardHeaderLeftItem>
						</BodyCardHeaderLeft>
					</BodyCardHeader>

					<div className="main-gift-credit-container grid grid-cols-12 gap-5 p-4">
						<div className="col-span-12 md:col-span-7 xl:col-span-8">
							<div className="single__item mt-0">
								<label htmlFor="store_credit_amount" className="text-md text-[var(--hex-paragraph-color)]">{__("Store credit amount", "hex-coupon-for-woocommerce")}</label>
								<input
									type="number"
									id="store_credit_amount"
									onChange={(e) => setStoreCreditAmount(e.target.value)}
									value={storeCreditAmount}
									placeholder="Enter amount"
									className="py-2.5 pl-4 pr-4 mt-2 h-[34] w-full !border-transparent !ring-1 !ring-[var(--hex-border-color)] text-md !text-[var(--hex-paragraph-color)] focus:!ring-[var(--hex-main-color-one)] focus:!border-transparent"
								/>
							</div>
							<div className="single__item single__select mt-4">
								<label htmlFor="myTextArea" className="text-md text-[var(--hex-paragraph-color)]">{__("Grant credit to multiple customers: ", "hex-coupon-for-woocommerce")}</label>
								<Select
									closeMenuOnSelect={false}
									isMulti
									options={customersInfoForSelect2}
									onChange={handleUserSelect}
									className="mt-2"
								/>
							</div>
							<div className="single__item mt-4">
								<label htmlFor="myTextArea" className="text-md text-[var(--hex-paragraph-color)]">{__("Write message or note:", "hex-coupon-for-woocommerce")}</label>
								<textarea
									id="myTextArea"
									onChange={(e) => setNote(e.target.value)}
									value={note}
									rows={4}
									cols={50}
									className="py-2.5 pl-4 pr-4 mt-2 h-[auto] w-full !border-transparent !ring-1 !ring-[var(--hex-border-color)] text-md !text-[var(--hex-paragraph-color)] focus:!ring-[var(--hex-main-color-one)] focus:!border-transparent"
								/>
							</div>
						</div>
						<div className="col-span-12 md:col-span-5 xl:col-span-4">
							<div className="creditSummary ring-2 ring-[var(--hex-main-color-one)] p-4 text-center w-full rounded-md">
								<h1 className="text-3xl">Granting credits:</h1>
								<p className="mt-2.5 text-md">{"Amount of credits: " + storeCreditAmount}</p>
								<p className="text-md">{"Granting credits to no. of customer: " + selectedCustomersCount}</p>
								<Button children={__("Give New Credit Now", "hex-coupon-for-woocommerce")} btnStyle={"primary"} onClick={sendStoreCreditInfo} className="mt-4" />
							</div>
						</div>
					</div>
				</BodyCard>
			</PageBody>
			<ToastContainer />
		</>
	);
}

export default GiveNewCredit;