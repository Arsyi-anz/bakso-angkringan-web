{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Dashboard</h4>
    <p class="section-subtitle mb-0">Ringkasan performa program loyalty Bakso Angkringan</p>
</div>

<div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-xl-5">
    @foreach ($stats as $stat)
        <div class="col">
            <div class="card stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi {{ $stat['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ number_format($stat['value']) }}{{ $stat['suffix'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
