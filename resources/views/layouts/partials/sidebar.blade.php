{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside class="app-sidebar" id="appSidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.jpeg') }}" class="brand-logo" alt="Bakso Angkringan">
        <div>
            <div class="brand-name">Bakso Angkringan</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="nav flex-column py-2">
        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Data Master</div>
        <a href="/admin/customer" class="nav-link {{ request()->is('admin/customer*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customer
        </a>
        <a href="/admin/produk" class="nav-link {{ request()->is('admin/produk*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Produk
        </a>

        <div class="sidebar-section-label">Transaksi</div>
        <a href="/admin/transaksi" class="nav-link {{ request()->is('admin/transaksi*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Daftar Transaksi
        </a>

        <div class="sidebar-section-label">Program Loyalty</div>
        <a href="/admin/loyalty/spin-voucher" class="nav-link {{ request()->is('admin/loyalty/spin-voucher*') ? 'active' : '' }}">
            <i class="bi bi-dice-5"></i> Spin &amp; Voucher
        </a>
        <a href="/admin/loyalty/instagram-story" class="nav-link {{ request()->is('admin/loyalty/instagram-story*') ? 'active' : '' }}">
            <i class="bi bi-instagram"></i> Instagram Story
        </a>
        <a href="/admin/loyalty/referral" class="nav-link {{ request()->is('admin/loyalty/referral*') ? 'active' : '' }}">
            <i class="bi bi-share"></i> Referral
        </a>
        <a href="/admin/loyalty/hampers" class="nav-link {{ request()->is('admin/loyalty/hampers*') ? 'active' : '' }}">
            <i class="bi bi-gift"></i> Hampers
        </a>

        <div class="sidebar-section-label">Report &amp; Export</div>
        <a href="/admin/report/customer" class="nav-link {{ request()->is('admin/report/customer*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Customer
        </a>
        <a href="/admin/report/transaksi" class="nav-link {{ request()->is('admin/report/transaksi*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-spreadsheet"></i> Laporan Transaksi
        </a>
        <a href="/admin/report/loyalty" class="nav-link {{ request()->is('admin/report/loyalty*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-medical"></i> Laporan Loyalty
        </a>
    </nav>
</aside>
