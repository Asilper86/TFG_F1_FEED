import React from 'react';
import { router } from '@inertiajs/react';
import { formatLapTime } from '../Pages/Dashboard';

export default function LapsTable({ laps, onSelectLap, selectedId }) {
    const handleDelete = (e, lapId) => {
        e.stopPropagation();
        if (confirm("Delete lap data?")) {
            router.delete(`/telemetry/lap/${lapId}`);
        }
    }

    return (
        <div className="bg-[#23262A] rounded-lg border border-[#2d3136] h-[550px] flex flex-col">
            <h3 className="text-[12px] font-bold uppercase tracking-wider text-white p-5 border-b border-[#2d3136] m-0">
                SESSION LAPS
            </h3>
            <div className="overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-[#333] scrollbar-track-transparent">
                {laps.map((lap) => {
                    const isActive = selectedId === lap.id;
                    return (
                        <div
                            key={lap.id}
                            onClick={() => onSelectLap(lap)}
                            className={`group flex items-center justify-between p-4 border-b border-[#2d3136] transition-colors cursor-pointer ${
                                isActive 
                                ? 'bg-[#2A2E33] border-l-4 border-l-[#E10600]' 
                                : 'bg-transparent border-l-4 border-l-transparent hover:bg-[#282C30]'
                            }`}
                        >
                            <span className={`text-sm font-semibold tracking-wide ${isActive ? 'text-white' : 'text-gray-400'}`}>
                                L{lap.lap_number}
                            </span>
                            <div className="flex items-center gap-4">
                                <span className={`font-mono text-sm ${isActive ? 'text-white' : 'text-gray-400'}`}>
                                    {formatLapTime(lap.lap_time)}
                                </span>
                                <button
                                    onClick={(e) => handleDelete(e, lap.id)}
                                    className="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-[#E10600] transition-opacity px-1"
                                    title="Delete"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    );
                })}
                {laps.length === 0 && (
                    <div className="h-full flex items-center justify-center text-gray-500 text-xs uppercase tracking-widest text-center px-4">
                        NO RECORDED LAPS
                    </div>
                )}
            </div>
        </div>
    );
}