import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            valet: {
                configPath: "C:\\Users\\aus\\.config\\valet\\config.json",
            },
            refresh: true,
            valetTls: false,
        }),
    ],
});
