import React, { useState } from 'react';
import { Head, usePage, router, Link } from '@inertiajs/react';
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





export default function Dashboard({ laps, activeSession }) {
    const { auth } = usePage().props;
    const safeLaps = laps || [];
    const [encendido, setEncendido] = useState(false);
    const [selectedLap, setSelectedLap] = React.useState(safeLaps[0] || null);
    const [visibleMetrics, setVisibleMetrics] = useState({
        speed: true,
        throttle: false,
        brake: false,
        gear: false,
    })


    React.useEffect(() => {
        const radar = setInterval(() => {
            setEncendido(true)
            router.reload({
                only: ['laps'],
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setTimeout(() => setEncendido(false), 800)
            });
        }, 10000);

        return () => clearInterval(radar);

    }, []);

    React.useEffect(() => {
        if (!selectedLap && safeLaps.length > 0) {
            setSelectedLap(safeLaps[0]);
        }
    }, [laps]);

    const telemetryData = selectedLap?.telemetry_logs?.[0]?.telemetry_json;

    const mejorVuelta = safeLaps.length > 0 ? [...safeLaps].sort((a, b) => a.lap_time - b.lap_time)[0] 
    : null;

    const vueltaFantasma = mejorVuelta?.telemetry_logs?.[0]?.telemetry_json;

    const chartData = telemetryData?.speed?.map((s, index) => ({
        point: index,
        distance: telemetryData.distance?.[index] || index,
        speed: s,
        speedGhost: vueltaFantasma?.speed?.[index],
        throttle: telemetryData.throttle[index],
        brake: telemetryData.brake[index],
        gear: telemetryData.gear[index]
    })).filter(d => d.distance >= 0) || [];

    const cambiarMetricas = (metrica) => {
        setVisibleMetrics(prev => ({ ...prev, [metrica]: !prev[metrica] }));
    }

    return (
        <div className="min-h-screen bg-[#1B1D21] text-white font-sans selection:bg-[#E10600]/30 pb-12">
            <Head title="Telemetry Dashboard" />

            <Navbar user={auth.user} />

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

                <div className="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-4 border-b border-[#2d3136]">
                    <div className="flex items-center gap-6">
                        <h1 className="text-[22px] font-bold tracking-wide text-white uppercase flex items-center gap-3">
                            <span className="text-[#E10600] text-3xl font-black">/</span> Analisis de Sesiones
                            {encendido ? (
                                <span className="text-[9px] uppercase tracking-widest text-[#10b981] ml-4 flex items-center gap-1.5 font-mono">
                                    <span className="w-1.5 h-1.5 bg-[#10b981] rounded-full animate-ping"></span> SYNCING_
                                </span>
                            ) : (
                                <span className="text-[9px] uppercase tracking-widest text-gray-600 ml-4 flex items-center gap-1.5 font-mono">
                                    <span className="w-1.5 h-1.5 bg-gray-600 rounded-full"></span> LIVE_MONITOR
                                </span>
                            )}
                        </h1>

                        {activeSession && (
                            <div className="hidden lg:flex items-center gap-4 bg-[#23262A] px-4 py-2 rounded-lg border border-[#2d3136]">
                                <div className="text-left">
                                    <p className="text-[8px] uppercase tracking-[0.2em] text-gray-500 font-bold mb-0.5">Active Setup</p>
                                    <p className="text-[10px] font-bold uppercase tracking-widest text-white">
                                        {activeSession.track_id} | {activeSession.car_id} | {activeSession.weather}
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>

                    <Link 
                        href="/session/setup"
                        className="mt-4 md:mt-0 bg-[#E10600] hover:bg-[#ff0700] text-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded italic transition-all active:scale-95"
                    >
                        + Configurar Sesión
                    </Link>
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
                        <div className="flex items-center justify-between mb-4 mt-8 bg-[#23262A] p-2 px-4 rounded border border-[#2d3136]">
                            <h4 className="text-[11px] font-bold uppercase tracking-widest text-white">PERFORMANCE TRACE</h4>

                            <div className="flex space-x-2">
                                <button
                                    onClick={() => cambiarMetricas('speed')}
                                    className={`px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.speed ? 'bg-[#3FA9F5]/20 text-[#3FA9F5] border border-[#3FA9F5]/50' : 'bg-[#1B1D21] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    SPEED
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('throttle')}
                                    className={`px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.throttle ? 'bg-[#10b981]/20 text-[#10b981] border border-[#10b981]/50' : 'bg-[#1B1D21] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    THROTTLE
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('brake')}
                                    className={`px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.brake ? 'bg-[#E10600]/20 text-[#E10600] border border-[#E10600]/50' : 'bg-[#1B1D21] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    BRAKE
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('gear')}
                                    className={`px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.gear ? 'bg-[#eab308]/20 text-[#eab308] border border-[#eab308]/50' : 'bg-[#1B1D21] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    GEAR
                                </button>
                            </div>
                        </div>
                        <GraficoTelemetria data={chartData} visibleMetrics={visibleMetrics} />
                    </div>
                </div>
            </main>
        </div>
    );
}