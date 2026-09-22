{{-- resources/views/admin/report/loyalty.blade.php --}}
@extends('layouts.admin')

@section('title', 'Laporan Loyalty')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="section-title mb-1">Laporan Program Loyalty</h4>
        <p class="section-subtitle mb-0">Ringkasan performa Spin &amp; Voucher, Instagram Story, Referral, dan Hampers</p>
    </div>
    <form action="/admin/report/loyalty/export" method="GET">
        <input type="hidden" name="periode" value="{{ $periode }}">
        <input type="hidden" name="dari" value="{{ $dariTerpilih }}">
        <input type="hidden" name="sampai" value="{{ $sampaiTerpilih }}">
        <button type="submit" class="btn btn-dark-brand">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </button>
    </form>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="/admin/report/loyalty" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold">Periode</label>
                <select name="periode" class="form-select form-select-sm">
                    <option value="bulan_ini" @selected($periode === 'bulan_ini')>Bulan Ini</option>
                    <option value="bulan_lalu" @selected($periode === 'bulan_lalu')>Bulan Lalu</option>
                    <option value="custom" @selected($periode === 'custom')>Rentang Kustom</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dariTerpilih }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampaiTerpilih }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-sm btn-outline-brand w-100">Terapkan Filter</button>
            </div>
        </form>
        <div class="small text-muted mt-2">Periode aktif: <strong>{{ $labelPeriode }}</strong></div>
    </div>
</div>

<div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-xl-5">
    @foreach ($ringkasan as $r)
        <div class="col">
            <div class="card stat-card h-100">
                <div class="stat-value">{{ number_format($r['value']) }}</div>
                <div class="stat-label">{{ $r['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">Detail per Program</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th>Program</th><th>Keterangan</th><th class="text-end">Jumlah</th></tr>
            </thead>
            <tbody>
                @forelse ($detail as $d)
                    <tr>
                        <td>{{ $d['program'] }}</td>
                        <td>{{ $d['keterangan'] }}</td>
                        <td class="text-end">{{ number_format($d['jumlah']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted small py-3">Belum ada data program loyalty.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
