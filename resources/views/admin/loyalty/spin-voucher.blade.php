{{-- resources/views/admin/loyalty/spin-voucher.blade.php --}}
@extends('layouts.admin')

@section('title', 'Spin & Voucher')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Spin &amp; Voucher</h4>
    <p class="section-subtitle mb-0">Riwayat undian spin dan status voucher reward customer</p>
</div>

<ul class="nav nav-tabs mb-3" id="spinVoucherTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-riwayat-spin" type="button">Riwayat Spin</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-voucher" type="button">Kode Voucher</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-riwayat-spin">
        <div class="card">
            <div class="card-header">Riwayat Spin</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Customer</th><th>Tanggal</th><th>Reward</th><th>Keterangan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatSpin as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s['customer']['nama'] }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($s['tanggal_spin'])->format('d M Y H:i') }}</td>
                                <td><span class="badge badge-soft-brand">{{ $s['jenis_reward'] }}</span></td>
                                <td>{{ $s['keterangan'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat spin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-voucher">
        <div class="card">
            <div class="card-header">Kode Voucher per Customer</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Customer</th><th>Kode Voucher</th><th>Status</th><th>Keterangan</th><th>Kadaluarsa</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($voucher as $v)
                            <tr>
                                <td class="fw-semibold">{{ $v['customer']['nama'] }}</td>
                                <td><code>{{ $v['kode_voucher'] }}</code></td>
                                <td>
                                    @php
                                        $badge = match($v['status']) {
                                            'aktif' => 'badge-soft-success',
                                            'terpakai' => 'badge-soft-info',
                                            default => 'badge-soft-danger',
                                        };
                                        $label = match($v['status']) {
                                            'aktif' => 'Aktif',
                                            'terpakai' => 'Terpakai',
                                            default => 'Kedaluwarsa',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td>{{ $v['keterangan'] ?? '-' }}</td>
                                <td>{{ $v['tanggal_kadaluarsa'] ? \Illuminate\Support\Carbon::parse($v['tanggal_kadaluarsa'])->format('d M Y') : '-' }}</td>
                                <td class="text-end">
                                    @if ($v['status'] === 'terpakai')
                                        <span class="badge badge-soft-info">Sudah dipakai</span>
                                    @elseif ($v['status'] === 'kedaluwarsa')
                                        <span class="text-muted small">-</span>
                                    @else
                                        <form action="/admin/loyalty/spin-voucher/{{ $v['id'] }}/pakai" method="POST" class="d-inline" onsubmit="return confirm('Pakai voucher {{ $v['kode_voucher'] }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-brand">Pakai</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada voucher.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
