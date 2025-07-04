<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <style>
        @media (min-width: 640px) {
    .sm\:ml-6 {
        margin-left: 1rem !important;
            }
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route( 'admin' === Auth::getDefaultDriver() ? 'admin.dashboard' : 'dashboard' ) }}">
                        <img height="80px" width="80px" src="{{ url('/storage/logo.png') }}" alt="">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden text-lg space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admin.dashboard' : 'dashboard' )" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                
                @if (Auth::user()->type == 'superadmin')
               
               
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'testimonial.index' : 'dashboard' )" :active="request()->routeIs('testimonial.index')">
                        {{ __('Testimonials') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allorders' : 'dashboard' )" :active="request()->routeIs('allorders')">
                        {{ __('Memberships') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allpayemnts' : 'dashboard' )" :active="request()->routeIs('allpayemnts')">
                        {{ __('Payments') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allorderspending' : 'dashboard' )" :active="request()->routeIs('allorderspending')">
                        {{ __('Orders') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'manualpayments.get' : 'dashboard' )" :active="request()->routeIs('manualpayments.get')">
                        {{ __('Manual payments') }}
                    </x-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'packages.index' : 'dashboard' )" :active="request()->routeIs('packages.index')">
                        {{ __('Packages') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allstories' : 'dashboard' )" :active="request()->routeIs('allstories')">
                        {{ __('Stories') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'youtubevideo.index' : 'dashboard' )" :active="request()->routeIs('youtubevideo.index')">
                        {{ __('youtube videos') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'partner.index' : 'dashboard' )" :active="request()->routeIs('partner.index')">
                        {{ __('Partner') }}
                    </x-nav-link>
                </div>
                @endif
            
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'coupons.get' : 'dashboard' )" :active="request()->routeIs('coupons.get')">
                        {{ __('Coupons') }}
                    </x-nav-link>
                </div>
                @if (Auth::user()->type != 'superadmin')
                <div class="hidden space-x-8 sm:-my-px sm:ml-6 sm:flex">
                    <x-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allorgpayemnts' : 'dashboard' )" :active="request()->routeIs('allorgpayemnts')">
                        {{ __('Organization stake') }}
                    </x-nav-link>
                </div>
                @endif
               
               
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admin.profile.edit' : 'profile.edit' )">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if (Auth::user()->type == 'superadmin')
                        <x-dropdown-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admins.manage' : '/' )">
                            {{ __('Sub Admins') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'allusers' : '/' )">
                            {{ __('Users') }}
                        </x-dropdown-link>
                        @else
                            
                        @endif

                      
                       
                        <!-- Authentication -->
                        <form method="POST" action="{{ route( 'admin' === Auth::getDefaultDriver() ? 'admin.logout' : 'logout' ) }}">
                            @csrf

                            <x-dropdown-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admin.logout' : 'logout' )"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admin.dashboard' : 'dashboard' )" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route( 'admin' === Auth::getDefaultDriver() ? 'admin.profile.edit' : 'profile.edit' )">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route( 'admin' === Auth::getDefaultDriver() ? 'admin.logout' : 'logout' ) }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
