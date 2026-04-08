<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#050505] relative overflow-hidden">
        
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-red-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[400px] h-[400px] bg-red-900/10 blur-[100px] rounded-full"></div>

        <div class="w-full sm:max-w-md mt-6 px-10 py-10 bg-[#0f0f0f]/80 backdrop-blur-xl border border-white/5 shadow-[0_0_50px_rgba(0,0,0,0.5)] sm:rounded-2xl relative z-10">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black italic tracking-tighter text-white uppercase">
                    NUEVO<span class="text-red-600 drop-shadow-[0_0_8px_rgba(220,38,38,0.5)]">PILOTO</span>
                </h1>
                <p class="text-[10px] text-gray-500 tracking-[0.3em] uppercase mt-2 font-light">Registro en el Sistema Central</p>
            </div>

            <x-validation-errors class="mb-6 text-red-500 text-xs" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-1.5 block group-focus-within:text-red-500 transition-colors">Nombre del Piloto</label>
                    <x-input id="name" class="block w-full bg-white/5 border-white/10 text-white focus:border-red-600 focus:ring-0 rounded-xl py-2.5 px-4 transition-all" 
                             type="text" name="name" :value="old('name')" required autofocus autocomplete="name"  />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-1.5 block group-focus-within:text-red-500 transition-colors">ID de Comunicación (Email)</label>
                    <x-input id="email" class="block w-full bg-white/5 border-white/10 text-white focus:border-red-600 focus:ring-0 rounded-xl py-2.5 px-4 transition-all" 
                             type="email" name="email" :value="old('email')" required autocomplete="username"  />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-1.5 block group-focus-within:text-red-500 transition-colors">Clave de Acceso</label>
                    <x-input id="password" class="block w-full bg-white/5 border-white/10 text-white focus:border-red-600 focus:ring-0 rounded-xl py-2.5 px-4 transition-all" 
                             type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold ml-1 mb-1.5 block group-focus-within:text-red-500 transition-colors">Confirmar Clave</label>
                    <x-input id="password_confirmation" class="block w-full bg-white/5 border-white/10 text-white focus:border-red-600 focus:ring-0 rounded-xl py-2.5 px-4 transition-all" 
                             type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <label for="terms" class="flex items-center cursor-pointer">
                            <x-checkbox name="terms" id="terms" required class="rounded border-white/10 bg-white/5 text-red-600 focus:ring-offset-0 focus:ring-red-600" />
                            <div class="ms-2 text-[11px] text-gray-500 italic">
                                {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-gray-300 hover:text-red-500 underline underline-offset-4">'.__('Términos de Servicio').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-gray-300 hover:text-red-500 underline underline-offset-4">'.__('Política de Privacidad').'</a>',
                                ]) !!}
                            </div>
                        </label>
                    </div>
                @endif

                <div class="pt-6 space-y-4">
                    <button class="group relative w-full inline-flex items-center justify-center px-8 py-3.5 font-bold text-white transition-all duration-200 bg-red-600 font-pj rounded-xl shadow-[0_0_20px_rgba(220,38,38,0.3)] hover:shadow-[0_0_30px_rgba(220,38,38,0.5)] hover:bg-red-500">
                        <span class="relative uppercase tracking-widest italic">Confirmar Registro</span>
                        <svg class="w-5 h-5 ml-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    <div class="text-center">
                        <a class="text-[10px] text-gray-500 hover:text-red-500 transition-all uppercase tracking-widest font-medium" href="{{ route('login') }}">
                            ¿Ya tienes una cuenta? <span class="italic text-gray-300">Iniciar Sesión</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="mt-8 z-10 flex space-x-6 opacity-40">
            <div class="text-[9px] text-gray-500 tracking-tighter uppercase font-mono">
                SECURE_ENROLLMENT_PROTOCOL_ACTIVE
            </div>
            <div class="text-[9px] text-gray-500 tracking-tighter uppercase font-mono">
                NODE_LOCATION: {{ request()->ip() }}
            </div>
        </div>
    </div>
</x-guest-layout>   