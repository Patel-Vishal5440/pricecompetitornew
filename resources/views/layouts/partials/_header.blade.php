<header class="header-top">
    <nav class="navbar navbar-light">
        <div class="navbar-left">
            <a href="" class="sidebar-toggle">
                <img class="svg" src="{{ asset('img/svg/bars.svg') }}" alt="img">
            </a>
            <a class="navbar-brand" href="#"><img class="svg dark" src="{{ asset('images/Mobilenzo_Logo_V3_1_.png') }}" alt="">
                <img class="light" src="{{ asset('img/Logo_white.png') }} " alt="">
            </a>
        <div class="px-25 py-20 d-flex align-items-center justify-content-between">
            <h4 class="page-title m-0 d-flex align-items-center">
                {{ $pageTitle }}
            </h4>
        </div>
        </div>
        <!-- ends: navbar-left -->
        <div class="navbar-right">
            <ul class="navbar-right__menu">
                <li class="nav-search d-none">
                    <a href="#" class="search-toggle">
                        <i class="la la-search"></i>
                        <i class="la la-times"></i>
                    </a>
                </li>
                <!-- ends: .nav-flag-select -->
                <li class="nav-author">
                    <div class="dropdown-custom">
                        {{-- @dd(Auth::user()->profile_image) --}}
                        <a href="javascript:;" class="nav-item-toggle circle"><img class="rounded-circle" src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('img/author/profile.png') }}"
                                                                            alt="" class="rounded-circle"></a>
                        <div class="dropdown-wrapper">
                            <div class="nav-author__info">
                                <div class="author-img">
                                    <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('img/author/profile.png') }}" alt="" class="rounded-circle">
                                </div>
                                <div>
                                    <h6>{{ Auth::user()->name }}</h6>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                            <div class="nav-author__options">
                                <ul>
                                    <li>
                                        <a href="{{ route('pages.profileSetting') }}">
                                            <span data-feather="user"></span> Profile</a>
                                    </li>   
                                </ul>
                                <a href="{{ route('logout') }}" class="nav-author__signout" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <span data-feather="log-out"></span> Sign Out</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        <!-- ends: .dropdown-wrapper -->
                    </div>
                </li>
                <!-- ends: .nav-author -->
            </ul>
            <!-- ends: .navbar-right__menu -->
            <div class="navbar-right__mobileAction d-md-none">
                <a href="#" class="btn-search">
                    <span data-feather="search"></span>
                    <span data-feather="x"></span></a>
                <a href="#" class="btn-author-action">
                    <span data-feather="more-vertical"></span></a>
            </div>
        </div>
        <!-- ends: .navbar-right -->
    </nav>
</header>
