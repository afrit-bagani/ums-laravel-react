import { usePage } from "@inertiajs/react";
import { useEffect } from "react";
import { toast } from "sonner";

export default function FlashMessageListner() {
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash?.message) {
            toast.success(flash.message);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash])

    return null;
}
