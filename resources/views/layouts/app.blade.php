<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ Session::get('layout') == 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Title Section --}}
    <title> PriceCompetitor | @yield(
        'title',
        $pageTitle ??
            'PriceCompetitor Laravel Web
             PriceCompetitor Application'
    )
    </title>

    <meta name="description" content="@yield('page_description', $pageDescription ?? 'PriceCompetitor Laravel Web Application')" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="{{ asset('vendor_assets/css/sweetalert2/sweetalert2.min.css') }}">
    {{-- Inject:css, Global Theme Styles (used by all pages) --}}

    @include('layouts.partials._styles')
    {{-- Includable CSS --}}
    @yield('styles')
    @yield('additional_styles')
    {{-- Endinject --}}
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Mobilenzo_Logo_V3_1_.png') }}">
</head>
<body class="layout-light side-menu @auth() overlayScroll @endauth">
    @auth()
        <div class="mobile-search"></div>
        <div class="mobile-author-actions"></div>
        @include('layouts.partials._header')
    @endauth
    <main class="main-content">
        @auth()
            @include('layouts.partials._aside')
        @endauth
        @section('content')
        @show
        @auth()
            @include('layouts.partials._footer')
        @endauth
    </main>
    @auth()
        <div id="overlayer">
            <span class="loader-overlay">
                <div class="atbd-spin-dots spin-lg">
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                </div>
            </span>
        </div>
        {{-- @include('layouts.partials._customizer') --}}
    @endauth
    <div class="overlay-dark-sidebar"></div>
    <div class="customizer-overlay"></div>
    {{-- Inject:js, Global Theme JS Bundle (used by all pages) --}}
    @yield('mapScript')
    @include('layouts.partials._scripts')
    {{-- SweetAlert2 JS --}}
    <script src="{{ asset('vendor_assets/js/sweetalert2/sweetalert2.all.min.js') }}"></script>
    {{-- Includable JS --}}
    <script src="{{ mix('js/alpinejs.cdn.min.js') }}"></script>
    
    @yield('scripts')
    {{-- Endinject --}}
</body>

</html>
