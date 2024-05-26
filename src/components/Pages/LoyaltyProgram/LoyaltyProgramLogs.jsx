import React, { useEffect, useState } from "react";
import axios from "axios";
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
import { Link } from "react-router-dom";
import ReactPaginate from "react-paginate";
import { useI18n } from "@wordpress/i18n"

const LoyaltyProgramLogs = () => {
	const { nonce, ajaxUrl } = loyaltyProgramLogs;
	const [isLoading, setIsLoading] = useState(true);
	const [storeCreditFullLogs, setStoreCreditFullLogs] = useState([]);
	const [currentPage, setCurrentPage] = useState(0);
	const [filterOption, setFilterOption] = useState("all");
	const [searchQuery, setSearchQuery] = useState("");
	const itemsPerPage = 10;

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: "point_loyalty_program_logs",
				},
				headers: {
					"Content-Type": "application/json",
				},
			})
			.then(({ data }) => {
				if (data && data.pointsLoyaltyLogs) {
					setStoreCreditFullLogs(data.pointsLoyaltyLogs);
				} else {
					console.error("Invalid data format", data);
				}
			})
			.catch((error) => {
				console.error("Error:", error);
			})
			.finally(() => setIsLoading(false));
	}, [nonce, ajaxUrl]);

	const getReason = (reasonCode) => {
		switch (reasonCode) {
			case "0":
				return <span className="px-2.5 py-2 bg-green-100 text-green-800 w-full text-center">Signup</span>;
			case "1":
				return <span className="px-2.5 py-2 bg-cyan-100 text-cyan-800 w-full text-center">Referral</span>;
			case "2":
				return <span className="px-2.5 py-2 bg-indigo-100 text-indigo-800 w-full text-center">Purchase</span>;
			default:
				return <span className="px-2.5 py-2 bg-green-100 text-green-800 w-full text-center">Unknown</span>;
		}
	};

	const handlePageClick = (data) => {
		setCurrentPage(data.selected);
	};

	const handleFilterChange = (event) => {
		setFilterOption(event.target.value);
		setCurrentPage(0); // Reset to first page on filter change
	};

	const handleSearchChange = (event) => {
		setSearchQuery(event.target.value);
		setCurrentPage(0); // Reset to first page on search
	};

	const filteredLogs = storeCreditFullLogs.filter((log) => {
		const matchesFilter = filterOption === "all" || getReason(log.reason).toLowerCase() === filterOption;
		const matchesSearch = log.user_name.toLowerCase().includes(searchQuery.toLowerCase());
		return matchesFilter && matchesSearch;
	});

	const offset = currentPage * itemsPerPage;
	const currentLogs = filteredLogs.slice(offset, offset + itemsPerPage);
	const pageCount = Math.ceil(filteredLogs.length / itemsPerPage);

	return (
		<>
			<PageBody>
				<BodyCard className="p-0">
					<BodyCardHeader className="p-4" isFlex={true}>
						<BodyCardHeaderLeft isFlex={true}>
							<BodyCardHeaderLeftItem>
								<BodyCardHeaderTItle>Loyalty Program Logs</BodyCardHeaderTItle>
							</BodyCardHeaderLeftItem>
						</BodyCardHeaderLeft>
						<BodyCardHeaderRight>
							<ButtonWrapper isFlex={true}>
								<select
									value={filterOption}
									onChange={handleFilterChange}
									className="customSelect py-2.5 pl-4 pr-4 h-[34px] !ring-1 !border-transparent !ring-[var(--hex-border-color)] text-md !text-[var(--hex-paragraph-color)] focus:!ring-[var(--hex-main-color-one)] focus:!border-transparent"
								>
									<option value="all">All</option>
									<option value="signup">Signup</option>
									<option value="referral">Referral</option>
									<option value="purchase">Purchase</option>
								</select>
								<input
									type="text"
									placeholder="Search by Name or display name"
									value={searchQuery}
									onChange={handleSearchChange}
									className="py-2.5 pl-4 pr-4 h-[34px] w-[170px] !ring-1 !border-transparent !ring-[var(--hex-border-color)] text-md !text-[var(--hex-paragraph-color)] focus:!ring-[var(--hex-main-color-one)] focus:!border-transparent"
								/>
							</ButtonWrapper>
						</BodyCardHeaderRight>
					</BodyCardHeader>
					{isLoading ? (
						<Skeleton height={500} radius={10} />
					) : (
						<>
							<Table className="border text-left">
								<THead>
									<Th>Customer Name</Th>
									<Th>Email</Th>
									<Th>Points</Th>
									<Th>Reason</Th>
									<Th>Referrer ID</Th>
									<Th>Converted Credit</Th>
									<Th>Conversion Rate</Th>
									<Th>Date</Th>
								</THead>
								<TBody>
									{currentLogs.length > 0 ? (
										currentLogs.map((log, index) => (
											<tr key={index}>
												<td>
													<Link
														to={`/loyalty-program-user-logs/${log.user_id}`}
														style={{ textDecoration: "underline" }}
													>
														{log.user_name}
													</Link>
												</td>
												<td>{log.user_email}</td>
												<td>{log.points}</td>
												<td>{getReason(log.reason)}</td>
												<td>{log.referee_id ? log.referee_id : "NA"}</td>
												<td>{log.converted_credit}</td>
												<td>{log.conversion_rate}</td>
												<td>{log.created_at}</td>
											</tr>
										))
									) : (
										<tr style={{textAlign:"center"}}>
											<td colSpan="8">No logs available</td>
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

export default LoyaltyProgramLogs;
