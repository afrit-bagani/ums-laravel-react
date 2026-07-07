# Project Progress & Changelog

This file tracks the ongoing features, refactoring, and UI enhancements made to the UMS project during our pairing sessions.

## [2026-06-29] Admin Dashboard & Batch Management

### 🎨 UI & Layout Enhancements
- **Compact SaaS Dashboard Look:** Refined the overall `AdminDashboardLayout`, slimming down the `Sidebar` width (`w-64` -> `w-56`), shrinking menu icons, and tightening vertical padding for a sleeker, more professional data-heavy view.
- **Shadcn Table Integration:** Completely swapped the raw HTML tables in `BatchTable.jsx` over to Shadcn UI Table components (`Table`, `TableHeader`, `TableBody`, `TableCell`, etc.) while preserving all of our custom Tailwind aesthetics.
- **Scrollable Data Tables:** Configured the table to scroll vertically (`max-h-[65vh]`) with a `sticky` header, ensuring large datasets won't blow out the page layout or push pagination off-screen.
- **Shadcn Select Integration:** Replaced native HTML `<select>` dropdowns with Shadcn `<Select>` components for the Status Filter and Bulk Action bar in `Index.jsx`. Styled them perfectly to match the adjacent search inputs.

### ⚙️ Refactoring & Logic Updates
- **Set-based Selection Logic:** Migrated the `selectedIds` state from a standard Array to a `Set` for `O(1)` lookup performance.
- **Bulletproof "Select All":** Rewrote the "Select All" checkbox logic to use `batches.data.every(b => selectedIds.has(b.batch_id))`. This prevents dangerous bugs where checking the box on Page 1 could incorrectly show as checked on Page 2 or during filtered searches.
- **Event Handler Fixes:** Adjusted Shadcn `<Select>` component event handlers to use `(value) => ...` instead of the standard `(e) => e.target.value` which caused breaking bugs.
- **Prop Warning Fixes:** Resolved React DOM warnings (`wrapperClassName`) on the Shadcn Table by leveraging Tailwind arbitrary variants (`[&_div[data-slot=table-container]]:...`) to target the inner wrapper directly without passing invalid props.

## [2026-07-03] Programmes Module & Form Enhancements

### 🚀 Feature Additions
- **Programmes Module Migration:** Built out the entire `Programmes` module (`Index`, `ProgrammeTable`, `CreateProgrammeDialog`, `EditProgrammeDialog`) mirroring the robust architecture created for `Batches`.
- **Soft Deletion Architecture:** Established that standard `DELETE` and `PATCH` actions are best-practice for 'soft-deleting' resources (setting `status = inactive`), completely avoiding database constraint headaches.

### 🐛 Bug Fixes & Refactoring
- **Radix UI Portal Form Bug:** Fixed a classic Shadcn/Radix issue where `<form>` tags wrapped around `<DialogContent>` break form submissions. Moved the `<form>` inside the `DialogContent` so submit buttons wire up correctly.
- **Naming Collisions Resolved:** Fixed a prop shadowing issue in `ChangeStatusDialog` where `const route = useRoute()` collided with the `route` prop, by cleanly renaming the variable to `ziggyRoute`.
- **React DOM Warning Fixed:** Corrected the `disable={processing}` typo to the standard HTML boolean attribute `disabled={processing}` to prevent React stringification warnings.
- **Controller Variable Cleanup:** Corrected leftover copy-paste artifacts (`$batches` -> `$programmes`) in `ProgrammeController.php` for cleaner, self-documenting code.

## [2026-07-06] Courses Module, SQL Optimization & UI Polish

### 🚀 Courses Module & SQL Optimization
- **Raw SQL & Joins:** Rewrote `CourseController.php` methods to use raw `DB::select`, `DB::insert`, and `DB::update` for maximum performance instead of standard Eloquent ORM. 
- **Inner Join Implementation:** Added an `INNER JOIN` to the course fetch query (`JOIN programme_master p ON c.programme_id = p.programme_id`) to successfully display the associated `programme_code` in the frontend table.
- **SQL Ambiguous Column Fix:** Prepended table aliases (`c.code`, `c.status`) to `WHERE` clauses to prevent ambiguous column crashes during search and filter operations.
- **Cache Serialization Fix:** Fixed a fatal `__PHP_Incomplete_Class_Name` error caused by Laravel file cache improperly serializing a `Collection` of `stdClass` objects. Resolved this by mapping to raw arrays and updating the cache key to guarantee clean data arrays for Inertia.

### 🐛 Bug Fixes & React Hydration
- **React Hydration Mismatch:** Hunted down a fatal hydration mismatch error in `CourseTable.jsx` that was caused by a stray `2` character rendering between two `TableCell` elements, which created an invalid HTML DOM structure.
- **Validation Rule Typo:** Fixed an internal Laravel validation crash (`Undefined array key 1`) in the `bulkUpdateStatus` method where `Rule::exists('course_master, course_id')` was incorrectly passed as a single string instead of two distinct arguments.
- **UI Error State Fixes:** Cleaned up frontend error displays in `CreateCourseDialog` where copy-paste typos mapped `{errors.status}` to the `programme_id` field.

### 🎨 Table Aesthetic Polish
- **Uniform Action Columns:** Refined the alignment of the `Action` columns across the entire Admin Dashboard (`BatchTable`, `ProgrammeTable`, `CourseTable`). Removed `text-center` from the `TableHead` and wrapped the row buttons in `<div className="flex justify-start">` to guarantee crisp, uniform left-alignment.
- **Shadcn Typography Inheritance:** Fixed a styling issue in `CourseTable` where `<TableCell>` typography classes (`font-semibold text-gray-900`) weren't applying to text inside `<Badge>` components. Explicitly passed the classes into the `Badge` variant to override the defaults.

## [2026-07-07] Subjects Module & Premium UI Modernization

### 🚀 Feature Additions & Backend Excellence
- **Subject Module Creation:** Built out the full `Subjects` module (`Index`, `Create`, `Edit`, `Show`, `SubjectTable`) using Inertia, React, and strict raw SQL for ultimate performance.
- **Strict SQL Optimization (`GEMINI.md`):** Adhered strictly to the custom architecture rules by bypassing Eloquent. Used parameterized `DB::select`, `DB::insert`, and `DB::update` for all Controller methods, ensuring completely injection-safe and lightning-fast database interactions.
- **Complex JOIN Integration:** Upgraded the `show` method with an advanced multi-table `LEFT JOIN` (pulling from `course_master`, `programme_master`, and `users` tables) to provide human-readable references for academic hierarchies and record audits without N+1 overhead.
- **Validation Refactoring:** Replaced all string-based `$request->validate()` rules with the cleaner, array-based `Rule` object syntax (e.g., `Rule::exists()`, `Rule::unique()->ignore()`), preventing string-splitting bugs and improving maintainability.

### 🎨 Premium UI & Shadcn Polish
- **Glassmorphic "Show" Page:** Completely overhauled the `ShowSubject` view to feature a stunning, state-of-the-art layout. Utilized a vivid gradient background, `backdrop-blur-md` glassmorphism headers, and interactive hover states (`hover:shadow-xl`, `hover:scale-105`) to create a "wow" factor.
- **Marks Configuration Display:** Designed a beautiful 3-column split for Subject Marks (Internal, Theory, Practical) utilizing custom Shadcn badges and soft, color-coded `lucide-react` icons (orange, blue, teal).
- **Record Audit Sidebar:** Implemented a clean sidebar panel to track "Created By" and "Updated By" timestamps using dynamic `new Date().toLocaleString()` formats and user relationship mapping.
- **Dynamic Form Population:** Bootstrapped the Inertia `useForm` hook in the `Edit` page to smoothly initialize with existing database data (including complex dependent dropdowns for Programmes -> Courses).

## [2026-07-07] Students Module Architecture & Backend Logic

### 🏗️ Advanced Database Architecture
- **Multi-Table Normalization:** Designed a scalable 4-table architecture (`student_profiles`, `student_paper_selections`, `student_documents`, `student_payments`) to handle the massive student registration wizard without bloating a single table.
- **Foreign Key Refactoring:** Switched the primary foreign key connection from `user_id` to `student_profile_id` for all student sub-tables, ensuring strict domain isolation between Admin/Auth and Student Data.
- **Robust Mock Data (Seeders):** Engineered a comprehensive `StudentProfileSeeder` that perfectly orchestrates dummy data across all 4 tables simultaneously, generating realistic `registration_numbers` (e.g., `2024-4039`) derived from `batch_master` relationships.

### 🚀 Backend & SQL Optimization
- **Controller Splitting:** Created 4 distinct controllers (`StudentProfileController`, `StudentPaperSelectionController`, etc.) mapping directly to the 4 wizard tabs, guaranteeing zero "Mega-Controller" technical debt.
- **Array-based Dynamic SQL Building:** Replaced simple `1=1` concatenation with the professional `$whereClauses[]` array and `implode(' AND ', ...)` pattern for significantly cleaner, scalable raw SQL search generation.
- **Symmetric LEFT JOINs:** Upgraded the `StudentProfileController@index` method to use explicit `LEFT JOIN`s for related programme/course/batch data. Carefully synced the exact same `LEFT JOIN`s to the `COUNT(*)` pagination query to guarantee it won't crash when search filters are added.
