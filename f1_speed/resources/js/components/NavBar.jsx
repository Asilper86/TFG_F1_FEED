import React from 'react';
import { Link, router } from '@inertiajs/react'; 

export default function Navbar({ user }) {
    const handleLogout = () => {
        router.post('/logout', {}, {
            onFinish: () => {
                window.location.href = '/';
            }
        })
    }
    
    return (
        <nav className="bg-[#1B1D21] border-b border-[#2d3136] px-6 py-4">
            <div className="max-w-7xl mx-auto flex justify-between items-center">
                <div className="flex items-center gap-8">
                    <Link href="/dashboard-f1" className="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                        <span className="text-[#E10600]">/</span> F1 SPEED
                    </Link>
                    
                    <Link href="/dashboard-f1" className="text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#E10600] transition-colors">
                        Dashboard
                    </Link>
                    <Link href="/session/setup" className="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors">
                        Setup de Sesión
                    </Link>
                    <a href="/feed" className='text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors'>
                        AUTO FEED
                    </a>
                </div>

                <div className="flex items-center space-x-6">
                    <div className="text-right hidden sm:block">
                        <p className="text-[10px] uppercase tracking-widest text-[#E10600] font-bold leading-none mb-1">Piloto Activo</p>
                        <p className="text-sm font-bold uppercase tracking-wide text-white">{user?.name || 'Invitado'}</p>
                    </div>

                    <button onClick={handleLogout} className="bg-[#23262A] hover:bg-[#2A2E33] text-gray-300 hover:text-white text-[11px] font-bold uppercase tracking-widest px-4 py-2.5 rounded border border-[#2d3136] transition-all">
                        CERRAR BOX
                    </button>
                </div>
            </div>
        </nav>
    );
}