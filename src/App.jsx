import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexMain/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/Dashboard';
import MainContainer from './components/HexMain/HexMainContainer/MainContainer';
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
						</Routes>
					</MainContainer>

				</div>
			</BrowserRouter>
		</>
	)
}
export default App;
