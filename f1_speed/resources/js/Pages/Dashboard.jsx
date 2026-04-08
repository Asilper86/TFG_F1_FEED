import React, { useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import StatsCards from '../Components/StatsCards';
import Navbar from '../components/NavBar';
import LapsTable from '../Components/LapsTable';
import GraficoTelemetria from '../Components/GraficoTelemetria';



export default function Dashboard({ laps }) {
    const {auth} = usePage().props;

    const safeLaps = laps || [];
    

    const [selectedLap, setSelectedLap] = React.useState(safeLaps[0] || null);
    React.useEffect(() => {
        setSelectedLap(laps[0] || null)
    }, [laps]);

    const telemetryData = selectedLap?.telemetry_logs?.[0]?.telemetry_json;
    const chartData = telemetryData?.speed?.map((s, index) => ({
        point: index,
        speed: s,
        throttle: telemetryData.throttle[index],
        brake: telemetryData.brake[index]
    })) || [];

    return (
        
        <div className="min-h-screen bg-[#050505] text-white font-sans">
            <Head title="Race Control" />
            <Navbar user={auth.user} />
            {}
            <div className="max-w-7xl mx-auto px-6 space-y-6">
                <StatsCards laps={safeLaps} />
                
                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6 pb-12">
                    <div className="lg:col-span-1">
                        <LapsTable 
                            laps={safeLaps} 
                            onSelectLap={setSelectedLap} 
                            selectedId={selectedLap?.id} 
                        />
                    </div>
                    <div className="lg:col-span-3 bg-[#0f0f0f] p-6 rounded-2xl border border-white/5 shadow-2xl">
                        <GraficoTelemetria 
                            data={chartData} 
                            lapNumber={selectedLap?.lap_number} 
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}