import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig(({ mode }) => {
    const envDir = "../../../";

    Object.assign(process.env, loadEnv(mode, envDir));

    return {
        build: {
            emptyOutDir: true,
        },

        envDir,

        server: {
            host: process.env.VITE_HOST || "localhost",
            port: process.env.VITE_PORT || 5174,
            cors: true,
        },

        plugins: [
            laravel({
                hotFile: "../../../public/product360-vite.hot",
                publicDirectory: "../../../public",
                buildDirectory: "vendor/product360/build",
                input: [
                    "src/Resources/assets/css/product360-viewer.css",
                    "src/Resources/assets/js/product360-viewer.js",
                ],
                refresh: true,
            }),
        ],

        experimental: {
            renderBuiltUrl(filename, { hostId, hostType, type }) {
                if (hostType === "css") {
                    return path.basename(filename);
                }
            },
        },
    };
});
