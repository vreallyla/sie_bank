<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">


    @livewireStyles
    @stack('pack')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <style>



    </style>
</head>

<body>
    <div x-data="{ show: false, showSub: null, pickMenu: null }" x-ref="menu"
        x-init="showSub=window.location.pathname.split('/')[1];" class="divide-y divide-gray-800">

        <nav class="flex items-center bg-gray-900 px-3 py-2 shadow-lg">
            <div>
                <button @click="show =! show"
                    class="block h-8 mr-3 text-gray-400 items-center hover:text-gray-200 focus:text-gray-200 focus:outline-none sm:hidden">
                    <svg class="w-8 fill-current" viewBox="0 0 24 24">
                        <path x-show="!show" fill-rule="evenodd"
                            d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z" />
                        <path x-show="show" fill-rule="evenodd"
                            d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z" />
                    </svg>
                </button>
            </div>
            <div class="h-12 w-full flex items-center">
                <a href="{{ url('/') }}" class="w-full relative">
                    <img class="h-12 object-cover" src="{{ url('/images/logo.png') }}" />
                </a>
            </div>
            <div class="flex justify-end sm:w-8/12">
                {{-- Top Navigation --}}
                <ul class="hidden sm:flex sm:text-left text-gray-200 text-sm">

                    <a href="{{ url('/') }}">
                        <li class="cursor-pointer px-4 py-2 hover:bg-gray-800">
                            <x-jet-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    {{ Auth::user()->name }}
                                </x-slot>
                                <x-slot name="content">
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Account') }}
                                    </div>

                                    <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </x-jet-dropdown-link>
                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-jet-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-jet-dropdown-link>
                                    </form>

                                </x-slot>
                            </x-jet-dropdown>
                        </li>
                    </a>

                </ul>
            </div>
        </nav>
        <div class="sm:flex sm:min-h-screen">
            <aside
                class="bg-gray-900 text-gray-700 divide-y divide-gray-700 divide-dashed sm:w-4/12 md:w-3/12 lg:w-2/12">
                {{-- Desktop Web View --}}
                <ul class="hidden text-gray-200 text-sm sm:block sm:text-left pt-12">

                    <a href="{{ url('dashboard') }}">
                        <li class="menu">
                            <i class="material-icons flex items-center">widgets</i>
                            <span class="flex items-center">Dasboard</span>
                        </li>
                    </a>
                    @if (auth()->user()->level == 'admin')
                        <li class="flex-col">
                            <div @click="showSub = showSub=='master'?'':'master'"
                                :class="showSub === 'master' ? 'active' : ''" class="menu with-sub flex gap-2">
                                <i class="material-icons flex items-center">layers</i>
                                <span class="flex items-center">Data Master</span>
                            </div>
                            <ul class="sub-menu">
                                <a href="{{ route('master.kolektibilitas') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Kolektibilitas</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Nasabah</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.pendidikan') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Pendidikan</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.pegawai') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Pegawai</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.penghasilan') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Penghasilan</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.profesi') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Profesi</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.produk') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Produk</span>
                                    </li>
                                </a>

                                <a href="{{ route('master.tim_pemasaran') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Tim Pemasaran</span>
                                    </li>
                                </a>
                                <a href="{{ route('master.wilayah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Daftar Wilayah</span>
                                    </li>
                                </a>
                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->level == 'eksekutif')
                        <li class="flex-col">
                            <div @click="showSub = showSub=='laporan' ?'':'laporan'"
                                :class="showSub === 'laporan' ? 'active' : ''" class="menu with-sub flex gap-2">
                                <i class="material-icons flex items-center">receipt</i>
                                <span class="flex items-center">Laporan</span>
                            </div>
                            <ul class="sub-menu">
                                <a href="{{ route('laporan.nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Wilayah Nasabah</span>
                                    </li>
                                </a>
                                <a href="{{ route('laporan.kolektabilitas_nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Kolektibilitas Nasabah</span>
                                    </li>
                                </a>

                                <a href="{{ route('laporan.pendidikan_nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Pendidikan Nasabah</span>
                                    </li>
                                </a>

                                <a href="{{ route('laporan.penghasilan_nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Penghasilan Nasabah</span>
                                    </li>
                                </a>

                                <a href="{{ route('laporan.profesi_nasabah') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Profesi Nasabah</span>
                                    </li>
                                </a>

                                <a href="{{ route('laporan.pemakaian_produk') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Pemakaian Produk</span>
                                    </li>
                                </a>

                                <a href="{{ route('laporan.kinerja_pemasaran') }}">
                                    <li class="menu">
                                        <i class="material-icons flex items-center">donut_large</i>
                                        <span class="flex items-center">Kinerja Pemasaran</span>
                                    </li>
                                </a>

                            </ul>
                        </li>
                    @endif


                </ul>

                {{-- Mobile Web View --}}
                <div :class="show ? 'block' : 'hidden'" class="pb-3 divide-y divide-gray-800 block sm:hidden">
                    <ul class="text-gray-200 text-xs">

                        <a href="{{ url('/') }}">
                            <li class="cursor-pointer px-4 py-2 hover:bg-gray-800">home</li>
                        </a>

                    </ul>

                    {{-- Top Navigation Mobile Web View --}}
                    <ul class="text-gray-200 text-xs">
                        <a href="{{ url('/') }}">
                            <li class="cursor-pointer px-4 py-2 hover:bg-gray-800">home</li>
                        </a>
                    </ul>
                </div>
            </aside>
            <main class="bg-gray-100 px-12 py-6 min-h-screen sm:w-8/12 md:w-9/12 lg:w-10/12">
                {{ $slot }}

            </main>
        </div>
    </div>
    @include('addons.custom-chart')

    @livewireScripts
    @stack('scripts')

    @stack('modals')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log(window.location.pathname);
            document.querySelectorAll('[href="' + '{!! url('/') !!}' + window.location.pathname + '"]')
                .forEach(el => {
                    let activeMenu = el.querySelector('.menu');

                    if (activeMenu) {
                        activeMenu.classList.add('active');
                    }

                });

        });

    </script>

</body>

</html>
