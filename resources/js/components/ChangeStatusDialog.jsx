import { useState } from "react";
import { useForm } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import { Check, RefreshCw } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Field, FieldGroup } from "@/components/ui/field";
import { Label } from "@/components/ui/label";

export default function ChangeStatusDialog({status, route, id, title}) {
    const ziggyRoute = useRoute();
    const [isOpen, setIsOpen] = useState(false);

    const { data, setData, patch, processing} = useForm({
        status: status || 'active',
    });

    function handleSubmit(e) {
        e.preventDefault();

        patch(ziggyRoute(route, id), {
            onSuccess: () => {
                setIsOpen(false);
            }
        });
    }

    function handleOpenChange(open) {
        setIsOpen(open)
        
        setData({
          status: status || 'active',
        });
    }
  return (
    <Dialog open={isOpen} onOpenChange={handleOpenChange}>
            <DialogTrigger asChild>
                 <button
                    type="button"
                    className="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-sky-50 text-sky-700 hover:bg-sky-100 dark:bg-sky-950/50 dark:text-sky-300 dark:hover:bg-sky-900 border border-transparent hover:border-sky-200 dark:hover:border-sky-800 transition-colors"
                    title="Change Status"
                >
                    <RefreshCw className="w-3.5 h-3.5" data-icon="inline-start" /> Change Status
                </button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Edit {title} Status</DialogTitle>
                    <DialogDescription>
                        Update the status for this {title.toLowerCase()}.
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit}>
                    <FieldGroup>
                        <Field>
                            <Label htmlFor="status">Select {title} Status</Label>
                            <Select id='status'
                                value={data.status}
                                onValueChange={(value) => setData('status', value)}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Active" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="active">Active</SelectItem>
                                        <SelectItem value="inactive">Inactive</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldGroup>
                    <DialogFooter className="mt-6">
                        <DialogClose asChild>
                            <Button variant="outline">Cancel</Button>
                        </DialogClose>
                        <Button
                            type="submit"
                            disabled={processing}>
                            {processing ? 'Saving...' : (
                                <>
                                    <Check className="w-4 h-4" /> Save Changes
                                </>)
                            }
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog >
  )
}