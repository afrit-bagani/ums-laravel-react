import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import Layout from "@/Pages/Layouts/Layout";

createInertiaApp({
    resolve: (name) => {
        return resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx"),
        ).then((page) => {
            page.default.layout =
                page.default.layout || ((page) => <Layout>{page}</Layout>);
            return page;
        });
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: "#4B5563",
    },
});
