import { ErrorAlert } from "@/components/ErrorAlert"
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { Field, FieldGroup } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useForm } from "@inertiajs/react"
import { Badge, Check, Edit, } from "lucide-react"
import { useRoute } from "ziggy-js"
import { useState } from "react"

export default function EditBatchDialog({batch}) {
    const route = useRoute();
    const [isOpen, setIsOpen] = useState(false);

    const { data, setData, errors, patch, processing, reset, clearErrors } = useForm({
        code: batch?.code || '',
        name: batch?.name || '',
        status: batch?.status || 'active',
    });

    function handleSubmit(e) {
        e.preventDefault();

        patch(route('admin.batches.update', batch?.batch_id), {
            onSuccess: () => {
                setIsOpen(false);
            }
        });
    }

    function handleOpenChange(open) {
        setIsOpen(open)

        if (!open) {
            clearErrors();
        } else {
            setData({
                code: batch?.code || '',
                name: batch?.name || '',
                status: batch?.status || 'active',
            });
        }
    }

    return (
        <Dialog open={isOpen} onOpenChange={handleOpenChange}>
            <DialogTrigger asChild>
                 <button
                    type="button"
                    className="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-950/50 dark:text-green-300 dark:hover:bg-green-900 border border-transparent hover:border-green-200 dark:hover:border-green-800 transition-colors"
                    title="Edit batch"
                >
                    <Edit className="w-3.5 h-3.5" data-icon="inline-start" /> Edit
                </button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Edit Batch</DialogTitle>
                    <DialogDescription>
                        Update the details for this student batch.
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit}>
                    <FieldGroup>
                        <Field>
                            <Label htmlFor="code">Batch Code</Label>
                            <Input
                                id="code"
                                type="text"
                                name="code"
                                value={data.code}
                                onChange={(e) => setData('code', e.target.value)}
                                placeholder='Enter Batch code'
                            />
                            {errors.code && <ErrorAlert title={errors.code} />}
                        </Field>
                        <Field>
                            <Label htmlFor="name">Batch Name</Label>
                            <Input
                                id="name"
                                type='text'
                                name="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Enter batch name"
                            />
                            {errors.name && <ErrorAlert title={errors.name} />}
                        </Field>
                        <Field>
                            <Label htmlFor="status">Select Batch Status</Label>
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
                            {errors.status && <ErrorAlert title={errors.status} />}
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
