import React from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

export default function GraficoTelemetria({ data, visibleMetrics = { speed: true, throttle: true, brake: true, gear: true } }) {
    if (!data || data.length === 0) {
        return (
            <div className="w-full h-[350px] flex items-center justify-center bg-[#23262A] rounded-lg border border-[#2d3136]">
                <span className="text-gray-500 tracking-widest text-xs uppercase">Esperando datos de telemetría...</span>
            </div>
        );
    }

    const CustomTooltip = ({ active, payload, label }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-[#1A1D20] border border-[#333] p-3 rounded shadow-xl">
                    <p className="text-[10px] text-gray-400 mb-2 uppercase">Distancia: {Math.round(label)}m</p>
                    {payload.map((entry, index) => (
                        <div key={index} className="flex items-center justify-between gap-4 mb-1">
                            <span style={{ color: entry.color }} className="text-[10px] font-bold uppercase">{entry.name}:</span>
                            <span className="text-white font-mono text-xs">{entry.value}</span>
                        </div>
                    ))}
                </div>
            );
        }
        return null;
    };

    return (
        <div className="w-full h-[350px] bg-[#23262A] p-4 rounded-lg border border-[#2d3136]">
            <ResponsiveContainer width="100%" height="100%">
                <LineChart data={data} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#2d3136" vertical={false} />
                    <XAxis
                        dataKey="distance"
                        type="number"
                        domain={['dataMin', 'dataMax']}
                        stroke="#555"
                        tick={{ fontSize: 10 }}
                        tickFormatter={(v) => `${Math.round(v)}m`}
                    />
                    <YAxis yAxisId="left" stroke="#888" tick={{ fontSize: 10 }} domain={[0, 360]} />
                    <YAxis yAxisId="right" orientation="right" stroke="#888" tick={{ fontSize: 10 }} domain={[0, 100]} />

                    <Tooltip content={<CustomTooltip />} />

                    {visibleMetrics.speed && (
                        <Line
                            yAxisId="left"
                            type="monotone"
                            name="SPEED"
                            dataKey="speed"
                            stroke="#3FA9F5"
                            dot={false}
                            strokeWidth={2}
                            isAnimationActive={false}
                        />
                    )}
                    {visibleMetrics.throttle && (
                        <Line
                            yAxisId="right"
                            type="monotone"
                            name="THROTTLE"
                            dataKey="throttle"
                            stroke="#10b981"
                            dot={false}
                            strokeWidth={2}
                            isAnimationActive={false}
                        />
                    )}
                    {visibleMetrics.brake && (
                        <Line
                            yAxisId="right"
                            type="monotone"
                            name="BRAKE"
                            dataKey="brake"
                            stroke="#E10600"
                            dot={false}
                            strokeWidth={2}
                            isAnimationActive={false}
                        />
                    )}

                    {visibleMetrics.gear && (
                        <Line
                            yAxisId="right"  
                            type="stepAfter" 
                            name="GEAR"
                            dataKey="gear"
                            stroke="#eab308"
                            dot={false}
                            strokeWidth={2}
                            isAnimationActive={false}
                        />
                    )}

                    <Line
                        yAxisId="left"
                        type="monotone"
                        dataKey="speedGhost"
                        stroke="#3FA9F5"
                        strokeWidth={1.5}
                        strokeOpacity={0.2}
                        strokeDasharray="5 5"
                        dot={false}
                        name="BEST REFERENCE"
                        isAnimationActive={false}
                    />
                </LineChart>
            </ResponsiveContainer>
        </div>
    );
}
