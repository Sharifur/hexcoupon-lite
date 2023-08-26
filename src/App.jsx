import { useState } from 'react'
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/Dashboard';
import StoreCredit from './components/Pages/StoreCredit/StoreCredit';
import Coupon from './components/Pages/Coupon/Coupon';
// import MainContainer from './components/HexMainContainer/MainContainer';


function App() {
	const [count, setCount] = useState(0);

	return (
		<BrowserRouter>
			<div className="HxcAppWrapper">
				<Sidebar />
				{/* <MainContainer /> */}
				<Routes>
					<Route element={<Dashboard />} path="/" />
					<Route element={<StoreCredit />} path="/store-credit" />
					<Route element={<Coupon/> } path="/coupon" />
					{/* <Route element={<LoyaltyProgramme /> } path="/loyalty-programme" /> */}
					{/* <Route element={<GiftCards /> } path="/gift-cards" /> */}
					{/* <Route element={<Automations /> } path="/automations" /> */}
				</Routes>
			</div>
		</BrowserRouter>
	)
}

export default App;
