import { AlertCircleIcon } from "lucide-react"

import {
    Alert,
    AlertDescription,
    AlertTitle,
} from "@/components/ui/alert"

export function Alert({ variant, title, description }) {
    return (
        <Alert variant={variant} className="max-w-md">
            <AlertCircleIcon />
            <AlertTitle>{title}</AlertTitle>
            <AlertDescription>
                {description}
            </AlertDescription>
        </Alert>
    )
}
