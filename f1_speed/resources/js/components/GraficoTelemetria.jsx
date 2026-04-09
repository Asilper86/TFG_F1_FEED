import React from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';

export default function GraficoTelemetria({ data, visibleMetrics = { speed: true, throttle: true, brake: true } }) {
    if (!data || data.length === 0) {
        return (
            <div className="w-full h-[375px] flex items-center justify-center bg-[#23262A] rounded-lg border border-[#2d3136]">
                <span className="text-gray-500 tracking-widest text-xs uppercase">No telemetry data</span>
            </div>
        );
    }

    const CustomTooltip = ({ active, payload }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-[#1A1D20] border border-[#333] p-4 rounded shadow-xl">
                    {payload.map((entry, index) => (
                        <div key={index} className="flex items-center gap-6 justify-between mb-2 last:mb-0">
                            <span style={{ color: entry.color }} className="text-[11px] font-semibold uppercase">{entry.name}</span>
                            <span className="text-white font-mono font-bold text-sm">{entry.value}</span>
                        </div>
                    ))}
                </div>
            );
        }
        return null;
    };

    return (
        <div className="w-full h-[375px] bg-[#23262A] p-6 pt-8 rounded-lg border border-[#2d3136]">
            <ResponsiveContainer width="100%" height="100%">
                <LineChart data={data} margin={{ top: 5, right: 5, left: -25, bottom: 0 }}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#2d3136" vertical={false} />
                    <XAxis dataKey="point" stroke="#555" tick={false} axisLine={{ stroke: '#333' }} />
                    <YAxis yAxisId="left" stroke="#888" tick={{ fill: '#888', fontSize: 11 }} axisLine={false} tickLine={false} />
                    <YAxis yAxisId="right" orientation="right" stroke="#888" domain={[0, 100]} tick={{ fill: '#888', fontSize: 11 }} axisLine={false} tickLine={false} />

                    <Tooltip content={<CustomTooltip />} cursor={{ stroke: '#444', strokeWidth: 1 }} />
                    <Legend wrapperStyle={{ paddingTop: '15px' }} iconType="circle" iconSize={6} />
                    {visibleMetrics.speed && (
                        <Line yAxisId="left" type="monotone" name="SPEED" dataKey="speed" stroke="#3FA9F5" dot={false} strokeWidth={2} activeDot={{ r: 4, fill: '#3FA9F5', stroke: '#1A1D20', strokeWidth: 2 }} />
                    )}
                    {visibleMetrics.throttle && (
                        <Line yAxisId="right" type="monotone" name="THROTTLE" dataKey="throttle" stroke="#10b981" dot={false} strokeWidth={2} />
                    )}
                    {visibleMetrics.brake && (
                        <Line yAxisId="right" type="monotone" name="BRAKE" dataKey="brake" stroke="#E10600" dot={false} strokeWidth={2} />
                    )}
                </LineChart>
            </ResponsiveContainer>
        </div>
    );
}
