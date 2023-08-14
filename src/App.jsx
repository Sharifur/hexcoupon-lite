import { useState } from 'react'
import logo from './logo.svg?url';
import './App.css'
import Sidebar from "./components/sidebar";
import MainContainer from "./components/MainContainer";
function App() {
	const [count, setCount] = useState(0)

	return (
		<div className="HxcAppWrapper">
			<Sidebar/>
			<MainContainer/>
		</div>
	)
}

export default App
