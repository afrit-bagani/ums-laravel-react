import { AlertCircleIcon } from "lucide-react"

import {
    Alert,
    AlertTitle,
} from "@/components/ui/alert"

export function ErrorAlert({ title }) {
    return (
        <Alert variant="destrutive" className="max-w-md">
            <AlertCircleIcon />
            <AlertTitle>{title}</AlertTitle>
        </Alert>
    )
}
