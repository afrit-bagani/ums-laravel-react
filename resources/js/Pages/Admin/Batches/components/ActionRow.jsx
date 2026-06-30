import ChangeStatusDialog from "./ChangeStatusDialog";
import EditBatchDialog from "./EditBatchDialog";

export default function ActionRow({ batch }) {
    return (
        <div className="flex flex-wrap gap-2">
            <ChangeStatusDialog batch={batch} />
            <EditBatchDialog batch={batch} />
        </div>
    )
}
