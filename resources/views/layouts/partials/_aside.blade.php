<aside class="sidebar">
   <div class="sidebar__menu-group">
      <ul class="sidebar_nav">
         <li class="menu-title">
            <span>Main menu</span>
         </li>
         <li>
            <a href="{{ route('products.list') }}" class="{{ request()->is('products/list') ? 'active' : ''}}">
                <span data-feather="package" class="nav-icon"></span>
                <span class="menu-text">Products</span>
            </a>
         </li>
         <li>
            <a href="{{ route('products.import-status') }}" class="{{ request()->is('products/import-status') ? 'active' : ''}}">
                <span data-feather="list" class="nav-icon"></span>
                <span class="menu-text">Import Status</span>
            </a>
         </li>
         <li>
            <a href="{{ route('competitor.list') }}" class="{{ Route::is('competitor.*') ? 'active': '' }}">
                <span data-feather="users" class="nav-icon"></span>
                <span class="menu-text">Competitors</span>
            </a>
         </li>
         <li>
            <a href="{{ route('price_history.list') }}" class="{{ request()->is('price-history*') ? 'active' : ''}}">
                <span data-feather="activity" class="nav-icon"></span>
                <span class="menu-text">Price History</span>
            </a>
         </li>
         @auth
            @if(
                auth()->user()->isAdmin() ||
                auth()->user()->hasPermission('user.view') ||
                auth()->user()->hasPermission('role.view') ||
                auth()->user()->hasPermission('permission.view') ||
                auth()->user()->hasPermission('category.view') ||
                auth()->user()->hasPermission('cron.view')
            )
                <li class="menu-title mt-3">
                    <span>Management</span>
                </li>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('category.view'))
                <li>
                    <a href="{{ route('categories.index') }}" class="{{ request()->is('categories*') ? 'active' : '' }}">
                        <span data-feather="tag" class="nav-icon"></span>
                        <span class="menu-text">Categories</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('user.view'))
                <li>
                    <a href="{{ route('user-management.index') }}" class="{{ request()->is('user-management*') ? 'active' : '' }}">
                        <span data-feather="user" class="nav-icon"></span>
                        <span class="menu-text">User Management</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('role.view'))
                <li>
                    <a href="{{ route('roles.index') }}" class="{{ request()->is('roles*') ? 'active' : '' }}">
                        <span data-feather="shield" class="nav-icon"></span>
                        <span class="menu-text">Role Management</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('permission.view'))
                <li>
                    <a href="{{ route('permissions.index') }}" class="{{ request()->is('permissions*') ? 'active' : '' }}">
                        <span data-feather="key" class="nav-icon"></span>
                        <span class="menu-text">Permission Management</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('cron.view'))
                <li>
                    <a href="{{ route('cron-jobs.index') }}" class="{{ request()->is('cron-jobs*') ? 'active' : '' }}">
                        <span data-feather="clock" class="nav-icon"></span>
                        <span class="menu-text">Cron Jobs</span>
                    </a>
                </li>
            @endif
        @endauth
      </ul>
   </div>
</aside>