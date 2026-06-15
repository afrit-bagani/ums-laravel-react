import { usePage } from "@inertiajs/react";
import { toast } from "sonner";

export default function AppLayout({ children }) {
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash.message) {
            toast.success(flash.message);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash])
    return <main>{children}</main>
}
