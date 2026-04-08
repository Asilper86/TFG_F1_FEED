import React from 'react';
import { formatLapTime } from '../Pages/Dashboard';

export default function StatsCards({laps = []}) {
    let mejorVuelta = 'N/A';
    if(laps.length > 0){
        const tiempos = laps.map(v => parseFloat(v.lap_time));
        mejorVuelta = formatLapTime(Math.min(...tiempos));
    }


    let mejorVelocidad = "0";
    if(laps.length > 0 && laps[0].telemetry_logs[0] && laps[0].telemetry_logs[0]){
        const velocidadesObj = laps[0].telemetry_logs[0].telemetry_json.speed;
        mejorVelocidad = Math.max(...velocidadesObj);
    }

    const stats = [
        { label: 'MEJOR VUELTA', value: mejorVuelta, color: 'border-purple-500' },
        { label: 'VELOCIDAD PUNTA', value: `${mejorVelocidad} KM/H`, color: 'border-red-600' },
        { label: 'VUELTAS TOTALES', value: laps.length, color: 'border-green-500' },
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {stats.map((stat, i) => (
                <div key={i} className={`bg-[#0f0f0f]/80 backdrop-blur-md border-l-4 ${stat.color} p-4 rounded-xl shadow-xl`}>
                    <p className="text-[10px] uppercase tracking-widest text-gray-500 font-bold">{stat.label}</p>
                    <p className="text-2xl font-black text-white italic">{stat.value}</p>
                </div>
            ))}
        </div>
    );
}