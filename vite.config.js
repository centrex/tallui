import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.js"],
            hotFile: "public/tallui.hot",
            refresh: [
                "resources/views/**",
                "src/**",
                "routes/**",
                "config/**",
                "tests/**",
            ],
        }),
    ],
    server: {
        host: "127.0.0.1",
        port: 5174,
        strictPort: true,
        cors: true,
    },
});
