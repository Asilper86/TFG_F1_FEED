<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#050505] relative overflow-hidden">
        
        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-red-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-red-900/10 blur-[100px] rounded-full"></div>

        <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-[#0f0f0f]/80 backdrop-blur-xl border border-white/5 shadow-[0_0_50px_rgba(0,0,0,0.5)] sm:rounded-2xl relative z-10">
            
            <div class="text-center mb-10">
                <h1 class="text-4xl font-black italic tracking-tighter text-white uppercase">
                    F1<span class="text-red-600 drop-shadow-[0_0_8px_rgba(220,38,38,0.5)]">SPEED</span>
                </h1>
                <p class="text-[10px] text-gray-500 tracking-[0.3em] uppercase mt-2 font-light">Telemetry & Analysis System</p>
            </div>

            <x-validation-errors class="mb-6 text-red-500 text-xs" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="relative group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-2 block group-focus-within:text-red-500 transition-colors">ID del Piloto</label>
                    <div class="relative">
                        <x-input id="email" 
                            class="block w-full bg-white/5 border-white/10 text-white placeholder-gray-600 focus:border-red-600 focus:ring-0 rounded-xl py-3 px-4 transition-all hover:bg-white/[0.08]" 
                            type="email" name="email" :value="old('email')" required autofocus placeholder="correo@ejemplo.com" />
                    </div>
                </div>

                <div class="relative group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-2 block group-focus-within:text-red-500 transition-colors">Código de Acceso</label>
                    <x-input id="password" 
                        class="block w-full bg-white/5 border-white/10 text-white focus:border-red-600 focus:ring-0 rounded-xl py-3 px-4 transition-all hover:bg-white/[0.08]" 
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <x-checkbox id="remember_me" name="remember" class="rounded border-white/10 bg-white/5 text-red-600 focus:ring-offset-0 focus:ring-red-600" />
                        <span class="ms-2 text-gray-400 group-hover:text-gray-300 transition-colors italic">Mantener sesión</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-gray-500 hover:text-red-500 transition-all font-medium italic underline decoration-red-600/30 underline-offset-4" href="{{ route('password.request') }}">
                            ¿Problemas de acceso?
                        </a>
                    @endif
                </div>

                <div class="pt-4">
                    <button class="group relative w-full inline-flex items-center justify-center px-8 py-3.5 font-bold text-white transition-all duration-200 bg-red-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 shadow-[0_0_20px_rgba(220,38,38,0.3)] hover:shadow-[0_0_30px_rgba(220,38,38,0.5)] hover:bg-red-500">
                        <span class="relative uppercase tracking-widest italic">Iniciar Sesión</span>
                        <svg class="w-5 h-5 ml-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('register') }}" class="text-[10px] text-gray-600 hover:text-gray-400 uppercase tracking-widest transition-colors font-medium">
                        ¿Nuevo piloto? Solicitar registro
                    </a>
                </div>
            </form>
        </div>
        
        <div class="mt-8 z-10 flex space-x-6">
            <div class="flex items-center text-[10px] text-gray-700 tracking-tighter">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span> SERVER ONLINE
            </div>
            <div class="text-[10px] text-gray-700 tracking-tighter uppercase font-mono">
                Encrypted Data Stream v2.6.4
            </div>
        </div>
    </div>
</x-guest-layout>