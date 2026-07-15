export default function AppLogoIcon({ text }) {
    return (
        <div className="absolute top-8 left-8 sm:top-12 sm:left-12 md:left-16 flex items-center gap-3 font-bold text-2xl tracking-tight">
            <div className="bg-white shadow-sm p-1.5 rounded-lg border border-gray-100 flex items-center justify-center">
                <img src="/logo.svg" alt="UMS Logo" className="w-8 h-8" />
            </div>
            <span className="bg-linear-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{text}</span>
        </div>
    );
}
