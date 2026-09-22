{{-- resources/views/admin/loyalty/referral.blade.php --}}
@extends('layouts.admin')

@section('title', 'Referral')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Program Referral</h4>
    <p class="section-subtitle mb-0">Pantau kode referral dan status validasinya</p>
</div>

<div class="card">
    <div class="card-header">Daftar Referral</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer (Pemberi Referral)</th>
                    <th>Kode Referral</th>
                    <th>Customer yang Direferensikan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($referral as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['customer']['nama'] }}</td>
                        <td><code>{{ $r['kode_referral'] }}</code></td>
                        <td>{{ $r['referredCustomer']['nama'] }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($r['tanggal'])->format('d M Y') }}</td>
                        <td>
                            @php
                                $badge = $r['status_valid'] === 'valid' ? 'badge-soft-success' : 'badge-soft-warning';
                                $label = $r['status_valid'] === 'valid' ? 'Valid' : 'Menunggu';
                            @endphp
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data referral.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
