import { AlertCircleIcon } from "lucide-react"
import {
    Alert,
    AlertTitle,
} from "@/components/ui/alert"

function ErrorAlert({ title }) {
    return (
        <Alert variant="destructive" className="max-w-md">
            <AlertCircleIcon />
            <AlertTitle>{title}</AlertTitle>
        </Alert>
    )
}

export { ErrorAlert };
export default ErrorAlert;
