<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#1B1D21] relative overflow-hidden">
        
        <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-[#23262A] border border-[#2d3136] rounded-lg relative z-10">
            
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold tracking-wide uppercase flex items-center justify-center gap-2 text-white">
                    <span class="text-[#E10600]">/</span> F1 SPEED
                </h1>
                <p class="text-[10px] text-gray-500 tracking-[0.2em] uppercase mt-2 font-bold">Telemetry System</p>
            </div>

            <x-validation-errors class="mb-6 text-red-500 text-xs" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="relative group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-2 block group-focus-within:text-[#E10600] transition-colors">ID del Piloto</label>
                    <div class="relative">
                        <x-input id="email" 
                            class="block w-full bg-[#1B1D21] border-[#2d3136] text-white placeholder-gray-600 focus:border-[#E10600] focus:ring-0 rounded py-3 px-4 transition-colors" 
                            type="email" name="email" :value="old('email')" required autofocus placeholder="correo@ejemplo.com" />
                    </div>
                </div>

                <div class="relative group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-2 block group-focus-within:text-[#E10600] transition-colors">Código de Acceso</label>
                    <x-input id="password" 
                        class="block w-full bg-[#1B1D21] border-[#2d3136] text-white focus:border-[#E10600] focus:ring-0 rounded py-3 px-4 transition-colors" 
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <x-checkbox id="remember_me" name="remember" class="rounded border-[#2d3136] bg-[#1B1D21] text-[#E10600] focus:ring-offset-0 focus:ring-[#E10600]" />
                        <span class="ms-2 text-gray-400 group-hover:text-white transition-colors">Mantener sesión</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-gray-500 hover:text-[#E10600] transition-colors font-medium underline decoration-[#2d3136] underline-offset-4" href="{{ route('password.request') }}">
                            ¿Problemas de acceso?
                        </a>
                    @endif
                </div>

                <div class="pt-4">
                    <button class="w-full inline-flex items-center justify-center px-8 py-3.5 font-bold text-white transition-colors duration-200 bg-[#E10600] hover:bg-red-700 rounded uppercase tracking-widest text-[11px]">
                        Iniciar Sesión
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('register') }}" class="text-[10px] text-gray-500 hover:text-white uppercase tracking-widest transition-colors font-medium">
                        ¿Nuevo piloto? Solicitar registro
                    </a>
                </div>
            </form>
        </div>
        
        <div class="mt-8 z-10 flex space-x-6">
            <div class="flex items-center text-[10px] text-gray-600 tracking-widest font-bold">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span> SERVER ONLINE
            </div>
            <div class="text-[10px] text-gray-600 tracking-widest uppercase font-bold">
                Data Stream v2.0
            </div>
        </div>
    </div>
</x-guest-layout>