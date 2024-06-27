import React, {useEffect, useState} from 'react';
import { Skeleton } from "../../Skeleton";

const Automation = () => {
	const [isLoading, setIsLoading] = useState(true);

	useEffect(() => {
		// Simulating some loading delay (e.g., fetching data)
		const timeout = setTimeout(() => {
			setIsLoading(false);
		}, 200); // Adjust the timeout value as needed

		return () => clearTimeout(timeout);
	}, []); // Empty dependency array to run effect only once on mount

	return (
		<>
			{isLoading ? (
				<Skeleton height={500} radius={10} />
			) : (
				<>
				</>
			)}

			<div className="coming-soon-wrapper">
				<h1>Coming Soon</h1>
				<p>This feature is currently under development. Stay tuned for updates!</p>
			</div>
		</>
	)
}

export default Automation;
