import React from 'react';
import { formatLapTime } from '../Pages/Dashboard';

export default function StatsCards({ laps = [] }) {
    let mejorVuelta = '--:--.---';
    if(laps.length > 0){
        mejorVuelta = formatLapTime(Math.min(...laps.map(v => parseFloat(v.lap_time))));
    }

    let mejorVelocidad = "0";
    if(laps.length > 0 && laps[0].telemetry_logs?.[0]){
        mejorVelocidad = Math.max(...laps[0].telemetry_logs[0].telemetry_json.speed);
    }

    let tiempoIdeal = '--:--.---';

    if(laps.length > 0){
        const minS1 = Math.min(...laps.map(v => parseFloat(v.sector_1)).filter(s=>s>0));
        const minS2 = Math.min(...laps.map(v => parseFloat(v.sector_2)).filter(s=>s>0));
        const minS3 = Math.min(...laps.map(v => parseFloat(v.sector_3)).filter(s=>s>0));

        const suma = minS1 + minS2 + minS3;
        if(suma > 0 && isFinite(suma)){
            tiempoIdeal = formatLapTime(suma)
        }
    }

    const stats = [
        { label: 'MEJOR VUELTA', value: mejorVuelta },
        { label: 'VUELTA IDEAL', value: tiempoIdeal, color: 'text-[#c026d3]' },
        { label: 'VELOCIDAD PUNTA (KM/H)', value: mejorVelocidad }, 
        { label: 'TOTAL VUELTAS', value: laps.length },
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            {stats.map((stat, i) => (
                <div key={i} className="bg-[#23262A] p-6 rounded-lg flex flex-col justify-center border border-[#2d3136]">
                    <span className="text-3xl font-bold text-white mb-2">{stat.value}</span>
                    <span className="text-[11px] font-semibold text-gray-400 uppercase tracking-widest">{stat.label}</span>
                </div>
            ))}
        </div>
    );
}