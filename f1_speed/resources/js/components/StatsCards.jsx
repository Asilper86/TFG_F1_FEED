import React from 'react';

export default function StatsCards() {
    const stats = [
        { label: 'MEJOR VUELTA', value: '1:14.450', color: 'border-purple-500' },
        { label: 'VELOCIDAD PUNTA', value: '324 KM/H', color: 'border-red-600' },
        { label: 'VUELTAS TOTALES', value: '24', color: 'border-green-500' },
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