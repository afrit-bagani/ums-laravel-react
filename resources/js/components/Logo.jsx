import { Building2 } from "lucide-react";

export default function Logo({ text }) {
    return (
        <div className="absolute top-8 left-8 sm:top-12 sm:left-12 md:left-16 flex items-center gap-2 font-semibold text-lg tracking-tight">
            <div className="bg-gray-900 text-white p-1.5 rounded-md">
                <Building2 className="w-5 h-5" />
            </div>
            <span>{text}</span>
        </div>
    );
}
