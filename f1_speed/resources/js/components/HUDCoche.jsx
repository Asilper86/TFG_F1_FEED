import React from "react";

export default function HUDCoche({ status }) {
    if (!status) return (
        <div className="bg-[#23262A] p-6 rounded-lg border border-[#2d3136] text-center">
            <p className="text-[9px] uppercase tracking-widest text-gray-500 animate-pulse">Esperando Telemetría Live...</p>
        </div>
    );

    const { tyres_wear, alerones } = status;

    const getStrokeColor = (wear) => {
        if (wear > 70) return '#ef4444';
        if (wear > 40) return '#f97316'; 
        return '#10b981';
    };

    const TyreCircle = ({ value, label }) => (
        <div className="relative flex flex-col items-center">
            <svg className="w-16 h-16 transform -rotate-90">
                <circle cx="32" cy="32" r="28" stroke="#333" strokeWidth="4" fill="transparent" />
                <circle 
                    cx="32" cy="32" r="28" stroke={getStrokeColor(value)} 
                    strokeWidth="4" fill="transparent" 
                    strokeDasharray={175.9}
                    strokeDashoffset={175.9 - (175.9 * Math.min(value, 100)) / 100}
                    className="transition-all duration-1000"
                />
            </svg>
            <div className="absolute inset-0 flex items-center justify-center flex-col pt-1">
                <span className="text-[10px] font-black text-white leading-none">{value}%</span>
            </div>
            <span className="text-[7px] text-gray-500 mt-1 font-bold">{label}</span>
        </div>
    );

    return (
        <div className="bg-[#23262A] p-5 rounded-lg border border-[#2d3136] shadow-2xl relative overflow-hidden">
            <div className="flex justify-between items-center mb-6">
                <h3 className="text-[9px] font-black uppercase tracking-[0.3em] text-gray-500 italic">Live Car Status</h3>
                <span className="flex h-2 w-2 rounded-full bg-[#E10600] animate-pulse"></span>
            </div>

            <div className="grid grid-cols-2 gap-y-8 gap-x-4 relative">
                <div className="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                   <div className="w-12 h-32 border-4 border-white rounded-full"></div>
                </div>

                <TyreCircle value={tyres_wear[2]} label="FL" />
                <TyreCircle value={tyres_wear[3]} label="FR" />
                <TyreCircle value={tyres_wear[0]} label="RL" />
                <TyreCircle value={tyres_wear[1]} label="RR" />
            </div>

            <div className="mt-8 pt-4 border-t border-white/5 flex justify-around">
               <div className={`px-2 py-0.5 rounded text-[7px] font-bold ${alerones.aleron_delantero_izq > 0 ? 'bg-red-500/20 text-red-500' : 'bg-green-500/10 text-green-500'}`}>FW_L</div>
               <div className={`px-2 py-0.5 rounded text-[7px] font-bold ${alerones.aleron_delantero_der > 0 ? 'bg-red-500/20 text-red-500' : 'bg-green-500/10 text-green-500'}`}>FW_R</div>
               <div className={`px-2 py-0.5 rounded text-[7px] font-bold ${alerones.aleron_trasero > 0 ? 'bg-red-500/20 text-red-500' : 'bg-green-500/10 text-green-500'}`}>RW_ANY</div>
            </div>
        </div>
    );
}
