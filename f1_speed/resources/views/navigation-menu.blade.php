@php
    $unread = Auth::check() ? Auth::user()->f1Notifications()->whereNull('read_at')->count() : 0;
    $notifications = Auth::check() ? Auth::user()->f1Notifications()->with('actor')->latest()->limit(15)->get() : collect();
@endphp

<nav x-data="{ open: false }" class="bg-[#1B1D21] border-b border-[#2d3136] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wide uppercase flex items-center gap-2 text-white">
                    <span class="text-[#E10600]">/</span> F1 SPEED
                </a>
                <div class="hidden lg:flex lg:items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'text-white border-b-2 border-[#E10600] pb-1' : 'text-gray-400 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-gauge"></i>Dashboard
                    </a>
                    <a href="{{ route('social.feed') }}" class="text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 {{ request()->routeIs('social.feed') ? 'text-white border-b-2 border-[#E10600] pb-1' : 'text-gray-400 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-rss"></i>AUTO FEED
                    </a>
                    <a href="{{ route('social.profile') }}" class="text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 {{ request()->routeIs('social.profile') ? 'text-white border-b-2 border-[#E10600] pb-1' : 'text-gray-400 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-user"></i>MI PERFIL
                    </a>
                    <a href="{{ route('social.search') }}" class="text-[13px] font-bold uppercase tracking-widest transition-colors flex items-center gap-2 {{ request()->routeIs('social.search') ? 'text-white border-b-2 border-[#E10600] pb-1' : 'text-gray-400 hover:text-[#E10600]' }}">
                        <i class="fa-solid fa-magnifying-glass"></i>BUSCADOR
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center gap-4">
                <div class="flex items-center gap-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-8 h-8 rounded-full object-cover border border-[#E10600]" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="w-8 h-8 rounded-full bg-[#E10600] flex items-center justify-center text-white font-bold text-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">{{ Auth::user()->name }}</span>
                </div>

                <div class="relative" x-data="{ notifOpen: false }">
                    <button @click.stop="notifOpen = !notifOpen" class="relative text-gray-400 hover:text-white transition-colors p-1">
                        <i class="fa-solid fa-bell text-lg"></i>
                        @if ($unread > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#E10600] rounded-full text-[9px] font-black flex items-center justify-center text-white">{{ $unread }}</span>
                        @endif
                    </button>

                    <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition class="absolute right-0 mt-3 w-80 bg-[#1B1D21] border border-[#2d3136] rounded-xl shadow-2xl z-50 overflow-hidden" x-cloak>
                        <div class="px-4 py-3 border-b border-[#2d3136] flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-white">Notificaciones</span>
                            <a href="{{ route('notifications.readAll') }}" class="text-[9px] text-gray-500 hover:text-white uppercase tracking-widest">Marcar leído</a>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-[#2d3136]">
                            @forelse ($notifications as $n)
                                <a href="{{ route('social.profile', $n->actor?->id) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-[#23262A] transition-colors {{ !$n->read_at ? 'border-l-2 border-l-[#E10600]' : '' }}">
                                    @if ($n->actor?->profile_photo_url)
                                        <img src="{{ $n->actor->profile_photo_url }}" class="w-9 h-9 rounded-full object-cover border border-[#2d3136] shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-[#E10600] flex items-center justify-center text-white font-black text-sm shrink-0">
                                            {{ substr($n->actor?->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-300 leading-snug">
                                            <span class="font-bold text-white">{{ $n->actor?->name }}</span>
                                            @if ($n->type === 'like') le dio like a tu post @endif
                                            @if ($n->type === 'follow') empezó a seguirte @endif
                                            @if ($n->type === 'repost') reposteó tu publicación @endif
                                            @if ($n->type === 'comment') comentó en tu post @endif
                                        </p>
                                        <p class="text-[9px] text-gray-600 mt-0.5 uppercase tracking-widest">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if ($n->type === 'like')
                                        <i class="fa-solid fa-heart text-[#E10600] text-xs shrink-0"></i>
                                    @elseif ($n->type === 'follow')
                                        <i class="fa-solid fa-user-plus text-[#3FA9F5] text-xs shrink-0"></i>
                                    @elseif ($n->type === 'repost')
                                        <i class="fa-solid fa-retweet text-[#00D100] text-xs shrink-0"></i>
                                    @elseif ($n->type === 'comment')
                                        <i class="fa-solid fa-comment text-[#eab308] text-xs shrink-0"></i>
                                    @endif
                                </a>
                            @empty
                                <p class="text-center text-gray-600 text-xs py-8 uppercase tracking-widest">Sin notificaciones</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button @click.prevent="$root.submit();" class="text-[10px] uppercase tracking-widest text-[#E10600] hover:text-[#ff0700] font-bold transition-all px-3 py-1.5 bg-[#121418] rounded border border-[#2d3136]">
                        Cerrar Box
                    </button>
                </form>
            </div>

            <div class="flex items-center lg:hidden">
                <button @click="open = !open" class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#23262A] transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden fixed inset-0 z-50 bg-[#1B1D21]">
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
                <a href="{{ route('notifications.readAll') }}" class="flex items-center gap-4 text-lg font-bold uppercase tracking-[0.1em] text-gray-400">
                    <i class="fa-solid fa-bell text-[#E10600] w-6 text-center"></i> NOTIFICACIONES
                    @if ($unread > 0)
                        <span class="px-2 py-0.5 bg-[#E10600] text-white text-[9px] rounded-full font-black">{{ $unread }}</span>
                    @endif
                </a>
            </div>
            <div class="mt-12 pt-8 border-t border-[#2d3136]">
                <div class="flex items-center gap-4 mb-10">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-12 h-12 rounded-full object-cover border-2 border-[#E10600]" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="w-12 h-12 rounded-full bg-[#E10600] flex items-center justify-center text-white font-black text-lg italic">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-lg font-black uppercase tracking-widest text-white italic">{{ Auth::user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button @click.prevent="$root.submit();" class="text-[#E10600] text-sm font-black uppercase tracking-[0.2em] hover:text-[#ff0700] transition-colors">
                        CERRAR BOX
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
