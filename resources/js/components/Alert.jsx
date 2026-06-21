import { AlertCircleIcon } from "lucide-react"

import {
    Alert as UIAlert,
    AlertDescription,
    AlertTitle,
} from "@/components/ui/alert"

export function Alert({ variant, title, description }) {
    return (
        <UIAlert variant={variant} className="max-w-md">
            <AlertCircleIcon />
            <AlertTitle>{title}</AlertTitle>
            <AlertDescription>
                {description}
            </AlertDescription>
        </UIAlert>
    )
}
