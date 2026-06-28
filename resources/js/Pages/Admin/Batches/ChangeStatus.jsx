import { Badge } from "@/components/ui/badge"
import { RefreshCw } from "lucide-react"


export default function ChangeStatus() {
  return (
    <Badge
    onClick
    className="bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300">
        <RefreshCw data-icon="inline-start" />
        Change Status
    </Badge>
  )
}
