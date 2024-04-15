import { HashRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexMain/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/index';
import MainContainer from './components/HexMain/HexMainContainer/MainContainer';
import { SidebarProvider } from "./components/context/SidebarContext";
import StoreCreditSettings from "./components/Pages/StoreCredit/StoreCreditSettings";
import StoreCreditLogs from "./components/Pages/StoreCredit/StoreCreditLogs";
import StoreCreditUserLogs from "./components/Pages/StoreCredit/StoreCreditUserLogs";
import GiveNewCredit from "./components/Pages/StoreCredit/GiveNewCredit";

function App() {
	return (
		<>
			<HashRouter>
				<SidebarProvider>
					<div className="HxcAppWrapper">
						<Sidebar />
						<MainContainer>
							<Routes>
								<Route element={<Dashboard />} path="/" />
								<Route element={<StoreCreditSettings />} path="/store-credit/store-credit-settings" />
								<Route element={<StoreCreditLogs />} path="/store-credit/store-credit-logs" />
								<Route element={<StoreCreditUserLogs />} path="/store-credit-user-logs/:userId" />
								<Route element={<GiveNewCredit />} path="/store-credit/give-new-credit" />
							</Routes>
						</MainContainer>
					</div>
				</SidebarProvider>
			</HashRouter>
		</>
	)
}
export default App;
