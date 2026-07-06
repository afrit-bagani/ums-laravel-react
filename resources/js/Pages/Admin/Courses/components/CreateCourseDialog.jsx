import { useState } from "react"
import { useForm } from "@inertiajs/react"
import { useRoute } from "ziggy-js";
import { Check, Plus } from "lucide-react";
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
} from "@/components/ui/select";
import { Field, FieldGroup } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function CreateCourseDialog({ programmes }) {
  const route = useRoute();
  const [isOpen, setIsOpen] = useState(false);

  const { data, setData, errors, post, processing, reset, clearErrors } = useForm({
    programme_id: '',
    code: '',
    name: '',
    status: 'active',
  });

  function handleSubmit(e) {
    e.preventDefault();

    post(route('admin.courses.store'), {
      onSuccess: () => {
        setIsOpen(false);
        reset();
      }
    });
  }

  function handleOpenChange(open) {
    setIsOpen(open)

    if (!open) {
      clearErrors();
      reset()
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={handleOpenChange}>
      <DialogTrigger asChild>
        <Button size="lg" className="p-6"><Plus className="w-4 h-4" />New Course</Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>Create New Course</DialogTitle>
          <DialogDescription>
            Create a new course. Click save when you're done.
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit}>
          <FieldGroup>
            <Field>
              <Label htmlFor="programme_id">Select Programme</Label>
              <Select id='programme_id'
                value={data.programme_id}
                onValueChange={(value) => setData('programme_id', value)}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Select Programme" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    {programmes.map(programme => (
                      <SelectItem key={programme.programme_id} value={programme.programme_id.toString()}>
                        {programme.name}
                      </SelectItem>
                    ))}
                  </SelectGroup>
                </SelectContent>
              </Select>
              {errors.programme_id && <ErrorAlert title={errors.programme_id} />}
            </Field>
            <Field>
              <Label htmlFor="code">Course Code</Label>
              <Input
                id="code"
                name="code"
                value={data.code}
                onChange={(e) => setData('code', e.target.value)}
                placeholder='Enter Course code'
              />
              {errors.code && <ErrorAlert title={errors.code} />}
            </Field>
            <Field>
              <Label htmlFor="name">Course Name</Label>
              <Input
                id="name"
                name="name"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                placeholder="Enter course name"
              />
              {errors.name && <ErrorAlert title={errors.name} />}
            </Field>
            <Field>
              <Label htmlFor="status">Select Course Status</Label>
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
              <Button type="button" variant="outline">Cancel</Button>
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