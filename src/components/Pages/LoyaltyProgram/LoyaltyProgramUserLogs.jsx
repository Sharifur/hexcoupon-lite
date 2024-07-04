import React, { useEffect, useState } from "react";
import axios from "axios";
import { useParams, useNavigate } from "react-router-dom";
import { Skeleton } from "../../Skeleton";
import { ToastContainer } from "react-toastify";
import Table from "../../utils/table/Table";
import THead from "../../utils/table/THead";
import Th from "../../utils/table/Th";
import TBody from "../../utils/table/TBody";
import BodyCard from "../../Pagebody/card/BodyCard";
import PageBody from "../../Pagebody/PageBody";
import BodyCardHeaderLeft from "../../Pagebody/card/BodyCardHeaderLeft";
import BodyCardHeaderLeftItem from "../../Pagebody/card/BodyCardHeaderLeftItem";
import BodyCardHeaderTItle from "../../Pagebody/card/BodyCardHeaderTItle";
import BodyCardHeaderRight from "../../Pagebody/card/BodyCardHeaderRight";
import BodyCardHeader from "../../Pagebody/card/BodyCardHeader";
import ButtonWrapper from "../../utils/button/ButtonWrapper";
import ReactPaginate from "react-paginate";
import { IconChevronLeft } from "@tabler/icons-react";
import { __ } from '@wordpress/i18n';

const LoyaltyProgramUserLogs = () => {
	const { userId } = useParams();
	const { nonce, ajaxUrl } = loyaltyProgramLogs;
	const [isLoading, setIsLoading] = useState(true);
	const [userLogs, setUserLogs] = useState([]);
	const [currentPage, setCurrentPage] = useState(0);
	const [filterOption, setFilterOption] = useState("all");
	const navigate = useNavigate();
	const itemsPerPage = 10;
	const [userName, setUserName] = useState('');

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: "point_loyalty_program_logs",
					user_id: userId, // Fetch logs for the specific user
				},
				headers: {
					"Content-Type": "application/json",
				},
			})
			.then(({ data }) => {
				if (data && data.pointsLoyaltyLogs) {
					const logs = data.pointsLoyaltyLogs.filter(log => log.user_id === userId);
					setUserLogs(logs);
					if (logs.length > 0) {
						setUserName(logs[0].user_name); // Set userName from the first log entry
					}
				} else {
					console.error("Invalid data format", data);
				}
			})
			.catch((error) => {
				console.error("Error:", error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce, ajaxUrl, userId]);

	const getReasonString = (reasonCode) => {
		switch (reasonCode) {
			case "0":
				return __("Signup");
			case "1":
				return __("Referral");
			case "2":
				return __("Purchase");
			default:
				return __("Unknown");
		}
	};

	const getReasonElement = (reasonCode) => {
		switch (reasonCode) {
			case "0":
				return <span className="px-2.5 py-2 bg-green-100 text-green-800 w-full text-center">{__("Signup")}</span>;
			case "1":
				return <span className="px-2.5 py-2 bg-cyan-100 text-cyan-800 w-full text-center">{__("Referral")}</span>;
			case "2":
				return <span className="px-2.5 py-2 bg-indigo-100 text-indigo-800 w-full text-center">{__("Purchase")}</span>;
			default:
				return <span className="px-2.5 py-2 bg-green-100 text-green-800 w-full text-center">{__("Unknown")}</span>;
		}
	};

	const handlePageClick = (data) => {
		setCurrentPage(data.selected);
	};

	const handleFilterChange = (event) => {
		setFilterOption(event.target.value);
		setCurrentPage(0); // Reset to first page on filter change
	};

	const filteredLogs = userLogs.filter((log) => {
		const reasonString = getReasonString(log.reason).toLowerCase();
		const matchesFilter = filterOption === "all" || reasonString === filterOption;
		return matchesFilter;
	});

	const offset = currentPage * itemsPerPage;
	const currentLogs = filteredLogs.slice(offset, offset + itemsPerPage);
	const pageCount = Math.ceil(filteredLogs.length / itemsPerPage);

	const goBack = () => {
		navigate(-1);
	};

	return (
		<>
			<PageBody>
				<BodyCard className="p-0">
					<BodyCardHeader className="p-4" isFlex={true}>
						<BodyCardHeaderLeft isFlex={true}>
							<BodyCardHeaderLeftItem>
								<BodyCardHeaderTItle icon={<IconChevronLeft onClick={goBack} />} children={`${userName}'s Loyalty Points log`} />
							</BodyCardHeaderLeftItem>
						</BodyCardHeaderLeft>
						<BodyCardHeaderRight>
							<ButtonWrapper isFlex={true}>
								<select
									value={filterOption}
									onChange={handleFilterChange}
									className="customSelect py-2.5 pl-4 pr-4 h-[34px] !ring-1 !border-transparent !ring-[var(--hex-border-color)] text-md !text-[var(--hex-paragraph-color)] focus:!ring-[var(--hex-main-color-one)] focus:!border-transparent"
								>
									<option value="all">{__("All")}</option>
									<option value="signup">{__("Signup")}</option>
									<option value="referral">{__("Referral")}</option>
									<option value="purchase">{__("Purchase")}</option>
								</select>
							</ButtonWrapper>
						</BodyCardHeaderRight>
					</BodyCardHeader>
					{isLoading ? (
						<Skeleton height={500} radius={10} />
					) : (
						<>
							<Table className="border text-left">
								<THead>
									<Th>{__("Customer Name")}</Th>
									<Th>{__("Email")}</Th>
									<Th>{__("Points")}</Th>
									<Th>{__("Reason")}</Th>
									<Th>{__("Referrer ID")}</Th>
									<Th>{__("Converted Credit")}</Th>
									<Th>{__("Conversion Rate")}</Th>
									<Th>{__("Date")}</Th>
								</THead>
								<TBody>
									{currentLogs.length > 0 ? (
										currentLogs.map((log, index) => (
											<tr key={index}>
												<td>{log.user_name}</td>
												<td>{log.user_email}</td>
												<td>{log.points}</td>
												<td>{getReasonElement(log.reason)}</td>
												<td>{log.referee_id ? log.referee_id : "NA"}</td>
												<td>{log.converted_credit}</td>
												<td>{log.conversion_rate}</td>
												<td>{log.created_at}</td>
											</tr>
										))
									) : (
										<tr style={{ textAlign: "center" }}>
											<td colSpan="8">No Data Available</td>
										</tr>
									)}
								</TBody>
							</Table>
							<ReactPaginate
								previousLabel={"previous"}
								nextLabel={"next"}
								breakLabel={"..."}
								breakClassName={"break-me"}
								pageCount={pageCount}
								marginPagesDisplayed={2}
								pageRangeDisplayed={5}
								onPageChange={handlePageClick}
								containerClassName={"pagination"}
								subContainerClassName={"pages pagination"}
								activeClassName={"active"}
							/>
						</>
					)}
				</BodyCard>
			</PageBody>
			<ToastContainer />
		</>
	);
};

export default LoyaltyProgramUserLogs;
