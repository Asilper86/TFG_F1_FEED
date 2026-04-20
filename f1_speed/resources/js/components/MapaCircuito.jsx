import React, { useMemo } from "react";

export default function MapaCircuito({ datoCircuito, posicionDirecto }) {
    if (!datoCircuito || !datoCircuito.world_x || datoCircuito.world_x.length === 0) {
        return (
            <div className="bg-[#23262A] p-8 rounded-lg border border-[#2d3136] text-center h-[320px] flex items-center justify-center">
                <p className="text-[10px] uppercase tracking-[0.3em] text-gray-600 animate-pulse italic font-black">Waiting for Track Path...</p>
            </div>
        );
    }

    const { minX, maxX, minZ, maxZ, width, height, centerX, centerZ } = useMemo(() => {
        const x = datoCircuito.world_x;
        const z = datoCircuito.world_z;
        const minX = Math.min(...x);
        const maxX = Math.max(...x);
        const minZ = Math.min(...z);
        const maxZ = Math.max(...z);
        
        return {
            minX, maxX, minZ, maxZ,
            width: maxX - minX,
            height: maxZ - minZ,
            centerX: (maxX + minX) / 2,
            centerZ: (maxZ + minZ) / 2
        };
    }, [datoCircuito]);

    const size = 260;
    const padding = 40;
    const availableSize = size - padding;
    const scale = availableSize / Math.max(width, height);

    const points = useMemo(() => {
        return datoCircuito.world_x.map((x, i) => {
            const z = datoCircuito.world_z[i];
            const posX = (x - centerX) * scale + (size / 2);
            const posZ = (z - centerZ) * scale + (size / 2);
            return `${posX},${posZ}`;
        }).join(' ');
    }, [datoCircuito, scale, centerX, centerZ]);

    const carX = posicionDirecto ? (posicionDirecto.x - centerX) * scale + (size / 2) : null;
    const carZ = posicionDirecto ? (posicionDirecto.z - centerZ) * scale + (size / 2) : null;

    return (
        <div className="bg-[#23262A] p-6 rounded-lg border border-[#2d3136] flex flex-col items-center relative overflow-hidden group">
            <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#E10600]/50 to-transparent"></div>
            
            <h3 className="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400 mb-4 italic self-start opacity-70">
                Live Track Map
            </h3>
            
            <div className="relative">
                <svg 
                    width={size} 
                    height={size} 
                    viewBox={`0 0 ${size} ${size}`}
                    className="transform rotate-180 drop-shadow-[0_0_15px_rgba(0,0,0,0.5)]"
                >
                    <defs>
                        <linearGradient id="trackGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stopColor="#4B5563" />
                            <stop offset="50%" stopColor="#9CA3AF" />
                            <stop offset="100%" stopColor="#4B5563" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="2.5" result="coloredBlur"/>
                            <feMerge>
                                <feMergeNode in="coloredBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>

                    <polyline
                        points={points}
                        fill="none"
                        stroke="black"
                        strokeWidth="5"
                        strokeOpacity="0.2"
                        className="translate-y-1 translate-x-1"
                    />

                 
                    <path
                        points={points}
                        fill="none"
                        stroke="url(#trackGradient)"
                        strokeWidth="2.5"
                        strokeLinejoin="round"
                        strokeLinecap="round"
                        active="true"
                        filter="url(#glow)"
                    />

            
                    {carX !== null && (
                        <g filter="url(#glow)">
                            <circle 
                                cx={carX} 
                                cy={carZ} 
                                r="8" 
                                fill="rgba(225, 6, 0, 0.3)" 
                                className="animate-ping"
                            />
                            <circle 
                                cx={carX} 
                                cy={carZ} 
                                r="4.5" 
                                fill="#E10600" 
                                className="ring-2 ring-white"
                            />
                            <circle 
                                cx={carX} 
                                cy={carZ} 
                                r="1.5" 
                                fill="white" 
                            />
                        </g>
                    )}
                </svg>
                
                <div className="absolute bottom-[-10px] right-0 flex gap-4 opacity-50">
                    <div className="text-[8px] font-mono text-gray-500 uppercase">X: {posicionDirecto?.x.toFixed(0)}</div>
                    <div className="text-[8px] font-mono text-gray-500 uppercase">Z: {posicionDirecto?.z.toFixed(0)}</div>
                </div>
            </div>
        </div>
    );
}
