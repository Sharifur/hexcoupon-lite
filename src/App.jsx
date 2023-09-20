import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexMain/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/Dashboard';
import StoreCredit from './components/Pages/StoreCredit/StoreCredit';
import Coupon from './components/Pages/Coupon/Coupon';
import GrantCoupon from './components/GrantCoupon/GrantCoupon';
import MainContainer from './components/HexMain/HexMainContainer/MainContainer';
import CouponSettings from './components/HexCoupon/CouponSettings';


function App() {
	const windowLocation = window.location.pathname;
	const windowParams = window.location.search;

	return (
		<>
			<BrowserRouter basename={windowLocation}>
				<div className="HxcAppWrapper">

					<Sidebar searchParam={windowParams} />
					<MainContainer>
						<Routes>
							<Route element={<Dashboard />} path="/" />
							<Route element={<StoreCredit />} path="/store-credit" />
							<Route element={<Coupon /> } path="/coupon" />
							{/* <Route element={<LoyaltyProgramme /> } path="/loyalty-programme" /> */}
							{/* <Route element={<GiftCards /> } path="/gift-cards" /> */}
							{/* <Route element={<Automations /> } path="/automations" /> */}

							{/* hex coupon inner link  */}
							<Route element={<GrantCoupon /> } path="/grant-coupon" />
							<Route element={<CouponSettings /> } path="/coupon-settings" />

						</Routes>
					</MainContainer>

				</div>
			</BrowserRouter>
		</>
	)
}

export default App;
