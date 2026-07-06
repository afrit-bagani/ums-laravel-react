import ChangeStatusDialog from "@/components/ChangeStatusDialog";
import EditCourseDialog from "./EditCourseDialog";

export default function ActionRow({ course, programmes }) {
  return (
    <div className="flex flex-wrap gap-2">
      <ChangeStatusDialog status={course.status} route="admin.courses.update-status" id={course.course_id} title="Course" />
      <EditCourseDialog programmes={programmes} course={course} />
    </div>
  )
}
