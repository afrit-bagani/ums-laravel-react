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
