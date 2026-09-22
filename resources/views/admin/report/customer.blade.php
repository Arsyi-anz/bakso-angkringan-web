{{-- resources/views/admin/report/customer.blade.php --}}
@extends('layouts.admin')

@section('title', 'Laporan Customer')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="section-title mb-1">Laporan Customer</h4>
        <p class="section-subtitle mb-0">Rekap data dan aktivitas customer</p>
    </div>
    <form action="/admin/report/customer/export" method="GET">
        <input type="hidden" name="dari" value="{{ request('dari') }}">
        <input type="hidden" name="sampai" value="{{ request('sampai') }}">
        <button type="submit" class="btn btn-dark-brand">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </button>
    </form>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="/admin/report/customer" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-sm btn-outline-brand w-100">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Rekap Customer</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>No. HP</th>
                    <th>Kode Referral</th>
                    <th>Total Transaksi</th>
                    <th>Total Belanja</th>
                    <th>Terdaftar Sejak</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $l)
                    <tr>
                        <td class="fw-semibold">{{ $l['nama'] }}</td>
                        <td>{{ $l['no_hp'] }}</td>
                        <td><code>{{ $l['kode_referral'] }}</code></td>
                        <td>{{ $l['total_transaksi'] }}x</td>
                        <td>Rp {{ number_format($l['total_belanja'], 0, ',', '.') }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($l['tanggal_daftar'])->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data pada rentang ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
