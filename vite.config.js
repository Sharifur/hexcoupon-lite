import { defineConfig } from "vite";
import react from '@vitejs/plugin-react';

export default defineConfig({
	base: '/wp-content/plugins/hexcoupon-lite/dist/',
	build: {
		rollupOptions: {
			output: {
				entryFileNames: `assets/[name].js`,
				chunkFileNames: `assets/[name].js`,
				assetFileNames: `assets/[name].[ext]`
			}
		}
	},
	plugins: [
		react(),
		{
			name: "php",
			handleHotUpdate({ file, server }) {
				if (file.endsWith(".php")) {
					server.ws.send({ type: "full-reload", path: "*" });
				}
			},
		},
	],
});
