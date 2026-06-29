import { Link } from "@inertiajs/react";
import { Field, FieldLabel } from "@/components/ui/field";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

export default function Pagination({ data, onPerPageChange }) {
    return (
    <div className="border-t border-gray-100 p-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
        <p className="text-sm text-gray-500 font-medium">
            Showing <span className="text-gray-900 font-semibold">{data.from || 0}</span> to <span className="text-gray-900 font-semibold">{data.to || 0}</span> of <span className="text-gray-900 font-semibold">{data.total || 0}</span> results
        </p>

        <div className="flex items-center gap-4">
            <Field orientation="horizontal" className="w-fit mb-0 flex items-center gap-2">
                <FieldLabel htmlFor="select-rows-per-page" className="mb-0 text-sm font-medium text-gray-500">Rows per page</FieldLabel>
                <Select defaultValue={data.per_page?.toString() || "10"} onValueChange={onPerPageChange}>
                    <SelectTrigger className="w-20" id="select-rows-per-page">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent align="start">
                        <SelectGroup>
                            <SelectItem value="10">10</SelectItem>
                            <SelectItem value="25">25</SelectItem>
                            <SelectItem value="50">50</SelectItem>
                            <SelectItem value="100">100</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </Field>

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
        </div>
    </div>)
}
