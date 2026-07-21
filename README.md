# University Management System (UMS)

## Overview
A comprehensive, state-of-the-art University/College Management System designed to handle the entire lifecycle of students, from applicant registration to full enrollment. The system features strict role-based access for Admins, Applicants, and Students, complete with a robust AI-powered document verification system for automated passport photo and signature validation.

## Tech Stack
- **Backend**: Laravel (PHP 8.3+)
- **Frontend**: React 19, Inertia.js
- **Styling & UI**: Tailwind CSS v4, Shadcn UI components
- **Database**: MySQL (optimized with raw SQL for maximum performance)
- **AI Microservice**: Python FastAPI, Hugging Face Transformers (CLIP), EasyOCR, ImageHash

## Key Features
- **Admin Dashboard**: Sleek, high-performance data tables with bulk actions, sorting, and filtering for Batches, Programmes, Courses, and Subjects.
- **Applicant Portal**: A secure, public-facing multi-step registration wizard with automated `APP-YYYY-XXXX` code generation and atomic database transactions.
- **Student Enrollment**: Complete admission workflow tracking basic info, academic placements, uploaded documents, and payment details across fully normalized database tables.
- **AI Document Verification**: Real-time evaluation of uploaded passport photos and handwritten signatures utilizing a local Python microservice. Validates semantic content and prevents duplicate image uploads via perceptual hashing (`pHash`).
- **Student Dashboard**: Secure read-only portal for enrolled students to view their profile, academic details, and download generated PDF payment receipts (via `dompdf`).
- **Automated Email & Security**: SMTP-powered welcome emails with auto-generated temporary passwords. The system forces a mandatory password change on the first login via custom middleware.

---

## Setup Instructions

### Prerequisites
- **PHP 8.3+** (Laravel Herd recommended for Windows)
- **Composer**
- **Node.js & npm**
- **MySQL**
- **Python 3.10+** (for the AI Microservice)

### 1. Application Setup
Clone the repository and install the backend and frontend dependencies:
```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Setup environment variables
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with your local database and mail credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dcg_ums
DB_USERNAME=root
DB_PASSWORD=admin

AI_MICROSERVICE_URL="http://127.0.0.1:8000"

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Database Migration & Seeding
The project comes with a comprehensive seeding architecture that populates related tables with realistic data.
```bash
# Run migrations and populate mock data
php artisan migrate:fresh --seed
```

### 3. AI Document Microservice Setup
The AI Microservice validates images locally using open-source models to ensure privacy.
```bash
# Set up a Python virtual environment
python -m venv .venv
venv\Scripts\activate

# Install the required Python packages
pip install fastapi uvicorn Pillow imagehash easyocr transformers python-dotenv

# Run the FastAPI server
python -m uvicorn main:app --reload
```
*(Ensure the microservice is running before attempting to upload documents in the Laravel app.)*

### 4. Running the Project
Start the Vite development server to compile your React components:
```bash
npm run dev
```
If you are not using Laravel Herd, also start the PHP development server:
```bash
php artisan serve
```
You can now access the application at `http://ums.test` (or `http://localhost:8000`).