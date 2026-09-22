{{-- resources/views/layouts/partials/navbar.blade.php --}}
<header class="app-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary sidebar-toggle" type="button" id="sidebarToggleBtn">
            <i class="bi bi-list"></i>
        </button>
        <form action="/admin/search" method="GET" class="search-box d-none d-md-block" role="search">
            <i class="bi bi-search"></i>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari customer, transaksi, produk..." value="{{ request('q') }}">
        </form>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                <span class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-person-fill"></i>
                </span>
                <span class="d-none d-sm-inline">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Admin' }}</span>
                <i class="bi bi-chevron-down small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><span class="dropdown-item-text small text-muted">Masuk sebagai Admin</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="/logout" method="POST" class="px-3 py-1" onsubmit="return confirm('Yakin ingin logout?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
