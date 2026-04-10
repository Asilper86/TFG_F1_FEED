import React, { useMemo } from "react";

export default function MapaCircuito({ datoCircuito, posicionDirecto }) {
    if (!datoCircuito || !datoCircuito.world_x || datoCircuito.world_x.length === 0) {
        return (
            <div className="bg-[#23262A] p-8 rounded-lg border border-[#2d3136] text-center">
                <p className="text-[9px] uppercase tracking-widest text-gray-500 animate-pulse italic">Generando mapa del trazado...</p>
            </div>
        );
    }

    const limitCircuito = useMemo(() => {
        const minX = Math.min(...datoCircuito.world_x);
        const maxX = Math.max(...datoCircuito.world_x);
        const minZ = Math.min(...datoCircuito.world_z);
        const maxZ = Math.max(...datoCircuito.world_z);
        
        const width = maxX - minX;
        const height = maxZ - minZ;
        
        return { minX, maxX, minZ, maxZ, width, height };
    }, [datoCircuito]);

    const tamano = 250;
    const escala = (tamano * 0.8) / Math.max(limitCircuito.width, limitCircuito.height);

    const points = useMemo(() => {
        return datoCircuito.world_x.map((x, i) => {
            const z = datoCircuito.world_z[i];
            const posX = (x - limitCircuito.minX) * escala;
            const posZ = (z - limitCircuito.minZ) * escala;
            return `${posX},${posZ}`;
        }).join(' ');
    }, [datoCircuito, escala, limitCircuito]);

    const carX = posicionDirecto ? (posicionDirecto.x - limitCircuito.minX) * escala : null;
    const carZ = posicionDirecto ? (posicionDirecto.z - limitCircuito.minZ) * escala : null;

    return (
        <div className="bg-[#23262A] p-5 rounded-lg border border-[#2d3136] flex flex-col items-center">
            <h3 className="text-[9px] font-black uppercase tracking-[0.3em] text-gray-500 mb-6 italic">Live Track Map</h3>
            
            <svg 
                width={tamano} 
                height={tamano} 
                viewBox={`0 0 ${tamano} ${tamano}`}
                className="transform rotate-180"
            >
                <polyline
                    points={points}
                    fill="none"
                    stroke="#374151"
                    strokeWidth="3"
                    strokeLinejoin="round"
                    strokeLinecap="round"
                />

                {carX !== null && (
                    <circle 
                        cx={carX} 
                        cy={carZ} 
                        r="6" 
                        fill="#E10600" 
                        className="animate-pulse shadow-2xl"
                    />
                )}
            </svg>
        </div>
    );
}
