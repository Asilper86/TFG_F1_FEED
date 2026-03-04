import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';

const container = document.getElementById('react-app');

if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <div style={{ padding: '20px', backgroundColor: '#e2e8f0', borderRadius: '8px' }}>
                <h1 style={{ color: '#1e40af' }}>🏎️ ¡React y Laravel Conectados!</h1>
                <p>La telemetría de F1 está lista para despegar.</p>
            </div>
        </React.StrictMode>
    );
}