import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import ActionRow from "./ActionRow";
import Pagination from "@/components/Pagination";
import { Checkbox } from "@/components/ui/checkbox";

export function BatchTable({ batches, selectedIds, handleSelectRow, handleSelectAll, handlePerPageChange }) {
  const selectAll = batches.data.length > 0 && batches.data.every(b => selectedIds.has(b.batch_id));

  return (
    <div className="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden [&_div[data-slot=table-container]]:max-h-[65vh]">
      <Table className="w-full text-left whitespace-nowrap">
        <TableHeader className="sticky top-0 z-10 bg-white shadow-sm">
          <TableRow className="bg-gray-50 border-b border-gray-100 hover:bg-gray-50">
            <TableHead className="py-2.5 px-4 w-10 text-center h-auto">
              <Checkbox
                onCheckedChange={handleSelectAll}
                checked={selectAll}
                className="border-gray-300 data-[state=checked]:bg-indigo-600 data-[state=checked]:border-indigo-600 cursor-pointer"
              />
            </TableHead>
            <TableHead className="py-2.5 px-4 text-[11px] font-semibold text-gray-500 uppercase text-center h-auto">Sl No</TableHead>
            <TableHead className="py-2.5 px-4 text-[11px] font-semibold text-gray-500 uppercase h-auto">Status</TableHead>
            <TableHead className="py-2.5 px-4 text-[11px] font-semibold text-gray-500 uppercase h-auto">Action</TableHead>
            <TableHead className="py-2.5 px-4 text-[11px] font-semibold text-gray-500 uppercase h-auto">Batch Code</TableHead>
            <TableHead className="py-2.5 px-4 text-[11px] font-semibold text-gray-500 uppercase h-auto">Batch Name</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody className="divide-y divide-gray-100 text-[13px]">
          {batches.data.length === 0 ? (
            <TableRow>
              <TableCell colSpan={6} className="py-12 text-center text-gray-500">
                No batches found. Try adjusting your filters.
              </TableCell>
            </TableRow>
          ) : (
            batches.data.map((batch, index) => {
              const serialNo = batches.from + index;
              const isSelected = selectedIds.has(batch.batch_id);

              return (
                <TableRow key={batch.batch_id} className={`transition-colors ${isSelected ? 'bg-indigo-50/30 hover:bg-indigo-50/40' : 'hover:bg-gray-50/50'}`}>
                  <TableCell className="py-2.5 px-4 text-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={(checked) => handleSelectRow(batch.batch_id, checked === true)}
                      className="border-gray-300 data-[state=checked]:bg-indigo-600 data-[state=checked]:border-indigo-600 cursor-pointer"
                    />
                  </TableCell>
                  <TableCell className="py-2.5 px-4 text-center text-gray-500 font-medium">
                    {serialNo}
                  </TableCell>
                  <TableCell className="py-2.5 px-4">
                    <span className={`inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold ${batch.status === 'active' ? 'bg-emerald-100/60 text-emerald-700' : 'bg-rose-100/60 text-rose-700'}`}>
                      <span className={`w-1.5 h-1.5 rounded-full ${batch.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'}`}></span>
                      {batch.status.toUpperCase()}
                    </span>
                  </TableCell>
                  <TableCell className="py-2.5 px-4">
                    <ActionRow batch={batch} />
                  </TableCell>
                  <TableCell className="py-2.5 px-4 font-semibold text-gray-900">{batch.code}</TableCell>
                  <TableCell className="py-2.5 px-4 text-gray-600">{batch.name}</TableCell>
                </TableRow>
              );
            })
          )}
        </TableBody>
      </Table>

      {/* PAGINATION */}
      <Pagination data={batches} onPerPageChange={handlePerPageChange} />
    </div>
  )
}
