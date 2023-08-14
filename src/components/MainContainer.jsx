import React from "react";
import DashboardBox from "./DashboardBox";



export default function MainContainer(){

	return (
		<div className="MainContainer">
			<div className="hex-grid-container">
				<div className="grid-item">
					<DashboardBox title="126K" text="S. Credit Granted"/>
				</div>
				<div className="grid-item">
					<DashboardBox title="34K" text="S. Credit Granted as Refunds"/>
				</div>
				<div className="grid-item">
					<DashboardBox title="119K" text="S. Credit Redeemed"/>
				</div>
				<div className="grid-item">
					<DashboardBox title="6,900" text="S. Credit Unclaimed"/>
				</div>
			</div>
		</div>
	);
}
