<link rel="stylesheet" href="{{ asset('vendor_assets/css/bootstrap/'.(Session::get('layout')=='rtl' ? 'bootstrap-rtl.css' : 'bootstrap.css')) }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/footable.standalone.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/fullcalendar@5.2.0.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/jquery-jvectormap-2.0.5.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/jquery.mCustomScrollbar.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/line-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/MarkerCluster.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/MarkerCluster.Default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/slick.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/star-rating-svg.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/trumbowyg.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor_assets/css/wickedpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugin.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/'.(Session::get('layout')=='rtl' ? 'gallery-rtl.css' : 'gallery.css')) }}">
<link rel="stylesheet" href="{{ asset('css/'.(Session::get('layout')=='rtl' ? 'style-rtl.css' : 'style.css')) }}">

<style>
/* Enhanced Sidebar Styling */
.sidebar {
    background: #fff;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.sidebar__menu-group {
    padding: 1rem 0;
}

.sidebar_nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar_nav li {
    margin: 0;
    padding: 0;
}

.sidebar_nav .menu-title {
    padding: 0.75rem 1.5rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.sidebar_nav li a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.sidebar_nav li a:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
    border-left-color: #0d6efd;
}

.sidebar_nav li a.active {
    background-color: #f8f9fa;
    color: #0d6efd;
    border-left-color: #0d6efd;
    font-weight: 500;
}

.sidebar_nav .nav-icon {
    width: 18px;
    height: 18px;
    margin-right: 0.75rem;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
}

.sidebar_nav .menu-text {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Responsive Sidebar */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}

/* Active state for Cron Jobs */
.sidebar_nav li a[href*="cron-jobs"].active {
    background-color: #e7f3ff;
    color: #0d6efd;
    border-left-color: #0d6efd;
}

/* Clock icon styling */
.sidebar_nav li a .nav-icon[data-feather="clock"] {
    stroke: currentColor;
    stroke-width: 2;
}
</style>