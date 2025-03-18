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
    // server: {
    //     host: "192.168.101.192", // Ganti dengan IP server
    //     port: 5173, // Port default Vite
    //     strictPort: true,
    //     hmr: {
    //         host: "192.168.101.192",
    //         protocol: "ws", // Gunakan WebSocket, bukan Secure WebSocket (wss)
    //     },
    // },
});
