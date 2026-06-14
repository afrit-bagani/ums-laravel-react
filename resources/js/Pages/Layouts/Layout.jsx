import Sidebar from "./Sidebar";

export default function Layout({ showSidebar = true, children }) {
    return (
        <>
            {showSidebar && <Sidebar />}
            <main>{children}</main>
        </>
    );
}
