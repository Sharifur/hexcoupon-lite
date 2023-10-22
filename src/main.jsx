import React from 'react'
import ReactDOM from 'react-dom/client'
import { I18nProvider } from '@wordpress/react-i18n';
import App from './App'
import "./scss/main.scss";

ReactDOM.createRoot(document.getElementById('vite-react-sample')).render(
	<I18nProvider >
		<App />
	</I18nProvider>
)

