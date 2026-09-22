{{-- resources/views/admin/search.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Hasil Pencarian</h4>
    <p class="section-subtitle mb-0">
        @if ($q !== '')
            Pencarian untuk <strong>"{{ $q }}"</strong>
        @else
            Ketik kata kunci pada kolom pencarian.
        @endif
    </p>
</div>

@if ($q === '')
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-search" style="font-size:2rem;"></i>
            <p class="mt-3 mb-0">Cari customer, transaksi, atau produk menggunakan kolom pencarian di kanan atas.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        {{-- Customer --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Customer</span>
                    <span class="badge badge-soft-brand">{{ $customers->count() }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($customers as $c)
                        <div class="list-group-item py-2">
                            <div class="fw-semibold">{{ $c['nama'] }}</div>
                            <div class="small text-muted">{{ $c['no_hp'] }} &middot; <code>{{ $c['kode_referral'] }}</code></div>
                            <a href="{{ route('admin.customer.index') }}" class="small">Lihat data customer &rarr;</a>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Tidak ada customer ditemukan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Transaksi --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Transaksi</span>
                    <span class="badge badge-soft-brand">{{ $transaksi->count() }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($transaksi as $t)
                        <div class="list-group-item py-2">
                            <div class="fw-semibold">#TRX-{{ $t['id'] }}</div>
                            <div class="small text-muted">
                                {{ $t['customer']['nama'] }} &middot; Rp {{ number_format($t['total_transaksi'], 0, ',', '.') }}
                            </div>
                            <a href="{{ route('admin.transaksi.show', $t['id']) }}" class="small">Lihat detail transaksi &rarr;</a>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Tidak ada transaksi ditemukan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Produk --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Produk</span>
                    <span class="badge badge-soft-brand">{{ $produk->count() }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($produk as $p)
                        <div class="list-group-item py-2">
                            <div class="fw-semibold">{{ $p['nama_produk'] }}</div>
                            <div class="small text-muted">Rp {{ number_format($p['harga'], 0, ',', '.') }}</div>
                            <a href="{{ route('admin.produk.index') }}" class="small">Lihat data produk &rarr;</a>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Tidak ada produk ditemukan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
@endsection