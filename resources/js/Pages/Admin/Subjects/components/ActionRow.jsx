import { router } from "@inertiajs/react";
import { route } from "ziggy-js";
import ChangeStatusDialog from "@/components/ChangeStatusDialog";
import { Edit, Eye } from "lucide-react";

export default function ActionRow({ subject }) {
  return (
    <div className="flex flex-wrap gap-2">
      <ChangeStatusDialog status={subject.status} route="admin.subjects.update-status" id={subject.subject_id} title="Subject" />
      <button
        type="button"
        onClick={() => router.get(route('admin.subjects.edit', subject.subject_id))}
        className="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-950/50 dark:text-green-300 dark:hover:bg-green-900 border border-transparent hover:border-green-200 dark:hover:border-green-800 transition-colors"
        title="Edit Subject"
      >
        <Edit className="w-3.5 h-3.5" data-icon="inline-start" /> Edit
      </button>
      <button
        type="button"
        onClick={() => router.get(route('admin.subjects.show', subject.subject_id))}
        className="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-yellow-50 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-950/50 dark:text-yellow-300 dark:hover:bg-yellow-900 border border-transparent hover:border-yellow-200 dark:hover:border-yellow-800 transition-colors"
        title="View Subject"
      >
        <Eye className="w-3.5 h-3.5" data-icon="inline-start" /> View
      </button>
    </div>
  )
}
