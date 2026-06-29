import { Link, usePage } from '@inertiajs/react';
import React from 'react';
import { cn } from '@/lib/utils';
import {
    LayoutDashboard,
    Layers,
    GraduationCap,
    Library,
    BookOpen,
    Users,
    LogOut
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useRoute } from 'ziggy-js';


export default function Sidebar() {
    const { url } = usePage();
    const route = useRoute();

    const menuItems = [
        // { name: 'Dashboard', icon: LayoutDashboard, href: '/admin/dashboard' },
        { name: 'Batches', icon: Layers, href: '/admin/batches' },
        { name: 'Programmes', icon: GraduationCap, href: '/admin/programmes' },
        { name: 'Courses', icon: Library, href: '/admin/courses' },
        { name: 'Subjects', icon: BookOpen, href: '/admin/subjects' },
        { name: 'Students', icon: Users, href: '/admin/students' },
    ];

    return (
        <aside className="w-56 min-h-screen bg-white border-r border-gray-200 flex flex-col hidden md:flex">
            <div className="h-14 flex items-center px-5 border-b border-gray-200">
                <div className="flex items-center gap-2 font-bold text-lg tracking-tight text-gray-900">
                    <div className="bg-gray-900 text-white p-1 rounded-md">
                        <GraduationCap className="w-5 h-5" />
                    </div>
                    <span>UMS Admin</span>
                </div>
            </div>

            <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                <div className="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">
                    Main Menu
                </div>

                {menuItems.map((item) => {
                    const isActive = url.startsWith(item.href);

                    return (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={cn(
                                "flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors",
                                isActive
                                    ? "bg-gray-900 text-white"
                                    : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                            )}
                        >
                            <item.icon className={cn("w-4 h-4", isActive ? "text-gray-300" : "text-gray-400")} />
                            {item.name}
                        </Link>
                    );
                })}
            </nav>

            <div className="p-3 border-t border-gray-200">
                <div className="flex items-center gap-3 px-2 py-1">
                    <div className="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-[10px]">
                        AD
                    </div>
                    <div className="flex flex-col">
                        <span className="text-sm font-medium text-gray-900">Administrator</span>
                        <span className="text-xs text-gray-500">System Access</span>
                    </div>
                    <Button variant="destructive" asChild>
                        <Link href={route('admin.logout')} method="post" as="button" title='Logout'><LogOut /></Link>
                    </Button>
                </div>
            </div>
        </aside>
    );
}
