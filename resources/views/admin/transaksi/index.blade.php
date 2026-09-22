{{-- resources/views/admin/transaksi/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Transaksi')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="section-title mb-1">Daftar Transaksi</h4>
        <p class="section-subtitle mb-0">Riwayat seluruh transaksi customer</p>
    </div>
    <a href="/admin/transaksi/create" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Tambah Transaksi
    </a>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span>Riwayat Transaksi</span>
        <form action="/admin/transaksi" method="GET" class="d-flex flex-wrap gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari customer">
            <input type="date" name="tanggal" class="form-control form-control-sm">
            <button class="btn btn-sm btn-outline-brand" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $t)
                    <tr>
                        <td class="fw-semibold">#TRX-{{ $t['id'] }}</td>
                        <td>{{ $t['customer']['nama'] }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($t['tanggal_transaksi'])->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($t['total_transaksi'], 0, ',', '.') }}</td>

                        <td class="text-end">
                            <a href="/admin/transaksi/{{ $t['id'] }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalHapusTransaksi{{ $t['id'] }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach ($transaksi as $t)
    <div class="modal fade" id="modalHapusTransaksi{{ $t['id'] }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size:2rem;"></i>
                    <p class="mt-3 mb-0">Hapus transaksi <strong>#TRX-{{ $t['id'] }}</strong>?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <form action="/admin/transaksi/{{ $t['id'] }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
