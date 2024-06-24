import React, { useEffect, useState } from "react";
import axios from "axios";
import { Skeleton } from "../../Skeleton";
import Table from "../../utils/table/Table";
import THead from "../../utils/table/THead";
import Th from "../../utils/table/Th";
import TBody from "../../utils/table/TBody";
import { Link } from "react-router-dom";
import { __ } from '@wordpress/i18n';
import BodyCardHeaderLeft from "../../Pagebody/card/BodyCardHeaderLeft";
import BodyCardHeaderLeftItem from "../../Pagebody/card/BodyCardHeaderLeftItem";
import BodyCardHeaderTItle from "../../Pagebody/card/BodyCardHeaderTItle";
import {IconChevronLeft} from "@tabler/icons-react";
import BodyCardHeaderRight from "../../Pagebody/card/BodyCardHeaderRight";
import ButtonWrapper from "../../utils/button/ButtonWrapper";
import Button from "../../utils/button/Button";
import BodyCardHeader from "../../Pagebody/card/BodyCardHeader";
import HexCardHeaderLeft from "../../HexCardHeader/HexCardHeaderLeft";
import HexCardHeaderTitle from "../../HexCardHeader/HexCardHeaderTitle";
import HexCardHeaderRight from "../../HexCardHeader/HexCardHeaderRight";
import SingleSelect from "../../Global/FormComponent/SingleSelect/SingleSelect";
import {Bar} from "react-chartjs-2";

const TopLoyaltyPointsEarner = () => {
	const {nonce,ajaxUrl} = hexCuponData;
	const [isLoading, setIsLoading] = useState(true);
	const [topPointsEarners, setTopPointsEarners] = useState([]);
	const [topPointsReasons, setTopPointsReasons] = useState([]);

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: "all_combined_data",
				},
				headers: {
					"Content-Type": "application/json",
				},
			})
			.then(({ data }) => {
				if (data && data.topPointsEarner) {
					setTopPointsEarners(data.topPointsEarner);
					setTopPointsReasons(data.topPointsReasons);
				} else {
					console.error("Invalid data format", data);
				}
			})
			.catch((error) => {
				console.error("Error:", error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce, ajaxUrl]);


	return (
		<>
			<div className="loyalty-dashboard-container">
				{isLoading ? (
					<Skeleton height={500} radius={10} />
				) : (
					<>
						<div className="loyalty-dashboard-box">
							<div className="hexDashboard__card mt-4 radius-10">
								<div className="hexDashboard__card__header">
									<div className="hexDashboard__card__header__flex">
										<HexCardHeaderLeft>
											<HexCardHeaderTitle titleHeading={__("Top Loyalty Points Sources","hex-coupon-for-woocommerce")} />
										</HexCardHeaderLeft>
									</div>
								</div>
								<div className="hexDashboard__card__inner mt-4">
									<Table className="border text-left">
										<THead>
											<Th>{__("Sources")}</Th>
											<Th>{__("Points")}</Th>
										</THead>
										<TBody>
											{topPointsReasons.length > 0 ? (
												topPointsReasons.map((log, index) => (
													<tr key={index}>
														<td>
															{log.reason}
														</td>
														<td>{log.points}</td>
													</tr>
												))
											) : (
												<tr style={{ textAlign: "center" }}>
													<td colSpan="8">{__("No data available")}</td>
												</tr>
											)}
										</TBody>
									</Table>
								</div>
							</div>
						</div>
					</>
				)}

				{isLoading ? (
					<Skeleton height={500} radius={10} />
				) : (
					<>
						<div className="loyalty-dashboard-box">
							<div className="hexDashboard__card mt-4 radius-10">
								<div className="hexDashboard__card__header">
									<div className="hexDashboard__card__header__flex">
										<HexCardHeaderLeft>
											<HexCardHeaderTitle titleHeading={__("Top Loyalty Points Earner","hex-coupon-for-woocommerce")} />
										</HexCardHeaderLeft>
									</div>
								</div>
								<div className="hexDashboard__card__inner mt-4">
									<Table className="border text-left">
										<THead>
											<Th>{__("Customer Name")}</Th>
											<Th>{__("Points")}</Th>
										</THead>
										<TBody>
											{topPointsEarners.length > 0 ? (
												topPointsEarners.map((log, index) => (
													<tr key={index}>
														<td>
															{log.user_name}
														</td>
														<td>{log.points}</td>
													</tr>
												))
											) : (
												<tr style={{ textAlign: "center" }}>
													<td colSpan="8">{__("No data available")}</td>
												</tr>
											)}
										</TBody>
									</Table>
								</div>
							</div>
						</div>
					</>
				)}

				{isLoading ? (
					<Skeleton height={500} radius={10} />
				) : (
					<>
						<div className="loyalty-dashboard-box">A Graph</div>
					</>
				)}
			</div>



		</>
	);
};

export default TopLoyaltyPointsEarner;
