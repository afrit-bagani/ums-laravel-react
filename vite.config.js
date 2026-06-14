import inertia from "@inertiajs/vite";
import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";
import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.jsx"],
            refresh: true,
            fonts: [
                bunny("Instrument Sans", {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            "ziggy-js": path.resolve("vendor/tightenco/ziggy"),
        },
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
