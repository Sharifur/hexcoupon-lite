import { defineConfig } from "vite";
import react from '@vitejs/plugin-react';

const currentFilePath = new URL(import.meta.url).pathname;

const regex = /[^\/\/]+(?=\/wp-content\/plugins\/hex-coupon-for-woocommerce)/;
const match = currentFilePath.match(regex);
var everythingBefore = '';
if (match) {
	 everythingBefore = match[0];
}
const basepath = `/${everythingBefore}/wp-content/plugins/hex-coupon-for-woocommerce/dist/`;
export default defineConfig({
	base: basepath,
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
