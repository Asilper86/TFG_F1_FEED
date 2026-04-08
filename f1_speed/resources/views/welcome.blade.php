<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>F1 SPEED | Telemetry System</title>
        
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap');
            body { font-family: 'Inter', sans-serif; background-color: #050505; }
            .f1-italic { font-style: italic; }
        </style>
    </head>
    <body class="antialiased text-white overflow-x-hidden">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-red-600/10 blur-[150px] rounded-full"></div>
            <div class="absolute bottom-[10%] right-[-5%] w-[500px] h-[500px] bg-red-900/10 blur-[120px] rounded-full"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            
            <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto w-full">
                <div class="text-2xl font-black f1-italic tracking-tighter">
                    F1<span class="text-red-600">SPEED</span>
                </div>
                <div class="space-x-6 text-xs uppercase tracking-widest font-bold">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard-f1') }}" class="hover:text-red-500 transition">Panel de Control</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-red-500 transition">Entrar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-red-600 px-5 py-2 rounded-full hover:bg-red-700 transition shadow-[0_0_15px_rgba(220,38,38,0.3)]">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>

            <main class="flex-grow flex flex-col items-center justify-center text-center px-4">
                <div class="inline-block px-3 py-1 border border-red-600/30 rounded-full bg-red-600/5 text-red-500 text-[10px] uppercase tracking-[0.3em] font-bold mb-6 animate-pulse">
                    Data Stream Active v2.0
                </div>
                
                <h1 class="text-6xl md:text-8xl font-black f1-italic tracking-tighter uppercase leading-none">
                    Domina la <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-gray-500">Telemetría</span>
                </h1>
                
                <p class="mt-8 max-w-2xl text-gray-400 text-lg font-light leading-relaxed">
                    Analiza cada curva, cada frenada y cada milisegundo. Conecta tu simulador y visualiza el rendimiento de tu monoplaza en tiempo real con tecnología de vanguardia.
                </p>

                <div class="mt-12 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="bg-white text-black font-black uppercase italic tracking-widest px-10 py-4 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 transform hover:scale-105 shadow-xl">
                        Empezar Análisis
                    </a>
                    <div class="flex items-center px-6 text-gray-500 text-xs uppercase tracking-widest font-bold border border-white/10 rounded-xl backdrop-blur-sm">
                        Compatible con F1 24 / 23
                    </div>
                </div>
            </main>

            <footer class="py-10 text-center border-t border-white/5">
                <div class="flex justify-center space-x-8 mb-4 opacity-30 grayscale">
                    <span class="text-[10px] font-bold tracking-[0.4em]">PYHTON</span>
                    <span class="text-[10px] font-bold tracking-[0.4em]">LARAVEL</span>
                    <span class="text-[10px] font-bold tracking-[0.4em]">REACT</span>
                </div>
                <p class="text-[10px] text-gray-600 uppercase tracking-widest">
                    &copy; {{ date('Y') }} F1 SPEED Project — Ingeniería de Datos aplicada al SimRacing
                </p>
            </footer>
        </div>

    </body>
</html>