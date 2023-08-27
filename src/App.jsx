import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexMain/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/Dashboard';
import StoreCredit from './components/Pages/StoreCredit/StoreCredit';
import Coupon from './components/Pages/Coupon/Coupon';
import GrantCoupon from './components/GrantCoupon/GrantCoupon';
import MainContainer from './components/HexMain/HexMainContainer/MainContainer';


function App() {

	return (
		<>
			<BrowserRouter>
				<div className="HxcAppWrapper">

					<Sidebar />					
					<MainContainer>
						<Routes>
							<Route element={<Dashboard />} path="/" />
							<Route element={<StoreCredit />} path="/store-credit" />
							<Route element={<Coupon/> } path="/coupon" />
							{/* <Route element={<LoyaltyProgramme /> } path="/loyalty-programme" /> */}
							{/* <Route element={<GiftCards /> } path="/gift-cards" /> */}
							{/* <Route element={<Automations /> } path="/automations" /> */}

							{/* hex grant coupon inner link  */}
							<Route element={<GrantCoupon /> } path="/grant-coupon" />
						</Routes>
					</MainContainer>

				</div>
			</BrowserRouter>
		</>
	)
}

export default App;
