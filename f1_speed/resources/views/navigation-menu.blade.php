<nav x-data="{ open: false }" class="bg-[#1B1D21] border-b border-[#2d3136]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                        <span class="text-[#E10600]">/</span> F1 SPEED
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('dashboard') }}" class="text-[15px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-gauge text-[#E10600] mr-1"></i>Dashboard
                    </a>
                    <a href="{{ route('social.feed') }}" class="text-[15px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('social.feed') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-rss text-[#E10600] mr-1"></i> AUTO FEED
                    </a>
                    <a href="{{ route('social.profile') }}" class="text-[15px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('social.profile') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-user text-[#E10600] mr-1"></i>MI PERFIL
                    </a>
                    <a href="{{ route('social.search') }}" class="text-[15px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('social.search') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-magnifying-glass text-[#E10600] mr-1"></i>BUSCADOR
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-[#E10600] transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-[#2d3136] text-sm leading-4 font-bold tracking-widest uppercase rounded-md text-gray-300 bg-[#23262A] hover:text-white hover:bg-[#2A2E33] focus:outline-none transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="bg-[#23262A] border border-[#2d3136] rounded-md shadow-lg py-1">
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}" class="text-gray-300 hover:text-white hover:bg-[#2A2E33] text-[11px] uppercase tracking-widest font-bold">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <div class="border-t border-[#2d3136] my-1"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}"
                                             @click.prevent="$root.submit();" class="text-[#E10600] hover:text-[#ff0700] hover:bg-[#2A2E33] text-[11px] uppercase tracking-widest font-bold">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#23262A] focus:outline-none focus:bg-[#23262A] transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden fixed inset-0 z-50 bg-[#1B1D21]">
        <div class="p-6">
            <div class="flex justify-between items-center mb-10">
                <div class="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                    <span class="text-[#E10600]">/</span> F1 SPEED
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-white">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}">
                    <i class="fa-solid fa-gauge text-[#E10600] w-6 text-center"></i> DASHBOARD
                </a>
                <a href="{{ route('social.feed') }}" class="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] {{ request()->routeIs('social.feed') ? 'text-white' : 'text-gray-400' }}">
                    <i class="fa-solid fa-rss text-[#E10600] w-6 text-center"></i> AUTO FEED
                </a>
                <a href="{{ route('social.profile') }}" class="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] {{ request()->routeIs('social.profile') ? 'text-white' : 'text-gray-400' }}">
                    <i class="fa-solid fa-user text-[#E10600] w-6 text-center"></i> MI PERFIL
                </a>
                <a href="{{ route('social.search') }}" class="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] {{ request()->routeIs('social.search') ? 'text-white' : 'text-gray-400' }}">
                    <i class="fa-solid fa-magnifying-glass text-[#E10600] w-6 text-center"></i> BUSCADOR
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-[#2d3136]">
                <div class="flex items-center gap-4 mb-10">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="size-12 rounded-full object-cover border-2 border-[#E10600]" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="w-12 h-12 rounded-full bg-[#E10600] flex items-center justify-center text-white font-black text-lg italic">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif

                    <div>
                        <div class="text-lg font-black uppercase tracking-widest text-white italic">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">PILOTO ACTIVO</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button 
                        @click.prevent="$root.submit();" 
                        class="text-[#E10600] text-sm font-black uppercase tracking-[0.2em] hover:text-[#ff0700] transition-colors"
                    >
                        CERRAR BOX
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
