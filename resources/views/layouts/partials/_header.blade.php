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
                @php
                    $user = Auth::user();
                    $userName = $user->name ?? 'User';
                    $userEmail = $user->email ?? '';
                    $userInitials = collect(explode(' ', trim($userName)))
                        ->filter()
                        ->take(2)
                        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                        ->implode('');
                    $defaultAvatar = asset('img/author/profile.png');
                    $hasProfileImage = !empty($user?->profile_image);
                    $profileImage = route('profile.image.show', ['v' => optional($user?->updated_at)->timestamp ?? now()->timestamp]);
                @endphp
                <li class="nav-author">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle circle profile-avatar-trigger">
                            <img class="rounded-circle profile-avatar-image {{ $hasProfileImage ? '' : 'is-default-logo' }}"
                                src="{{ $profileImage }}"
                                onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';"
                                alt="{{ $userName }}">
                            <span class="profile-avatar-fallback">{{ $userInitials ?: 'U' }}</span>
                        </a>
                        <div class="dropdown-wrapper">
                            <div class="nav-author__info">
                                <div class="author-img">
                                    <img src="{{ $profileImage }}" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';" alt="{{ $userName }}" class="rounded-circle profile-dropdown-image {{ $hasProfileImage ? '' : 'is-default-logo' }}">
                                </div>
                                <div>
                                    <h6>{{ $userName }}</h6>
                                    <span>{{ $userEmail }}</span>
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
<style>
    .profile-avatar-trigger {
        position: relative;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        background: #0d6efd;
    }

    .profile-avatar-image {
        width: 34px;
        height: 34px;
        object-fit: cover;
        display: block;
        position: relative;
        z-index: 2;
    }

    .profile-avatar-image.is-default-logo { object-fit: cover; background: transparent; padding: 0; }

    .profile-dropdown-image {
        width: 44px !important;
        height: 44px !important;
        min-width: 44px;
        min-height: 44px;
        object-fit: cover;
        display: block;
    }

    .profile-dropdown-image.is-default-logo {
        object-fit: cover;
        background: transparent;
        padding: 0;
    }

    .profile-avatar-fallback {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        z-index: 1;
        pointer-events: none;
    }

    /* Keep dropdown user info clean and aligned */
    .dropdown-wrapper .nav-author__info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dropdown-wrapper .nav-author__info .author-img {
        margin-right: 0;
        flex-shrink: 0;
    }
</style>