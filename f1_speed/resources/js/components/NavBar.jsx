import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';

export default function Navbar({ user }) {
    const [isOpen, setIsOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);

    const handleLogout = () => {
        router.post('/logout', {}, {
            onFinish: () => {
                window.location.href = '/';
            }
        })
    }

    const isCurrent = (path) => window.location.pathname.includes(path);

    return (
        <nav className="bg-[#1B1D21] border-b border-[#2d3136] sticky top-0 z-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    <div className="flex items-center gap-8">
                        <div className="shrink-0 flex items-center">
                            <Link href="/dashboard-f1" className="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                                <span className="text-[#E10600]">/</span> F1 SPEED
                            </Link>
                        </div>

                        {/* Desktop Menu */}
                        <div className="hidden space-x-6 lg:flex lg:items-center">
                            <Link href="/dashboard-f1" className={`text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 ${isCurrent('dashboard') ? 'text-white border-b-2 border-[#E10600] pb-1' : 'text-gray-400 hover:text-[#E10600]'}`}>
                                <i className="fa-solid fa-gauge"></i>Dashboard
                            </Link>
                            <a href="/feed" className={`text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 text-gray-400 hover:text-[#E10600]`}>
                                <i className="fa-solid fa-rss"></i>AUTO FEED
                            </a>
                            <a href="/profile" className={`text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 text-gray-400 hover:text-[#E10600]`}>
                                <i className="fa-solid fa-user"></i>MI PERFIL
                            </a>
                            <a href="/search" className={`text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 text-gray-400 hover:text-[#E10600]`}>
                                <i className="fa-solid fa-magnifying-glass"></i>BUSCADOR
                            </a>
                        </div>
                    </div>

                    <div className="hidden lg:flex lg:items-center lg:ms-6">
                        <div className="flex items-center space-x-4">
                            <div className="flex items-center gap-3">
                                {user?.profile_photo_url ? (
                                    <img src={user.profile_photo_url} alt={user.name} className="w-8 h-8 rounded-full object-cover border border-[#E10600]" />
                                ) : (
                                    <div className="w-8 h-8 rounded-full bg-[#E10600] flex items-center justify-center text-white font-bold text-xs">
                                        {user?.name?.charAt(0)}
                                    </div>
                                )}
                                <span className="text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                                    {user?.name}
                                </span>
                            </div>
                            <div className="relative">
                                <button
                                    onClick={() => setNotifOpen(!notifOpen)}
                                    className="relative text-gray-400 hover:text-white transition-colors"
                                >
                                    <i className="fa-solid fa-bell text-lg"></i>
                                    {user?.unread_notifications_count > 0 && (
                                        <span className="absolute -top-1 -right-1 w-4 h-4 bg-[#E10600] rounded-full text-[9px] font-black flex items-center justify-center text-white">
                                            {user.unread_notifications_count}
                                        </span>
                                    )}
                                </button>
                                {notifOpen && (
                                    <div className="absolute right-0 mt-3 w-80 bg-[#1B1D21] border border-[#2d3136] rounded-xl shadow-2xl z-50 overflow-hidden">
                                        <div className="px-4 py-3 border-b border-[#2d3136] flex justify-between items-center">
                                            <span className="text-[10px] font-black uppercase tracking-widest text-white">Notificaciones</span>
                                            <a href="/notifications/read-all" className="text-[9px] text-gray-500 hover:text-white uppercase tracking-widest">
                                                Marcar todo leído
                                            </a>
                                        </div>
                                        <div className="max-h-80 overflow-y-auto">
                                            {user?.notifications?.length > 0 ? (
                                                user.notifications.map((n, i) => (
                                                    <div key={i} className={`px-4 py-3 border-b border-[#2d3136] hover:bg-[#23262A] transition-colors ${!n.read_at ? 'border-l-2 border-l-[#E10600]' : ''}`}>
                                                        <p className="text-xs text-gray-300">
                                                            <span className="font-bold text-white">{n.actor?.name}</span>
                                                            {n.type === 'like' && ' le dio like a tu post'}
                                                            {n.type === 'follow' && ' empezó a seguirte'}
                                                            {n.type === 'repost' && ' reposteó tu publicación'}
                                                        </p>
                                                        <p className="text-[9px] text-gray-600 mt-1 uppercase tracking-widest">{n.created_at}</p>
                                                    </div>
                                                ))
                                            ) : (
                                                <p className="text-center text-gray-600 text-xs py-8 uppercase tracking-widest">Sin notificaciones</p>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                            <button
                                onClick={handleLogout}
                                className="text-[10px] uppercase tracking-widest text-[#E10600] hover:text-[#ff0700] font-bold transition-all px-3 py-1.5 bg-[#121418] rounded border border-[#2d3136]"
                            >
                                Cerrar Box
                            </button>
                        </div>
                    </div>

                    {/* Mobile Hamburger Button */}
                    <div className="flex items-center lg:hidden">
                        <button
                            onClick={() => setIsOpen(!isOpen)}
                            className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#23262A] focus:outline-none transition-colors"
                        >
                            <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                {isOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile Menu (Matches Screenshot) */}
            <div className={`${isOpen ? 'fixed inset-0 z-50 bg-[#1B1D21]' : 'hidden'} lg:hidden`}>
                <div className="p-6">
                    <div className="flex justify-between items-center mb-10">
                        <div className="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                            <span className="text-[#E10600]">/</span> F1 SPEED
                        </div>
                        <button onClick={() => setIsOpen(false)} className="text-gray-400 hover:text-white">
                            <svg className="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div className="space-y-8">
                        <Link href="/dashboard-f1" className="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] text-white group">
                            <i className="fa-solid fa-gauge text-[#E10600] w-6 text-center"></i> DASHBOARD
                        </Link>
                        <a href="/feed" className="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] text-white group">
                            <i className="fa-solid fa-rss text-[#E10600] w-6 text-center"></i> AUTO FEED
                        </a>
                        <a href="/profile" className="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] text-white group">
                            <i className="fa-solid fa-user text-[#E10600] w-6 text-center"></i> MI PERFIL
                        </a>
                        <a href="/search" className="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] text-white group">
                            <i className="fa-solid fa-magnifying-glass text-[#E10600] w-6 text-center"></i> BUSCADOR
                        </a>
                    </div>

                    <div className="mt-12 pt-8 border-t border-[#2d3136]">
                        <div className="flex items-center gap-4 mb-10">
                            {user?.profile_photo_url ? (
                                <img src={user.profile_photo_url} alt={user.name} className="w-12 h-12 rounded-full object-cover border-2 border-[#E10600]" />
                            ) : (
                                <div className="w-12 h-12 rounded-full bg-[#E10600] flex items-center justify-center text-white font-black text-lg italic">
                                    {user?.name?.charAt(0)}
                                </div>
                            )}
                            <span className="text-lg font-black uppercase tracking-widest text-white italic">{user?.name}</span>
                        </div>

                        <button
                            onClick={handleLogout}
                            className="text-[#E10600] text-sm font-black uppercase tracking-[0.2em] hover:text-[#ff0700] transition-colors"
                        >
                            CERRAR BOX
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    );
}