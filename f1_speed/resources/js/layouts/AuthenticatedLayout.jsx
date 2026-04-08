import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';

export default function AuthenticatedLayout({ user, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const logout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="min-h-screen bg-[#050505] text-white">
            {/* Barra de Navegación */}
            <nav className="bg-[#0f0f0f] border-b border-white/5 sticky top-0 z-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 items-center">
                        <div className="flex items-center">
                            {/* Logo */}
                            <Link href="/" className="flex items-center">
                                <span className="text-xl font-black italic tracking-tighter uppercase">
                                    F1<span className="text-red-600">SPEED</span>
                                </span>
                            </Link>

                            {/* Enlace Simple (Sustituye a NavLink) */}
                            <div className="hidden sm:flex sm:ms-10">
                                <Link href={route('dashboard')} className="text-sm font-bold uppercase tracking-widest text-red-600 border-b-2 border-red-600 pb-1">
                                    Race Control
                                </Link>
                            </div>
                        </div>

                        {/* Menú de Usuario Simple (Sustituye a Dropdown) */}
                        <div className="hidden sm:flex sm:items-center">
                            <div className="flex items-center space-x-4">
                                <span className="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                    Piloto: <span className="text-white">{user.name}</span>
                                </span>
                                <button 
                                    onClick={logout}
                                    className="text-[10px] uppercase tracking-widest bg-red-600/10 text-red-500 px-3 py-1 rounded border border-red-500/20 hover:bg-red-600 hover:text-white transition-all"
                                >
                                    Cerrar Box
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Cabecera */}
            {header && (
                <header className="bg-[#050505] border-b border-white/5">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            {/* Contenido */}
            <main className="relative">
                <div className="absolute top-0 right-0 w-96 h-96 bg-red-600/5 blur-[100px] pointer-events-none"></div>
                <div className="relative z-10">
                    {children}
                </div>
            </main>
        </div>
    );
}