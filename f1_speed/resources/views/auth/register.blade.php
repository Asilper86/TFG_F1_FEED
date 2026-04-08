<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#1B1D21] relative overflow-hidden">
        
        <div class="w-full sm:max-w-md mt-6 px-10 py-10 bg-[#23262A] border border-[#2d3136] rounded-lg relative z-10">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold tracking-wide uppercase flex items-center justify-center gap-2 text-white">
                    <span class="text-[#E10600]">/</span> NUEVO PILOTO
                </h1>
                <p class="text-[10px] text-gray-500 tracking-[0.2em] uppercase mt-2 font-bold">Registro de Sesión</p>
            </div>

            <x-validation-errors class="mb-6 text-red-500 text-xs" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-1.5 block group-focus-within:text-[#E10600] transition-colors">Nombre del Piloto</label>
                    <x-input id="name" class="block w-full bg-[#1B1D21] border-[#2d3136] text-white focus:border-[#E10600] focus:ring-0 rounded py-2.5 px-4 transition-colors" 
                             type="text" name="name" :value="old('name')" required autofocus autocomplete="name"  />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-1.5 block group-focus-within:text-[#E10600] transition-colors">ID de Comunicación (Email)</label>
                    <x-input id="email" class="block w-full bg-[#1B1D21] border-[#2d3136] text-white focus:border-[#E10600] focus:ring-0 rounded py-2.5 px-4 transition-colors" 
                             type="email" name="email" :value="old('email')" required autocomplete="username"  />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-1.5 block group-focus-within:text-[#E10600] transition-colors">Clave de Acceso</label>
                    <x-input id="password" class="block w-full bg-[#1B1D21] border-[#2d3136] text-white focus:border-[#E10600] focus:ring-0 rounded py-2.5 px-4 transition-colors" 
                             type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="group">
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold ml-1 mb-1.5 block group-focus-within:text-[#E10600] transition-colors">Confirmar Clave</label>
                    <x-input id="password_confirmation" class="block w-full bg-[#1B1D21] border-[#2d3136] text-white focus:border-[#E10600] focus:ring-0 rounded py-2.5 px-4 transition-colors" 
                             type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <label for="terms" class="flex items-center cursor-pointer">
                            <x-checkbox name="terms" id="terms" required class="rounded border-[#2d3136] bg-[#1B1D21] text-[#E10600] focus:ring-offset-0 focus:ring-[#E10600]" />
                            <div class="ms-2 text-[11px] text-gray-500">
                                {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-gray-400 hover:text-white underline underline-offset-4">'.__('Términos de Servicio').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-gray-400 hover:text-white underline underline-offset-4">'.__('Política de Privacidad').'</a>',
                                ]) !!}
                            </div>
                        </label>
                    </div>
                @endif

                <div class="pt-6 space-y-4">
                    <button class="w-full inline-flex items-center justify-center px-8 py-3.5 font-bold text-white transition-colors duration-200 bg-[#E10600] hover:bg-red-700 rounded uppercase tracking-widest text-[11px]">
                        Confirmar Registro
                    </button>

                    <div class="text-center">
                        <a class="text-[10px] text-gray-500 hover:text-white uppercase tracking-widest font-bold transition-colors" href="{{ route('login') }}">
                            ¿Ya tienes una cuenta? <span class="text-gray-400">Iniciar Sesión</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="mt-8 z-10 flex space-x-6 opacity-60">
            <div class="text-[10px] text-gray-600 tracking-widest uppercase font-bold">
                ENROLLMENT_PROTOCOL_ACTIVE
            </div>
        </div>
    </div>
</x-guest-layout>   