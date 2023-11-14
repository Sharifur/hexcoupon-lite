import { HashRouter, Routes, Route } from 'react-router-dom';
import Sidebar from './components/HexMain/HexSidebar/sidebar';
import Dashboard from './components/Pages/Dashboard/Dashboard';
import MainContainer from './components/HexMain/HexMainContainer/MainContainer';
function App() {

	return (
		<>
			<HashRouter>
				<div className="HxcAppWrapper">
					<Sidebar />
					<MainContainer>
						<Routes>
							<Route element={<Dashboard />} path="/" />
						</Routes>
					</MainContainer>
				</div>
			</HashRouter>
		</>
	)
}
export default App;
