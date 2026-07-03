import ChangeStatusDialog from "@/components/ChangeStatusDialog";
import EditProgrammeDialog from "./EditProgrammeDialog";

export default function ActionRow({ programme }) {
    return (
        <div className="flex items-center gap-2">
            <ChangeStatusDialog status={programme.status} route={'admin.programmes.change-status'} id={programme.programme_id} title="Programme" />
            <EditProgrammeDialog programme={programme} />
        </div>
    )
}
