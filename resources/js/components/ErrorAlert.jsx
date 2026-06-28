import { AlertCircleIcon } from "lucide-react"

import {
    Alert,
    AlertTitle,
} from "@/components/ui/alert"

export function ErrorAlert({ title }) {
    return (
        <Alert variant="destructive" className="max-w-md">
            <AlertCircleIcon />
            <AlertTitle>{title}</AlertTitle>
        </Alert>
    )
}
