{{-- resources/views/admin/loyalty/hampers.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hampers')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Kelayakan Hampers</h4>
    <p class="section-subtitle mb-0">Rekap kelayakan customer menerima hampers bulan berjalan</p>
</div>

<div class="card">
    <div class="card-header">Daftar Customer &amp; Status Kelayakan</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Periode</th>
                    <th>Jumlah Repeat Order</th>
                    <th>Jumlah Referral Valid</th>
                    <th>Status Kelayakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hampers as $h)
                    <tr>
                        <td class="fw-semibold">{{ $h['customer']['nama'] }}</td>
                        <td>{{ $h['periode'] }}</td>
                        <td>{{ $h['jumlah_repeat_order'] }}x</td>
                        <td>{{ $h['jumlah_referral'] }}</td>
                        <td>
                            @if ($h['status_kelayakan'] === 'memenuhi')
                                <span class="badge badge-soft-success">Layak</span>
                            @else
                                <span class="badge badge-soft-warning">Belum Layak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data hampers bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
