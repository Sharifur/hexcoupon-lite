import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'

if (document.getElementById('vite-react-sample') != null){
	ReactDOM.createRoot(document.getElementById('vite-react-sample')).render(
		<React.StrictMode>
			<App />
		</React.StrictMode>
	)
}
