import React from 'react';
import { formatLapTime } from '../Pages/Dashboard';
import { router } from '@inertiajs/react';



export default function LapsTable({ laps, onSelectLap, selectedId }) {

    const handleDelete = (e, lapId) => {
        e.stopPropagation();
        if (confirm("¿Deseas eliminar definitivamente?")) {
            router.delete(`/telemetry/lap/${lapId}`)
        }
    }

    return (
        <div className="bg-[#0f0f0f]/80 backdrop-blur-md p-4 rounded-2xl border border-white/5 h-full">
            <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4 italic">Historial de Sesión</h3>
            <div className="space-y-2">
                {laps.map((lap) => (
                    <div
                        key={lap.id}
                        onClick={() => onSelectLap(lap)}
                        className={`flex justify-between p-3 rounded-lg border transition-all cursor-pointer ${selectedId === lap.id ? 'border-red-600 bg-red-600/10' : 'border-white/5 bg-white/5 hover:border-white/20'
                            }`}
                    >
                        <span className={`${selectedId === lap.id ? 'text-white' : 'text-red-500'} font-bold p-2 italic`}>
                            L{lap.lap_number}
                        </span>
                        <div className="flex items-center space-x-4">
                            <span className="text-white font-mono">{formatLapTime(lap.lap_time)}</span>
                            <button
                                onClick={(e) => handleDelete(e, lap.id)}
                                className="text-red-500/50 p-2 hover:text-red-500 transition-colors text-lg"
                                title="Borrar Vuelta"
                            >
                                ×
                            </button>
                        </div>



                    </div>
                ))}
                {laps.length === 0 && <p className="text-gray-600 text-xs text-center italic">Esperando telemetría...</p>}
            </div>
        </div>
    );
}