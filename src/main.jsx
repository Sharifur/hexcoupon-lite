import React from 'react'
import ReactDOM from 'react-dom/client'
import { I18nProvider } from '@wordpress/react-i18n';
import App from './App'
import './index.css';
import "./scss/main.scss";

// import { createI18n }  from '@wordpress/react-i18n';

// const i18n = createI18n();
ReactDOM.createRoot(document.getElementById('vite-react-sample')).render(
	<I18nProvider >
		<App />
	</I18nProvider>
)

