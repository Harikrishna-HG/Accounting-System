<button class="mobile-menu-btn" id="mobileMenuBtn">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <button class="toggle-btn" id="toggleBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <span class="logo-text">Accounting System</span>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar" style="overflow:hidden;">
            <img src="{{ asset('pngtree-vector-users-icon-png-image_4144740.jpg') }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
        </div>
        <div class="user-info">
            <span class="user-name">{{ auth()->user()->name }}</span>
            <span class="user-role">{{ auth()->user()->role?->name ?? 'User' }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            @if(auth()->user()->hasPermission('dashboard.view'))
            <li class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('dashboard.view'))
            <li class="menu-item" style="border-top:1px solid rgba(255,255,255,0.15);padding-top:8px;margin-top:8px;">
                <a href="{{ route('accounting.dashboard') }}" class="menu-link {{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-calculator"></i></span>
                    <span class="menu-text" style="font-weight:700;">Accounting</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('products.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.products.index') }}" class="menu-link {{ request()->routeIs('accounting.products.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-boxes"></i></span>
                    <span class="menu-text">Products</span>
                    <span class="menu-badge">{{ $productCount }}</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('clients.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.clients.index') }}" class="menu-link {{ request()->routeIs('accounting.clients.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span class="menu-text">Clients</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('suppliers.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.suppliers.index') }}" class="menu-link {{ request()->routeIs('accounting.suppliers.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-truck"></i></span>
                    <span class="menu-text">Suppliers</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('invoices.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.invoices.index') }}" class="menu-link {{ request()->routeIs('accounting.invoices.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-file-invoice"></i></span>
                    <span class="menu-text">Invoices</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('payments.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.payments.index') }}" class="menu-link {{ request()->routeIs('accounting.payments.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-credit-card"></i></span>
                    <span class="menu-text">Payments</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('expenses.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.expenses.index') }}" class="menu-link {{ request()->routeIs('accounting.expenses.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="menu-text">Expenses</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('purchase-orders.manage'))
            <li class="menu-item">
                <a href="{{ route('accounting.purchase-orders.index') }}" class="menu-link {{ request()->routeIs('accounting.purchase-orders.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="menu-text">Purchase Orders</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('reports.view'))
            <li class="menu-item">
                <a href="{{ route('accounting.transactions.index') }}" class="menu-link {{ request()->routeIs('accounting.transactions.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-book"></i></span>
                    <span class="menu-text">Ledger</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('reports.view'))
            <li class="menu-item">
                <a href="{{ route('accounting.reports.index') }}" class="menu-link {{ request()->routeIs('accounting.reports.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="menu-text">Reports</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('audit.view'))
            <li class="menu-item">
                <a href="{{ route('accounting.audit-logs.index') }}" class="menu-link {{ request()->routeIs('accounting.audit-logs.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-clock-rotate-left"></i></span>
                    <span class="menu-text">Audit Log</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('users.manage'))
            <li class="menu-item">
                <a href="{{ route('dashboard.users.index') }}" class="menu-link {{ request()->routeIs('dashboard.users.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span class="menu-text">Users</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('roles.manage'))
            <li class="menu-item">
                <a href="{{ route('dashboard.roles.index') }}" class="menu-link {{ request()->routeIs('dashboard.roles.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="menu-text">Roles & Permissions</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <div class="sidebar-footer">
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('logout') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span class="menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<style>
.mobile-menu-btn { position: fixed; top: 16px; left: 16px; width: 40px; height: 40px; background: #CD2737; border: none; border-radius: 8px; cursor: pointer; display: none; align-items: center; justify-content: center; z-index: 1001; box-shadow: 0 4px 15px rgba(205, 39, 55, 0.4); color: white; font-size: 1.8rem; }
.sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; opacity: 0; transition: opacity 0.3s; z-index: 999; }
.sidebar-overlay.active { display: block; opacity: 1; }
.sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 80px; background: #CD2737; transition: width 0.3s ease; overflow: hidden; z-index: 1000; display: flex; flex-direction: column; }
.sidebar.active { width: 280px; }
.sidebar-header { display: flex; align-items: center; padding: 16px; height: 64px; border-bottom: 1px solid rgba(255,255,255,0.15); flex-shrink: 0; }
.toggle-btn { min-width: 36px; height: 36px; background: rgba(255,255,255,0.15); border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.85); transition: all 0.3s; font-size: 1.4rem; }
.toggle-btn:hover { background: rgba(255,255,255,0.25); color: white; }
.logo-text { margin-left: 14px; font-size: 1.8rem; font-weight: 700; color: white; white-space: nowrap; opacity: 0; transition: opacity 0.3s; }
.sidebar.active .logo-text { opacity: 1; }
.sidebar-user { display: flex; align-items: center; padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.15); flex-shrink: 0; gap: 10px; }
.user-avatar { min-width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem; flex-shrink: 0; }
.user-info { display: flex; flex-direction: column; opacity: 0; transition: opacity 0.3s; overflow: hidden; }
.sidebar.active .user-info { opacity: 1; }
.user-name { font-size: 1.3rem; font-weight: 600; color: white; white-space: nowrap; }
.user-role { font-size: 1.1rem; color: rgba(255,255,255,0.75); }
.sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
.sidebar-menu { list-style: none; }
.menu-item { padding: 2px 12px; }
.menu-link { display: flex; align-items: center; padding: 12px 12px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 10px; transition: all 0.25s; gap: 0; position: relative; }
.menu-link:hover { background: rgba(255,255,255,0.12); color: white; }
.menu-link.active { background: rgba(255,255,255,0.2); color: white; }
.menu-icon { min-width: 36px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
.menu-text { margin-left: 10px; font-size: 1.3rem; font-weight: 500; white-space: nowrap; opacity: 0; transition: opacity 0.3s; }
.sidebar.active .menu-text { opacity: 1; }
.menu-badge { margin-left: auto; background: rgba(255,255,255,0.2); color: white; padding: 2px 8px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; opacity: 0; transition: opacity 0.3s; }
.sidebar.active .menu-badge { opacity: 1; }
.sidebar-footer { border-top: 1px solid rgba(255,255,255,0.15); padding: 12px 0; flex-shrink: 0; }
@media (max-width: 768px) {
    .sidebar { width: 0; transform: translateX(-100%); }
    .sidebar.active { width: 280px; transform: translateX(0); }
    .logo-text, .user-info, .menu-text, .menu-badge { opacity: 1; }
}
</style>

