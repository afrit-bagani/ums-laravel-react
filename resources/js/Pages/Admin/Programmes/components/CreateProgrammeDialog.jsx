import { useState } from "react"
import { useForm } from "@inertiajs/react"
import { Check, Plus } from "lucide-react"
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
import { ErrorAlert } from "@/components/ErrorAlert"
import { Button } from "@/components/ui/button"
import { useRoute } from "ziggy-js"

export default function CreateProgrammeDialog() {
  const route = useRoute();
  const [isOpen, setIsOpen] = useState(false);

  const { data, setData, processing, errors, clearErrors, post, reset } = useForm({
    'code': '',
    'name': '',
    'status': 'active'
  });

  const handleFormSubmit = (e) => {
    e.preventDefault();
    post(route('admin.programmes.store'), {
      onSuccess: () => {
        setIsOpen(false);
        reset()
      }
    });

  }

  const handleOpenChange = (open) => {
    setIsOpen(open)

    if (!open) {
      clearErrors();
      reset()
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={handleOpenChange}>
      <DialogTrigger asChild>
        <Button size="lg" className='p-6'><Plus className="w-4 h-4" /> Create Programme</Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <form onSubmit={handleFormSubmit}>
          <DialogHeader>
            <DialogTitle>Create New Programme</DialogTitle>
            <DialogDescription>
              Configure a new Programme. Click save when you&apos;re
              done.
            </DialogDescription>
          </DialogHeader>
          <FieldGroup>
            <Field>
              <Label htmlFor="code">Programme Code</Label>
              <Input id="code" name="code" value={data.code} onChange={(e) => setData('code', e.target.value)} placeholder='Enter Programme Code' />
              {errors?.code && <ErrorAlert title={errors.code} />}
            </Field>
            <Field>
              <Label htmlFor="name">Programme Name</Label>
              <Input id="name" name="name" value={data.name} onChange={(e) => setData('name', e.target.value)} placeholder='Enter Programme Name' />
              {errors?.name && <ErrorAlert title={errors.name} />}
            </Field>
            <Field>
              <Label htmlFor="status">Select Programme Status</Label>
              <Select value={data.status} onValueChange={(value) => setData('status', value)} defaultValue={data.status}>
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
          <DialogFooter className='mt-6'>
            <DialogClose asChild>
              <Button type="button" variant="outline">Cancel</Button>
            </DialogClose>
            <Button type="submit" disabled={processing}>{processing ? 'Saving...' : <><Check className="w-4 h-4" /> Create Programme</> }</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}