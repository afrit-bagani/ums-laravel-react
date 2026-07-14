# Gemini Codebase Understanding & Guidelines

This file serves as a living document to track the architecture, stack, and rules for this project.

**CRITICAL RULE:** I must continuously update this `GEMINI.md` file whenever I make significant architectural changes, add new models, or discover new patterns in the codebase.

## 📌 Project Overview
This project is a **University/College Management System (UMS)**. It handles different user roles including Admins, Applicants, and Students. It manages the lifecycle of a student from application (documents, paper selection, payment) to being a full student.

## 🛠️ Tech Stack
- **Backend:** Laravel (PHP 8.3+)
- **Frontend:** React 19, Inertia.js, Tailwind CSS v4, Shadcn UI
- **Build Tool:** Vite
- **Routing:** Ziggy (for accessing Laravel routes in React)

## 🏗️ Architecture & Directory Structure
- **Models (`app/Models/`)**:
  - Core: `User`, `Programme`, `Course`, `Subject`, `Batch`
  - Applicant Lifecycle: `ApplicantProfile`, `ApplicantDocument`, `ApplicantPayment`, `ApplicantPaperSelection`
  - Student Lifecycle: `StudentProfile`, `StudentDocument`, `StudentPayment`, `StudentPaperSelection`
- **Controllers (`app/Http/Controllers/`)**: Grouped by role/domain (`Admin`, `Applicant`, `Student`, `Auth`).
- **Repositories (`app/Repositories/`)**: Used for abstracting data access logic (e.g., `Student` repo).
- **Frontend Pages (`resources/js/Pages/`)**: Grouped by role (`Admin`, `Student`) and includes `Layouts`.
- **Components (`resources/js/components/`)**: Reusable UI components (likely Shadcn UI based).

## 📜 Coding Rules & Guidelines
1. **Quality over Speed:** Write proper code, no shortcuts. Ensure all code is robust, type-safe (where applicable), and follows best practices.
2. **Maintain State:** Keep updating this `GEMINI.md` file as the project evolves.
3. **Follow Existing Patterns:** Respect the Repository pattern and the folder structures (e.g., role-based splitting for Controllers and Pages).
4. **Raw SQL Performance:** Since the project prioritizes performance through raw SQL queries (using `DB::insert`, `DB::update`, `DB::select`), **avoid Route Model Binding** and Eloquent ORM operations where possible. Controller methods should accept primitive IDs (e.g., `public function update(Request $request, $batch_id)`) rather than type-hinting Models to prevent hidden `SELECT` queries.

## ?? Progress Log

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

## [2026-07-08] Student Registration UI Wizard & Atomic Workflows

### 🚀 Atomic Create & Segmented Edit Architecture
- **Unified Creation Wizard:** Transformed the `Create.jsx` view into a cohesive, multi-step wizard managed by a single monolithic Inertia `useForm` hook. The form gathers all data across 4 tabs before submission, providing a fluid user experience.
- **Atomic Backend Transactions:** Rewrote `StudentProfileController@store` to receive the entire wizard payload. It executes within a secure `DB::transaction`, handling file uploads, generating batch-year-prefixed `registration_number`s, and inserting rows across all 4 student tables atomically. If any query fails, the entire student creation safely rolls back.
- **Piecemeal Edit Flow:** Designed `Edit.jsx` as a container where all 4 tabs are fully enabled. Each tab wraps an independent Inertia form (`EditBasicInfoForm`, `EditDocumentForm`, etc.) that points to its own controller's `PATCH` endpoint, allowing targeted updates without resubmitting massive payloads.
- **Component Reusability:** Refactored the internal form components (`CreateBasicInfoForm`, etc.) into pure, presentational UI components. They are now flawlessly reused between the Create wizard and the individual Edit wrappers, passing dynamic `buttonLabel` and `processing` states down as props.

### 🐛 Form & Validation Enhancements
- **Centralized Wizard Validation:** Grouped all cross-tab validation rules into a single `StudentProfileRequest`.
- **Intelligent Error Navigation:** Handled Inertia's `onError` callback in the Create wizard to automatically switch the user's active tab to whichever section contains a validation failure, ensuring errors aren't silently hidden.
- **File Upload over PATCH:** Utilized the `_method: patch` workaround to successfully send `multipart/form-data` updates (photos and signatures) to the backend during the piecemeal Edit flow.
- **Export Resolution:** Fixed a fatal Vite build error where `ErrorAlert` was improperly exported by ensuring both named (`export { ErrorAlert }`) and default exports exist.

## [2026-07-09] Student Dashboard & Authentication Workflows

### 🚀 Authentication & Security
- **Student User Creation:** Fixed a critical bug in `StudentProfileController@store` where students created via the Admin panel did not receive an accompanying `users` table record. Now, creating a student atomically creates a `users` record with their `login_identifier` set to their `registration_number` and assigns a default password of `'password'`.
- **First-Login Password Reset Flow:** Introduced a mandatory security measure requiring students to change their default password upon first login.
- **Middleware Interception:** Created and registered the `EnsurePasswordIsChanged` middleware (aliased as `password.changed`). This intercepts any authenticated student whose `is_password_changed` flag is `false` and forces them to the `ChangePassword` view, completely restricting access to the dashboard until secured.
- **Database Schema Update:** Augmented the `users` table migration to include an `is_password_changed` boolean field (default `false`), ensuring historical compatibility.
- **Seeder Intelligence:** Updated the `UserSeeder` to default the primary Admin account's `is_password_changed` status to `true`, preventing admins from being trapped in the reset loop.

### 🎨 Student Experience & Dashboard
- **Read-Only Data Architecture:** Engineered `StudentDashboardController` to execute a comprehensive, high-performance raw SQL `LEFT JOIN` query (similar to the admin's `Show` logic) that securely fetches the authenticated student's full profile, academic placement, documents, and payment history based on their `user_id`.
- **Premium Dashboard UI:** Built the `Dashboard.jsx` interface utilizing the premium Shadcn UI tabbed layout. The view offers a beautiful, responsive, and categorized presentation of the student's data mimicking the high-quality aesthetics of the admin portal, complete with a dedicated header and logout workflow.

## [2026-07-10] PDF Receipt Generation & Frontend Routing Fixes

### 📄 PDF Generation Architecture
- **Robust PDF Engine:** Integrated `barryvdh/laravel-dompdf` to generate downloadable payment receipts. Experimented with headless browsers (`Spatie Browsershot`) for Tailwind support, but opted to revert to `dompdf` for its zero-dependency reliability and superior performance on local Windows/Herd environments.
- **Table-Based Layout Refactoring:** Completely rewrote the `receipt.blade.php` template using a classic HTML `<table>` architecture and strict inline CSS to bypass `dompdf`'s lack of support for CSS Grid, Flexbox, and external stylesheets (Vite/Tailwind).
- **CSS Clipping Fixes:** Resolved a text-clipping bug where `overflow: hidden` on a container caused `dompdf` to slice absolutely positioned title headers in half. 
- **Timezone Localization:** Corrected the global application timezone in `config/app.php` to `'Asia/Kolkata'` to ensure all PDF footer timestamps match the user's local Indian Standard Time instead of defaulting to UTC.

### ⚙️ Routing & Auth Optimization
- **Ziggy Route Propagation:** Fixed a fatal React application crash on the frontend by running `php artisan ziggy:generate`. This successfully propagated newly created backend download routes (`student.payment.receipt`) to the Inertia/React client, preventing undefined route exceptions.
- **Optimized Admin Auth:** Refactored `AdminLoginController` to use a cleaner, single-line conditional check (`Auth::attempt($credentials) && Auth::user()->role === 'Admin'`), improving readability and execution flow without sacrificing security.

## [2026-07-13] Email Integration, Password Management & Security

### 📧 SMTP & Email Verification
- **SMTP Configuration:** Corrected the `.env` configuration for Gmail SMTP, explicitly ensuring that `MAIL_USERNAME` matches the full sender address (`MAIL_FROM_ADDRESS`) to successfully bypass Google's `535 5.7.8` authentication rejection.
- **Welcome Emails:** Fully enabled the automated welcome email dispatch in `StudentProfileController@store` when an admin creates a student profile, securely transmitting their auto-generated registration number and temporary password.

### 🔐 Password Change & Management
- **Admin Password Management:** Implemented an `AdminPasswordChangeController` and a dedicated React view. Admins can securely change their passwords from the dashboard without being trapped by the `EnsurePasswordIsChanged` middleware.
- **Security Best Practices:** Enforced strict session invalidation upon an admin changing their password (`Auth::logout()`, `$request->session()->invalidate()`, `$request->session()->regenerateToken()`), forcing a clean re-authentication flow.
- **UI Streamlining:** Refined the Admin Sidebar UI, moving the "Change Password" action out of the main menu list and transforming it into a sleek icon button directly next to the "Logout" button in the bottom user profile section.
- **Dynamic Profile Avatars:** Updated the Admin Sidebar to dynamically pull the authenticated user's name and role from the Inertia `usePage().props`, displaying their true initial rather than a static "AD".

### 🔑 Forgot Password Architecture
- **Unified Email Template:** Created a reusable `ForgotPasswordEmail` mailable and Blade template that handles password resets for both Students and Admins.
- **Role-Specific Backend Controllers:** 
  - Engineered `StudentForgotPasswordController` to accept a Registration Number, verify the user account, and uniquely query the `student_profiles` table to dispatch the email to their actual personal address.
  - Engineered `AdminForgotPasswordController` to directly query and dispatch to the admin's email (`login_identifier`).
- **Forced Password Reset Pipeline:** Architected the forgot password flow to securely generate a random 10-character temporary password, immediately hash it, and reset the user's `is_password_changed` flag to `false`. This guarantees that upon using the temporary password, the user is instantly trapped by the middleware and forced to set a permanent, private password.

## [2026-07-14] Repository Pattern Refactoring & Applicant Registration Workflow

### 🚀 Repository Pattern Implementation (Admin/Core Modules)
- **Architecture Shift:** Refactored all major Admin-facing modules (Batch, Programme, Course, Subject, and Student modules) out of "Fat Controllers" and into dedicated `Repository` classes to completely isolate database logic and improve code maintainability.
- **Constructor Injection:** Updated all core controllers to use Laravel's constructor dependency injection (e.g. `protected SubjectRepository $subjectRepo;`) ensuring loose coupling.
- **Namespace Alignment Fixes:** Corrected strict PSR-4 namespacing issues where `App\Repositories\Admin` files were missing the `\Admin` namespace declaration, resolving fatal "Class not found" crashes during the refactoring process.

### 🎓 Applicant Registration Portal (Public Facing)
- **Applicant Schema Orchestration:** Designed and migrated 4 dedicated database tables (`applicant_profiles`, `applicant_paper_selections`, `applicant_documents`, `applicant_payments`) mirroring the structure of the internal student tables but strictly isolating external applicants. Removed the `user_id` foreign key as applicants are independent, non-authenticated entities.
- **Atomic DB Transactions:** Created `ApplicantRepository@createApplicant` which orchestrates a massive 4-table raw SQL insertion inside a `DB::transaction`. If any step (such as photo upload or payment details) fails, the entire application gracefully rolls back to prevent orphaned records.
- **Intelligent Unique ID Generation:** Engineered `ApplicantController` to securely validate all 4 tiers of applicant data and automatically generate a unique, trackable `applicant_code` in the format `APP-YYYY-XXXXXX` (e.g., `APP-2026-X9A34B`) upon a successful submission.
- **Premium Public UI (Inertia):** Built a gorgeous, responsive, 4-step wizard interface at `/applicant/apply` using Shadcn Tabs.
  - **Smart Validation Routing:** Bound Inertia's `onError` callback to detect exactly which fields failed validation and automatically force the UI to jump to the corresponding tab (Profile, Academic, Document, or Payment).
  - **Component Integration:** Leveraged the custom `<ErrorAlert>` component globally across the registration form to provide a polished, unified error presentation.
  - **Success Modal:** Designed a crisp success modal that prominently displays the auto-generated `applicant_code` and explicitly instructs the user to save it for future references.

### 🐛 Form UI Polish & Cache Architecture Fixes
- **Global Error Banner:** Upgraded the error handling in both the `Applicant` and `Admin Student` creation wizards by moving away from Inertia's `onError` callback. Implemented a `useEffect` hooked to `hasErrors` that maps fields to their correct tabs and renders a highly visible red banner at the top of the form, guaranteeing users never miss a validation failure.
- **Strict UI Validation Mapping:** Stripped out backend-only required fields (like `nationality` and `present_country`) from the frontend routing arrays. Since these fields are hardcoded/hidden, mapping them to the UI caused the tab-switching logic to break invisibly.
- **Shared Cache Architecture:** Discovered and fixed a critical data collision bug where the `SubjectController` and `StudentProfileController` were silently overriding each other's cache because they shared the `'active_programmes_with_courses'` key but expected different array keys (`name` vs `course_name`). Standardized the `SubjectRepository` to output `course_name`, allowing both massive modules to safely share a single cache instance without duplicating data or breaking dropdowns!

