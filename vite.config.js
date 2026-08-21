import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath, URL } from "node:url";

const dependencyChunk = (id) => {
    if (id.includes("/node_modules/apexcharts/")) {
        return "vendor-charts";
    }

    if (id.includes("/node_modules/axios/")) {
        return "vendor-http";
    }

    if (
        id.includes("/node_modules/alpinejs/") ||
        id.includes("/node_modules/flyonui/dist/")
    ) {
        return "vendor-ui";
    }

    return undefined;
};

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/tinymce-init.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: [
            {
                find: "ApexCharts",
                replacement: "apexcharts",
            },
            {
                find: "../../img/illustrations/auth-background-2.png",
                replacement: fileURLToPath(
                    new URL(
                        "./public/assets/img/illustrations/auth-background-2.png",
                        import.meta.url,
                    ),
                ),
            },
        ],
    },
    build: {
        chunkSizeWarningLimit: 650,
        rollupOptions: {
            output: {
                manualChunks: dependencyChunk,
            },
        },
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
