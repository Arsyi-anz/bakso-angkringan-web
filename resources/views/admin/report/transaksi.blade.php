{{-- resources/views/admin/report/transaksi.blade.php --}}
@extends('layouts.admin')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="section-title mb-1">Laporan Transaksi</h4>
        <p class="section-subtitle mb-0">Rekap transaksi dan pendapatan</p>
    </div>
    <form action="/admin/report/transaksi/export" method="GET">
        <input type="hidden" name="dari" value="{{ request('dari') }}">
        <input type="hidden" name="sampai" value="{{ request('sampai') }}">
        <button type="submit" class="btn btn-dark-brand">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </button>
    </form>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="/admin/report/transaksi" method="GET" class="row g-2 align-items-end">
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Rekap Transaksi</span>
        <span class="fw-bold">Total: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $l)
                    <tr>
                        <td class="fw-semibold">#TRX-{{ $l['id'] }}</td>
                        <td>{{ $l['customer']['nama'] }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($l['tanggal_transaksi'])->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($l['total_transaksi'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data pada rentang ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
