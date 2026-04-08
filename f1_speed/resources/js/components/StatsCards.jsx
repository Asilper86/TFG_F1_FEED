import React from 'react';
import { formatLapTime } from '../Pages/Dashboard';

export default function StatsCards({ laps = [] }) {
    let mejorVuelta = '--:--.---';
    if(laps.length > 0){
        mejorVuelta = formatLapTime(Math.min(...laps.map(v => parseFloat(v.lap_time))));
    }

    let mejorVelocidad = "0";
    if(laps.length > 0 && laps[0].telemetryLogs?.[0]){
        mejorVelocidad = Math.max(...laps[0].telemetryLogs[0].telemetry_json.speed);
    }

    const stats = [
        { label: 'BEST LAP', value: mejorVuelta },
        { label: 'TOP SPEED (KM/H)', value: mejorVelocidad },
        { label: 'TOTAL LAPS', value: laps.length },
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {stats.map((stat, i) => (
                <div key={i} className="bg-[#23262A] p-6 rounded-lg flex flex-col justify-center border border-[#2d3136]">
                    <span className="text-3xl font-bold text-white mb-2">{stat.value}</span>
                    <span className="text-[11px] font-semibold text-gray-400 uppercase tracking-widest">{stat.label}</span>
                </div>
            ))}
        </div>
    );
}