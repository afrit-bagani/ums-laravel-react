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
import { Check, Plus } from "lucide-react"
import { useRoute } from "ziggy-js"
import { useState } from "react"

export default function CreateBatchDialog() {
    const route = useRoute();
    const [isOpen, setIsOpen] = useState(false);

    const { data, setData, errors, post, processing, reset, clearErrors } = useForm({
        code: '',
        name: '',
        status: 'active',
    });

    function handleSubmit(e) {
        e.preventDefault();

        post(route('admin.batches.store'), {
            onSuccess: () => {
                setIsOpen(false);
                reset();
            }
        });
    }

    function handleOpenChnage(open) {
        setIsOpen(open)

        if(!open){
            clearErrors();
            reset()
        }
    }

    return (
        <Dialog open={isOpen} onOpenChange={handleOpenChnage}>
            <DialogTrigger asChild>
                <Button size="lg" className="p-6"><Plus className="w-4 h-4" />New Batch</Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Create New Batch</DialogTitle>
                    <DialogDescription>
                        Configure a new student batch. Click save when you're done.
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
                                    <Check className="w-4 h-4" /> Create Batch
                                </>)
                            }
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog >
    )
}
