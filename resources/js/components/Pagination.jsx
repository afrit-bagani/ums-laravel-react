import { Link } from "@inertiajs/react";

export default function Pagination({ data }) {
    return (
    <div className="border-t border-gray-100 p-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
        <p className="text-sm text-gray-500 font-medium">
            Showing <span className="text-gray-900 font-semibold">{data.from || 0}</span> to <span className="text-gray-900 font-semibold">{data.to || 0}</span> of <span className="text-gray-900 font-semibold">{data.total || 0}</span> results
        </p>

        <div className="flex items-center gap-1 p-1 bg-gray-100/50 border border-gray-200/60 rounded-xl">
            {data.links.map((link, index) => {
                if (link.url === null) {
                    return (
                        <div
                            key={index}
                            className="px-3 py-1.5 text-sm font-medium text-gray-400 bg-transparent cursor-not-allowed rounded-lg"
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                }

                // Render active and clickable links
                return (
                    <Link
                        key={index}
                        href={link.url}
                        preserveState
                        preserveScroll
                        className={`px-3 py-1.5 text-sm font-medium rounded-lg transition-colors ${link.active
                                ? 'bg-white text-gray-900 shadow-sm border border-gray-200'
                                : 'text-gray-500 hover:bg-white hover:text-gray-900'
                            }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                );
            })}
        </div>
    </div>)
}
