import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import Navbar from '../Components/NavBar';

export const TRACKS = [
    { id: '0', name: 'Melbourne', country: 'Australia' },
    { id: '1', name: 'Paul Ricard', country: 'France' },
    { id: '2', name: 'Shanghai', country: 'China' },
    { id: '3', name: 'Sakhir (Bahrain)', country: 'Bahrain' },
    { id: '4', name: 'Catalunya', country: 'Spain' },
    { id: '5', name: 'Monaco', country: 'Monaco' },
    { id: '6', name: 'Montreal', country: 'Canada' },
    { id: '7', name: 'Silverstone', country: 'UK' },
    { id: '8', name: 'Hockenheim', country: 'Germany' },
    { id: '9', name: 'Hungaroring', country: 'Hungary' },
    { id: '10', name: 'Spa', country: 'Belgium' },
    { id: '11', name: 'Monza', country: 'Italy' },
    { id: '12', name: 'Singapore', country: 'Singapore' },
    { id: '13', name: 'Suzuka', country: 'Japan' },
    { id: '14', name: 'Abu Dhabi', country: 'UAE' },
    { id: '15', name: 'Texas', country: 'USA' },
    { id: '16', name: 'Brazil', country: 'Brazil' },
    { id: '17', name: 'Austria', country: 'Austria' },
    { id: '18', name: 'Sochi', country: 'Russia' },
    { id: '19', name: 'Mexico', country: 'Mexico' },
    { id: '20', name: 'Baku (Azerbaijan)', country: 'Azerbaijan' },
    { id: '26', name: 'Zandvoort', country: 'Netherlands' },
    { id: '27', name: 'Imola', country: 'Italy' },
    { id: '28', name: 'Portimão', country: 'Portugal' },
    { id: '29', name: 'Jeddah', country: 'Saudi Arabia' },
    { id: '30', name: 'Miami', country: 'USA' },
    { id: '31', name: 'Las Vegas', country: 'USA' },
    { id: '32', name: 'Losail', country: 'Qatar' },
];

export const TEAMS = [
    { id: '0', name: 'Mercedes', color: '#27F4D2' },
    { id: '1', name: 'Ferrari', color: '#E10600' },
    { id: '2', name: 'Red Bull Racing', color: '#3671C6' },
    { id: '3', name: 'Williams', color: '#64C4FF' },
    { id: '4', name: 'Aston Martin', color: '#229971' },
    { id: '5', name: 'Alpine', color: '#0093CC' },
    { id: '6', name: 'AlphaTauri / VCARB', color: '#6692FF' },
    { id: '7', name: 'Haas', color: '#B6BABD' },
    { id: '8', name: 'McLaren', color: '#FF8000' },
    { id: '9', name: 'Sauber / Stake F1', color: '#52E252' },
];

export default function SessionSetup({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        track_id: '',
        car_id: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/session/setup');
    };

    return (
        <div className="min-h-screen bg-[#121418] text-white font-sans pb-12">
            <Head title="Session Setup" />
            <Navbar user={auth?.user} />

            <main className="max-w-4xl mx-auto px-4 mt-10">
                <div className="mb-10 text-center">
                    <h1 className="text-3xl font-black uppercase tracking-tighter italic text-white">
                        <span className="text-[#E10600]">/</span> Session Configuration
                    </h1>
                    <p className="text-gray-500 text-xs mt-2 tracking-widest uppercase">Select your track and car</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-4">
                            <label className="text-[11px] font-bold uppercase tracking-widest text-[#E10600]">Circuit Selection</label>
                            <select 
                                value={data.track_id}
                                onChange={e => setData('track_id', e.target.value)}
                                className="w-full bg-[#1B1D21] border border-[#2d3136] rounded-lg p-4 text-sm text-white focus:border-[#E10600] outline-none transition-all"
                            >
                                <option value="">Select track...</option>
                                {TRACKS.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                            </select>
                        </div>
                        <div className="space-y-4">
                            <label className="text-[11px] font-bold uppercase tracking-widest text-[#E10600]">Car / Team</label>
                            <select 
                                value={data.car_id}
                                onChange={e => setData('car_id', e.target.value)}
                                className="w-full bg-[#1B1D21] border border-[#2d3136] rounded-lg p-4 text-sm text-white focus:border-[#E10600] outline-none transition-all"
                            >
                                <option value="">Select car...</option>
                                {TEAMS.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                            </select>
                        </div>
                    </div>

                    <button 
                        disabled={processing}
                        type="submit"
                        className="w-full bg-[#E10600] hover:bg-[#ff0700] text-white font-black uppercase italic tracking-tighter py-5 rounded-xl text-lg transition-transform active:scale-95 disabled:opacity-50"
                    >
                        {processing ? 'CREATING SESSION...' : 'CREATE NEW SESSION'}
                    </button>
                </form>
            </main>
        </div>
    );
}
