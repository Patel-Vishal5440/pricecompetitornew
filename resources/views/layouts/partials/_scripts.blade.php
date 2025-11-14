<script src="{{ asset('vendor_assets/js/jquery/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery/jquery-ui.js') }}"></script>
<script src="{{ asset('vendor_assets/js/bootstrap/popper.js') }}"></script>
<script src="{{ asset('vendor_assets/js/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/accordion.js') }}"></script>
<script src="{{ asset('vendor_assets/js/autoComplete.js') }}"></script>
<script src="{{ asset('vendor_assets/js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/charts.js') }}"></script>
<script src="{{ asset('vendor_assets/js/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/daterangepicker.js') }}"></script>
<script src="{{ asset('vendor_assets/js/drawer.js') }}"></script>
<script src="{{ asset('vendor_assets/js/dynamicBadge.js') }}"></script>
<script src="{{ asset('vendor_assets/js/dynamicCheckbox.js') }}"></script>
<script src="{{ asset('vendor_assets/js/feather.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/footable.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/fullcalendar@5.2.0.js') }}"></script>
<script src="{{ asset('vendor_assets/js/google-chart.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.filterizr.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.mCustomScrollbar.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.peity.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jquery.star-rating-svg.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/leaflet.js') }}"></script>
<script src="{{ asset('vendor_assets/js/leaflet.markercluster.js') }}"></script>
<script src="{{ asset('vendor_assets/js/loader.js') }}"></script>
<script src="{{ asset('vendor_assets/js/message.js') }}"></script>
<script src="{{ asset('vendor_assets/js/moment.js') }}"></script>
<script src="{{ asset('vendor_assets/js/muuri.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/notification.js') }}"></script>
<script src="{{ asset('vendor_assets/js/popover.js') }}"></script>
<script src="{{ asset('vendor_assets/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/slick.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/trumbowyg.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/trumbowyg.upload64.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/wickedpicker.min.js') }}"></script>
<script src="{{ asset('js/drag-drop.js') }}"></script>
<script src="{{ asset('js/full-calendar.js') }}"></script>
<script src="{{ asset('js/googlemap-init.js') }}"></script>
<script src="{{ asset('js/icon-loader.js') }}"></script>
<script src="{{ asset('js/jvectormap-init.js') }}"></script>
<script src="{{ asset('js/footable.js') }}"></script>
<script src="{{ asset('js/leaflet-init.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

<script>
// Enhanced Sidebar Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Mobile sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay-dark-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
            if (overlay) {
                overlay.classList.toggle('show');
            }
        });
    }
    
    // Close sidebar when clicking overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
    
    // Close sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            if (overlay) {
                overlay.classList.remove('show');
            }
        }
    });
    
    // Active menu highlighting
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar_nav li a');
    
    menuItems.forEach(function(item) {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
            item.classList.add('active');
        }
    });
    
    // Smooth scrolling for sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar_nav li a[href^="#"]');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Ensure icons are loaded after dynamic content
function reloadFeatherIcons() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Call this function after any dynamic content is loaded
window.reloadFeatherIcons = reloadFeatherIcons;
</script>
