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
                    <a href="{{ route('dashboard') }}" class="text-[10px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('social.feed') }}" class="text-[10px] font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('social.feed') ? 'text-white' : 'text-gray-500 hover:text-[#E10600]' }}">
                        AUTO FEED
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#1B1D21] border-t border-[#2d3136]">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white hover:bg-[#23262A]">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('social.feed') }}" :active="request()->routeIs('social.feed')" class="text-gray-300 hover:text-white hover:bg-[#23262A]">
                RED SOCIAL
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[#2d3136]">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-gray-300 hover:text-white hover:bg-[#23262A]">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();" class="text-[#E10600] hover:text-white hover:bg-[#23262A]">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
