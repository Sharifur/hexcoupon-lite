import React from "react";
import {useI18n} from "@wordpress/react-i18n";
export default function DashboardBox({title,text,active}){
	const { __ } = useI18n();
	return (
		<div className={`DashBox ${active ? 'active' : ''}`}>
			<h4 className="title">{__(title)}</h4>
			<p className="para">{__(text)}</p>
		</div>
	);
}
