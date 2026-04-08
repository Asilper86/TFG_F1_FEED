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
        <nav className="bg-[#0f0f0f] border-b border-white/5 px-6 py-4 mb-8">
            <div className="max-w-7xl mx-auto flex justify-between items-center">
                <Link href="/" className="text-xl font-black italic tracking-tighter uppercase">
                    F1<span className="text-red-600">SPEED</span>
                </Link>

                <div className="flex items-center space-x-6">
                    <div className="text-right hidden sm:block">
                        <p className="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold leading-none">Piloto Activo</p>
                        <p className="text-sm font-bold italic text-white uppercase">{user?.name || 'Invitado'}</p>
                    </div>

                    {}
                    <button onClick={handleLogout} className="bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg border border-red-600/20 transition-all duration-300">
                        Cerrar Box
                    </button>
                </div>
            </div>
        </nav>
    );
}