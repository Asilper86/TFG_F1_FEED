import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { formatLapTime } from '../Pages/Dashboard';

export default function ShareModal() {
    const [isOpen, setIsOpen] = useState(false);
    const [lap, setLap] = useState(null);
    
    const { data, setData, post, processing, reset, errors } = useForm({
        lap_id: '',
        content: '',
    });

    useEffect(() => {
        const handleOpen = (e) => {
            setLap(e.detail.lap);
            setData('lap_id', e.detail.lap.id);
            setIsOpen(true);
        };

        window.addEventListener('open-share-modal', handleOpen);
        return () => window.removeEventListener('open-share-modal', handleOpen);
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/telemetry/share-lap', {
            onSuccess: () => {
                setIsOpen(false);
                reset();
            },
        });
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div className="bg-[#1B1D21] border border-[#2d3136] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div className="px-6 py-4 border-b border-[#2d3136] flex justify-between items-center bg-[#121418]/50">
                    <h2 className="text-sm font-black uppercase tracking-[0.2em] text-white italic">
                        <span className="text-[#E10600]">/</span> Compartir Vuelta
                    </h2>
                    <button onClick={() => setIsOpen(false)} className="text-gray-500 hover:text-white transition-colors">
                        <i className="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    {lap && (
                        <div className="bg-[#121418] border border-[#2d3136] rounded-xl p-4 flex items-center justify-between">
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-[#E10600] mb-1 italic">Vuelta Detectada</p>
                                <p className="text-xl font-black text-white italic tracking-tighter">
                                    {formatLapTime(lap.lap_time)}
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="text-[10px] font-bold uppercase tracking-widest text-gray-500">Track</p>
                                <p className="text-xs font-bold text-gray-300 uppercase tracking-widest">
                                    {lap.session?.track_id || 'F1 Track'}
                                </p>
                            </div>
                        </div>
                    )}

                    <div className="space-y-2">
                        <label className="text-[10px] font-black uppercase tracking-widest text-gray-500 italic px-1">Tu Comentario</label>
                        <textarea
                            value={data.content}
                            onChange={e => setData('content', e.target.value)}
                            placeholder="¿Qué te ha parecido esta vuelta?"
                            className="w-full bg-[#121418] border border-[#2d3136] rounded-xl p-4 text-sm text-white focus:border-[#E10600] outline-none transition-all min-h-[120px] resize-none"
                            autoFocus
                        />
                        {errors.content && <p className="text-[#E10600] text-[10px] uppercase font-bold mt-1 px-1">{errors.content}</p>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full bg-[#E10600] hover:bg-[#ff0700] text-white font-black uppercase italic tracking-tighter py-4 rounded-xl text-sm transition-all active:scale-95 disabled:opacity-50 shadow-[0_0_20px_rgba(225,6,0,0.2)]"
                    >
                        {processing ? 'PUBLICANDO...' : 'PUBLICAR EN EL FEED'}
                    </button>
                </form>
            </div>
        </div>
    );
}
