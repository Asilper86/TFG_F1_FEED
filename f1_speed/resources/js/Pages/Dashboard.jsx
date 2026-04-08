import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import StatsCards from '../Components/StatsCards';
import Navbar from '../components/NavBar';
import LapsTable from '../Components/LapsTable';
import GraficoTelemetria from '../Components/GraficoTelemetria';

export const formatLapTime = (totalSeconds) => {
    if (!totalSeconds) return '0:00.000';
    const minutos = Math.floor(totalSeconds / 60);
    const segundos = totalSeconds % 60;
    const formatoSegundos = segundos < 10 ? `0${segundos.toFixed(3)}` : segundos.toFixed(3);
    return `${minutos}:${formatoSegundos}`;
};

export default function Dashboard({ laps }) {
    const { auth } = usePage().props;
    const safeLaps = laps || [];

    const [selectedLap, setSelectedLap] = React.useState(safeLaps[0] || null);
    
    React.useEffect(() => {
        if (!selectedLap && safeLaps.length > 0) {
            setSelectedLap(safeLaps[0]);
        }
    }, [laps]);

    const telemetryData = selectedLap?.telemetryLogs?.[0]?.telemetry_json;
    const chartData = telemetryData?.speed?.map((s, index) => ({
        point: index,
        speed: s,
        throttle: telemetryData.throttle[index],
        brake: telemetryData.brake[index]
    })) || [];

    return (
        <div className="min-h-screen bg-[#1B1D21] text-white font-sans selection:bg-[#E10600]/30 pb-12">
            <Head title="Telemetry Dashboard" />
            
            <Navbar user={auth.user} />

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
                
                <div className="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-4 border-b border-[#2d3136]">
                    <div>
                        <h1 className="text-[22px] font-bold tracking-wide text-white uppercase flex items-center gap-3">
                            <span className="text-[#E10600] text-3xl font-black">/</span> Analisis de Sesiones
                        </h1>
                    </div>
                    
                    {safeLaps.length > 0 && (
                        <div className="min-w-[200px] mt-4 md:mt-0">
                            <select
                                className="w-full appearance-none bg-[#23262A] text-white font-mono text-sm p-3 px-4 rounded-lg border border-[#2d3136] outline-none cursor-pointer focus:border-[#E10600]"
                                value={selectedLap?.id || ''}
                                onChange={(e) => {
                                    const found = safeLaps.find(l => l.id == e.target.value);
                                    if (found) setSelectedLap(found);
                                }}
                            >
                                {safeLaps.map(lap => (
                                    <option key={lap.id} value={lap.id} className="bg-[#1B1D21]">
                                        LAP {lap.lap_number} -- {formatLapTime(lap.lap_time)}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div className="lg:col-span-1">
                        <LapsTable 
                            laps={safeLaps} 
                            onSelectLap={setSelectedLap} 
                            selectedId={selectedLap?.id} 
                        />
                    </div>
                    <div className="lg:col-span-3">
                        <StatsCards laps={safeLaps} />
                        <h4 className="text-[12px] font-bold uppercase tracking-wider text-white mb-4 mt-8">PERFORMANCE TRACE</h4>
                        <GraficoTelemetria data={chartData} />
                    </div>
                </div>
            </main>
        </div>
    );
}