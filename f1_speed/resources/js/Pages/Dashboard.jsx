import React, { useEffect, useState } from 'react';
import { Head, usePage, router, Link } from '@inertiajs/react';
import StatsCards from '../Components/StatsCards';
import Navbar from '../Components/NavBar';
import LapsTable from '../Components/LapsTable';
import GraficoTelemetria from '../Components/GraficoTelemetria';
import MapaCircuito from '../Components/MapaCircuito';
import ShareModal from '../Components/ShareModal';
import axios from 'axios';
import { TRACKS, TEAMS } from './SessionSetup';

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

    const [isEngineRunning, setEngineRunning] = useState(false);
    const [engineLoading, setEngineLoading] = useState(false);

    const activeTrackName = TRACKS.find(t => t.id === activeSession?.track_id)?.name || activeSession?.track_id || 'Unknown';
    const activeCarName = TEAMS.find(t => t.id === activeSession?.car_id)?.name || activeSession?.car_id || 'Unknown';


    useEffect(() => {
        checkEngineStatus();
        const interval = setInterval(checkEngineStatus, 5000);
        return () => clearInterval(interval);
    }, []);


    const checkEngineStatus = async () => {
        try {
            const response = await axios.get('/telemetry/check-engine');
            setEngineRunning(response.data.running);
        } catch (error) {
            console.error("Error checking engine:", error)
        }
    }

    const handleStartEngine = async () => {
        setEngineLoading(true);
        try {
            await axios.post('/telemetry/start-engine');
            setEngineRunning(true);
        } catch (error) {
            alert("Error al arrancar el motor.");
        } finally {
            setEngineLoading(false);
        }
    };

    const handleStopEngine = async () => {
        setEngineLoading(true);
        try {
            await axios.post('/telemetry/stop-engine');
            setEngineRunning(false);
        } catch (error) {
            alert("Error al apagar el motor.");
        } finally {
            setEngineLoading(false);
        }
    };

    React.useEffect(() => {
        const radar = setInterval(() => {
            setEncendido(true)
            router.reload({
                only: ['laps', 'activeSession'],
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setTimeout(() => setEncendido(false), 500)
            });
        }, 5000);

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
        setVisibleMetrics(prev => ({
            ...prev,
            [metrica]: !prev[metrica]
        }));
    };

    const handleDeleteSession = (id) => {
        router.delete(`/session/${id}`);
    };

    return (
        <div className="min-h-screen bg-[#121418] text-white font-sans selection:bg-[#E10600]/30">
            <Head title="Telemetry Dashboard" />

            <Navbar user={auth.user} />

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

                {/* PANEL DE CONTROL DE TELEMETRÍA */}
                <div className="bg-[#1B1D21] border border-[#2d3136] rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between shadow-2xl relative overflow-hidden gap-6">
                    <div className={`absolute top-0 right-0 w-32 h-32 blur-[80px] opacity-20 ${isEngineRunning ? 'bg-green-500' : 'bg-red-500'}`}></div>

                    <div className="relative z-10 flex items-center gap-6">
                        <div>
                            <h3 className="text-xs font-black uppercase tracking-[0.2em] text-gray-500 mb-1 italic">Telemetry Engine</h3>
                            <div className="flex items-center gap-3">
                                <div className={`w-3 h-3 rounded-full animate-pulse ${isEngineRunning ? 'bg-[#00D100] shadow-[0_0_10px_#00D100]' : 'bg-[#E10600]'}`}></div>
                                <span className="text-xl font-black uppercase tracking-tighter italic">
                                    {isEngineRunning ? 'SYSTEM: ONLINE' : 'SYSTEM: OFFLINE'}
                                </span>
                            </div>
                        </div>

                        {activeSession && (
                            <div className="h-10 w-px bg-[#2d3136] hidden md:block"></div>
                        )}

                        {activeSession && (
                            <div>
                                <h3 className="text-xs font-black uppercase tracking-[0.2em] text-gray-500 mb-1 italic">Active Track</h3>
                                <p className="text-lg font-bold uppercase tracking-tighter text-white">
                                    {activeTrackName} <span className="text-gray-600 mx-2">|</span> {activeCarName}
                                </p>
                            </div>
                        )}
                    </div>

                    <div className="flex flex-wrap gap-4 relative z-10 w-full md:w-auto">
                        <button
                            onClick={isEngineRunning ? handleStopEngine : handleStartEngine}
                            disabled={engineLoading}
                            className={`flex-1 md:flex-none px-8 py-3 rounded font-black uppercase italic tracking-tighter transition-all active:scale-95 flex items-center justify-center gap-2 ${isEngineRunning
                                    ? 'bg-[#E10600]/10 text-[#E10600] border border-[#E10600]/30 hover:bg-[#E10600] hover:text-white'
                                    : 'bg-[#E10600] text-white hover:bg-[#ff0700] shadow-[0_0_20px_rgba(225,6,0,0.3)]'
                                }`}
                        >
                            {engineLoading ? 'PROCESSING...' : isEngineRunning ? 'STOP ENGINE' : 'START ENGINE'}
                        </button>

                        {activeSession && (
                            <button
                                onClick={() => handleDeleteSession(activeSession.id)}
                                className="flex-1 md:flex-none px-6 py-3 rounded border border-[#E10600]/30 text-[#E10600] hover:bg-[#E10600] hover:text-white font-black uppercase italic tracking-tighter transition-all text-xs"
                            >
                                DELETE SESSION
                            </button>
                        )}
                    </div>
                </div>

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
                            <div className="hidden lg:flex items-center gap-4 bg-[#1B1D21] px-4 py-2 rounded-lg border border-[#2d3136]">
                                <div className="text-left">
                                    <p className="text-[8px] uppercase tracking-[0.2em] text-gray-500 font-bold mb-0.5">Active Setup</p>
                                    <p className="text-[10px] font-bold uppercase tracking-widest text-white">
                                        {activeTrackName} | {activeCarName} | {activeSession.weather}
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
                    <div className="lg:col-span-1 space-y-6">
                        <div className="flex items-center justify-between mb-3 px-1">
                            <h4 className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 italic">
                                Session History
                                <span className="ml-2 px-1.5 py-0.5 bg-[#E10600] text-white rounded-sm text-[9px] not-italic">
                                    {safeLaps.length} LAPS
                                </span>
                            </h4>
                        </div>
                        <LapsTable laps={safeLaps} onSelectLap={setSelectedLap} selectedId={selectedLap?.id} />
                    </div>
                    <div className="lg:col-span-3">
                        <StatsCards
                            laps={safeLaps}
                            selectedLap={selectedLap}
                            activeSession={activeSession}
                        />
                        <div className="flex flex-col md:flex-row md:items-center justify-between mb-4 mt-8 bg-[#1B1D21] p-3 md:px-4 rounded border border-[#2d3136] gap-3">
                            <h4 className="text-[11px] font-bold uppercase tracking-widest text-white">PERFORMANCE TRACE</h4>

                            <div className="flex flex-wrap gap-2">
                                <button
                                    onClick={() => cambiarMetricas('speed')}
                                    className={`px-3 sm:px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.speed ? 'bg-[#3FA9F5]/20 text-[#3FA9F5] border border-[#3FA9F5]/50' : 'bg-[#121418] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    SPEED
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('throttle')}
                                    className={`px-3 sm:px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.throttle ? 'bg-[#10b981]/20 text-[#10b981] border border-[#10b981]/50' : 'bg-[#121418] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    THROTTLE
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('brake')}
                                    className={`px-3 sm:px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.brake ? 'bg-[#E10600]/20 text-[#E10600] border border-[#E10600]/50' : 'bg-[#121418] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    BRAKE
                                </button>
                                <button
                                    onClick={() => cambiarMetricas('gear')}
                                    className={`px-3 sm:px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase rounded transition-colors ${visibleMetrics.gear ? 'bg-[#eab308]/20 text-[#eab308] border border-[#eab308]/50' : 'bg-[#121418] text-gray-500 border border-[#2d3136] hover:text-gray-300'}`}
                                >
                                    GEAR
                                </button>
                            </div>
                        </div>
                        <GraficoTelemetria data={chartData} visibleMetrics={visibleMetrics} />

                    </div>
                </div>
            </main>
            <ShareModal />
        </div>
    );
}