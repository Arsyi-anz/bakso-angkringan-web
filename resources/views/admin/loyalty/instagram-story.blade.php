{{-- resources/views/admin/loyalty/instagram-story.blade.php --}}
@extends('layouts.admin')

@section('title', 'Instagram Story')

@section('content')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Validasi Instagram Story</h4>
    <p class="section-subtitle mb-0">Daftar bukti story customer yang perlu divalidasi admin</p>
</div>

<div class="card">
    <div class="card-header">Daftar Link Instagram Story</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Link Story</th>
                    <th>Tanggal Upload</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buktiStory as $b)
                    <tr>
                        <td class="fw-semibold">{{ $b['customer']['nama'] }}</td>
                        <td><a href="{{ $b['url_bukti'] }}" target="_blank" rel="noopener">{{ Str::limit($b['url_bukti'], 40) }}</a></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($b['tanggal_kirim'])->format('d M Y H:i') }}</td>
                        <td>
                            @php
                                $badge = match($b['status_verifikasi']) {
                                    'diterima' => 'badge-soft-success',
                                    'pending' => 'badge-soft-warning',
                                    default => 'badge-soft-danger',
                                };
                                $label = match($b['status_verifikasi']) {
                                    'diterima' => 'Diterima',
                                    'pending' => 'Menunggu',
                                    default => 'Ditolak',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="text-end">
                            @if ($b['status_verifikasi'] === 'pending')
                                <form action="/admin/loyalty/instagram-story/{{ $b['id'] }}/validasi" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand"><i class="bi bi-check-lg"></i> Validasi</button>
                                </form>
                                <form action="/admin/loyalty/instagram-story/{{ $b['id'] }}/tolak" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i> Tolak</button>
                                </form>
                            @else
                                <span class="text-muted small">Sudah diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada bukti story yang diunggah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
