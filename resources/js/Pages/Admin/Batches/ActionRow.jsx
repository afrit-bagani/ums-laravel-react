import ChangeStatus from "./ChangeStatus";
import EditBatchDialog from "./EditBatchDialog";

export default function ActionRow({batch}) {
    return (
        <div className="flex flex-wrap gap-2">
            <button
                type="button"
                onClick={() => openSingleStatusModal(batch)}
                title="Change status"
            >
                <ChangeStatus />
            </button>
            <EditBatchDialog batch={batch}/>
        </div>
    )
}
