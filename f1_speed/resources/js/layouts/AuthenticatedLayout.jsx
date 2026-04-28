import React, { useState } from 'react';
import { Link, router, Head } from '@inertiajs/react';

export default function AuthenticatedLayout({ user, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const logout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="min-h-screen bg-[#121418] text-white font-sans">
            <Head>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
            </Head>

            {/* Barra de Navegación Estilo F1 */}
            <nav className="bg-[#1B1D21] border-b border-[#2d3136] sticky top-0 z-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-16">
                        <div className="flex items-center gap-8">
                            {/* Logo */}
                            <div className="shrink-0 flex items-center">
                                <Link href={route('dashboard')} className="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                                    <span className="text-[#E10600]">/</span> F1 SPEED
                                </Link>
                            </div>

                            {/* Links de Navegación Identicos a Blade */}
                            <div className="hidden space-x-6 sm:flex sm:items-center sm:ms-6">
                                <Link href={route('dashboard')} className={`text-[15px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 ${route().current('dashboard') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]'}`}>
                                    <i className="fa-solid fa-gauge"></i>Dashboard
                                </Link>
                                <Link href={route('social.feed')} className={`text-[15px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 ${route().current('social.feed') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]'}`}>
                                    <i className="fa-solid fa-rss"></i>AUTO FEED
                                </Link>
                                <Link href={route('social.profile')} className={`text-[15px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 ${route().current('social.profile') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]'}`}>
                                    <i className="fa-solid fa-user"></i>MI PERFIL
                                </Link>
                                <Link href={route('social.search')} className={`text-[15px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 ${route().current('social.search') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]'}`}>
                                    <i className="fa-solid fa-magnifying-glass"></i>BUSCADOR
                                </Link>
                            </div>
                        </div>

                        {/* Menú Usuario Simple */}
                        <div className="hidden sm:flex sm:items-center sm:ms-6">
                            <div className="flex items-center space-x-4">
                                <span className="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                    Piloto: <span className="text-white">{user.name}</span>
                                </span>
                                <button 
                                    onClick={logout}
                                    className="text-[10px] uppercase tracking-widest text-[#E10600] hover:text-[#ff0700] font-bold transition-all"
                                >
                                    Cerrar Box
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Cabecera (Opcional) */}
            {header && (
                <header className="bg-[#121418] border-b border-white/5">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            {/* Contenido principal */}
            <main className="relative">
                <div className="relative z-10">
                    {children}
                </div>
            </main>
        </div>
    );
}