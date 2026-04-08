<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>F1 SPEED | Telemetry System</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
            body { font-family: 'Inter', sans-serif; background-color: #1B1D21; }
        </style>
    </head>
    <body class="antialiased text-white overflow-x-hidden bg-[#1B1D21]">
        
        <div class="relative z-10 min-h-screen flex flex-col">
            
            <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto w-full border-b border-[#2d3136]">
                <div class="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                    <span class="text-[#E10600]">/</span> F1 SPEED
                </div>
                <div class="space-x-6 text-[11px] uppercase tracking-widest font-bold">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard-f1') }}" class="text-gray-400 hover:text-white transition">Panel de Control</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Entrar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-[#E10600] text-white px-5 py-2.5 rounded hover:bg-red-700 transition">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>

            <main class="flex-grow flex flex-col items-center justify-center text-center px-4">
                <div class="inline-block px-4 py-1.5 border border-[#2d3136] rounded bg-[#23262A] text-[#E10600] text-[10px] uppercase tracking-[0.2em] font-bold mb-8">
                    Data Stream Active v2.0
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black tracking-tight uppercase leading-none text-white mb-6">
                    Domina la <br>
                    <span class="text-gray-400">Telemetría</span>
                </h1>
                
                <p class="max-w-2xl text-gray-400 text-lg font-normal leading-relaxed">
                    Analiza cada curva, cada frenada y cada milisegundo. Conecta tu simulador y visualiza el rendimiento de tu monoplaza en tiempo real con precisión brutal.
                </p>

                <div class="mt-12 flex flex-col sm:flex-row gap-4 items-center">
                    <a href="{{ route('register') }}" class="bg-[#E10600] text-white font-bold uppercase tracking-widest px-10 py-4 rounded hover:bg-red-700 transition-colors">
                        Empezar Análisis
                    </a>
                    <div class="flex items-center px-6 py-4 text-gray-400 text-[11px] uppercase tracking-widest font-bold border border-[#2d3136] rounded bg-[#23262A]">
                        Compatible con F1 24 / 23
                    </div>
                </div>
            </main>

            <footer class="py-10 text-center border-t border-[#2d3136]">
                <div class="flex justify-center space-x-8 mb-4">
                    <span class="text-[10px] font-bold tracking-[0.3em] text-[#3FA9F5]">PYTHON</span>
                    <span class="text-[10px] font-bold tracking-[0.3em] text-[#E10600]">LARAVEL</span>
                    <span class="text-[10px] font-bold tracking-[0.3em] text-[#10b981]">REACT</span>
                </div>
                <p class="text-[10px] text-gray-600 uppercase tracking-widest">
                    &copy; {{ date('Y') }} F1 SPEED Project — Ingeniería de Datos
                </p>
            </footer>
        </div>

    </body>
</html>