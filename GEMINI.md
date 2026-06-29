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
