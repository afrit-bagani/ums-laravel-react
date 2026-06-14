export default function Welcome() {
    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
            <h1 className="text-4xl font-bold mb-4">
                Welcome to the User Management System
            </h1>
            <p className="text-lg text-gray-600 mb-8">
                Please log in to access your dashboard.
            </p>
            <a
                href="/login"
                className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-300"
            >
                Log In
            </a>
        </div>
    );
}
