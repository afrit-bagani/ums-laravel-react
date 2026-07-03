import ChangeStatusDialog from "@/components/ChangeStatusDialog";
import EditBatchDialog from "./EditBatchDialog";

export default function ActionRow({ batch }) {
    return (
        <div className="flex flex-wrap gap-2">
            <ChangeStatusDialog status={batch.status} route="admin.batches.change-status" id={batch.batch_id} title="Batch" />
            <EditBatchDialog batch={batch} />
        </div>
    )
}
