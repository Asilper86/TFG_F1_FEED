import React from "react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";

const GraficoTelemetria = ({data}) => {

    return (
        <div style={{ width: '100%', height: 400, backgroundColor: '#1a1a1a', padding: '20px' }}>
            <h2 style={{ color: 'white' }}>Telemetría: Velocidad vs Tiempo</h2>
            <ResponsiveContainer width="100%" height="100%">
                <LineChart data={data}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#444" />
                    <XAxis dataKey="point" stroke="#888" />
                    <YAxis stroke="#888" />
                    <Tooltip />
                    <Line type="monotone" dataKey="speed" stroke="#ff0000" dot={false} strokeWidth={2} />
                </LineChart>

                
            </ResponsiveContainer>

        </div>
    );
}

export default GraficoTelemetria;
