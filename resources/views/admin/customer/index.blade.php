{{-- resources/views/admin/customer/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Data Customer')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="section-title mb-1">Data Customer</h4>
        <p class="section-subtitle mb-0">Kelola data customer yang terdaftar di aplikasi</p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span>Daftar Customer</span>
        <form action="/admin/customer" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, kode referral, atau no. HP" style="min-width:240px">
            <button class="btn btn-sm btn-outline-brand" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>No. HP</th>
                    <th>Kode Referral</th>
                    <th>Total Transaksi</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $c)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $c['nama'] }}</td>
                        <td>{{ $c['no_hp'] }}</td>
                        <td><code>{{ $c['kode_referral'] }}</code></td>
                        <td>{{ $c['total_transaksi'] }}x</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($c['tanggal_daftar'])->format('d M Y') }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#modalEditCustomer{{ $c['id'] }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalHapusCustomer{{ $c['id'] }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data customer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="small text-muted">Menampilkan {{ count($customers) }} data</span>
        {{-- {{ $customers->links() }} --}}
    </div>
</div>

{{-- Modal Edit & Hapus per-customer --}}
@foreach ($customers as $c)
    <div class="modal fade" id="modalEditCustomer{{ $c['id'] }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/admin/customer/{{ $c['id'] }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Customer</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama</label>
                            <input type="text" name="nama" class="form-control" value="{{ $c['nama'] }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ $c['no_hp'] }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kode Referral</label>
                            <input type="text" class="form-control" value="{{ $c['kode_referral'] }}" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-brand">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusCustomer{{ $c['id'] }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size:2rem;"></i>
                    <p class="mt-3 mb-0">Hapus customer <strong>{{ $c['nama'] }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <form action="/admin/customer/{{ $c['id'] }}" method="POST">
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
