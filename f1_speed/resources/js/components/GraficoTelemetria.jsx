import React from "react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";

const GraficoTelemetria = ({data}) => {

    return (
        <div style={{ width: '100%', height: 400, backgroundColor: '#1a1a1a', padding: '20px' }}>
            <h2 style={{ color: 'white' }}>Telemetría Avanzada</h2>
            <ResponsiveContainer width="100%" height="100%">
                <LineChart data={data}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#333" />
                    <XAxis dataKey="point" stroke="#888" tick={false}/>
                    <Tooltip contentStyle={{backgroundColor: "#111", borderColor: "#333"}}/>
                    <Legend />
                    <YAxis yAxisId="left" stroke="#888"/>
                    <YAxis yAxisId="right" orientation="right" stroke="#888" domain={[0, 100]} />
                    <Line yAxisId="left" type="monotone" name="Velocidad (km/h)" dataKey="speed" stroke="#e5e5e5" dot={false} strokeWidth={3} />
                    <Line yAxisId="right" type="monotone" name="Acelerador (%)" dataKey="throttle" stroke="#22c55e" dot={false} strokeWidth={2} opacity={0.6} />
                    <Line yAxisId="right" type="monotone" name="Freno (%)" dataKey="brake" stroke="#dc2626" dot={false} strokeWidth={2} opacity={0.6} />
                </LineChart>

                
            </ResponsiveContainer>

        </div>
    );
}

export default GraficoTelemetria;
