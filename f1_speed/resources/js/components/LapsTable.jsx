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

    // Calculadora de Sectores Morados
    let mejorS1 = 999, mejorS2 = 999, mejorS3 = 999;
    laps.forEach(lap => {
        if (parseFloat(lap.sector_1) > 0 && parseFloat(lap.sector_1) < mejorS1) mejorS1 = parseFloat(lap.sector_1);
        if (parseFloat(lap.sector_2) > 0 && parseFloat(lap.sector_2) < mejorS2) mejorS2 = parseFloat(lap.sector_2);
        if (parseFloat(lap.sector_3) > 0 && parseFloat(lap.sector_3) < mejorS3) mejorS3 = parseFloat(lap.sector_3);
    });

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
                            className={`group flex items-center justify-between p-4 border-b border-[#2d3136] transition-colors cursor-pointer ${isActive
                                    ? 'bg-[#2A2E33] border-l-4 border-l-[#E10600]'
                                    : 'bg-transparent border-l-4 border-l-transparent hover:bg-[#282C30]'
                                }`}
                        >
                            <div className="flex flex-col w-full ml-2">
                                <div className="flex justify-between items-center">
                                    <span className={`text-sm font-semibold tracking-wide ${isActive ? 'text-white' : 'text-gray-400'}`}>
                                        L{lap.lap_number}
                                    </span>
                                    <div className="flex items-center gap-4">
                                        <span className={`font-mono text-sm ${isActive ? 'text-white' : 'text-gray-400'}`}>
                                            {formatLapTime(lap.lap_time)}
                                        </span>
                                        <button onClick={(e) => handleDelete(e, lap.id)} className="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-[#E10600] transition-opacity px-1" title="Delete">✕</button>
                                    </div>
                                </div>

                                <div className="flex justify-between mt-2 pt-2 border-t border-[#2d3136] text-[10px] font-mono tracking-widest text-gray-500">
                                    <span>S1 <span className={`${parseFloat(lap.sector_1) === mejorS1 ? 'text-[#c026d3] font-black' : (isActive ? 'text-[#3FA9F5]' : 'text-gray-400')}`}>{lap.sector_1}</span></span>
                                    <span>S2 <span className={`${parseFloat(lap.sector_2) === mejorS2 ? 'text-[#c026d3] font-black' : (isActive ? 'text-[#10b981]' : 'text-gray-400')}`}>{lap.sector_2}</span></span>
                                    <span>S3 <span className={`${parseFloat(lap.sector_3) === mejorS3 ? 'text-[#c026d3] font-black' : (isActive ? 'text-[#E10600]' : 'text-gray-400')}`}>{lap.sector_3}</span></span>
                                </div>
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