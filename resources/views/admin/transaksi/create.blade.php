{{-- resources/views/admin/transaksi/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="mb-4">
    <h4 class="section-title mb-1">Tambah Transaksi</h4>
    <p class="section-subtitle mb-0">Catat transaksi baru untuk customer</p>
</div>

<form action="/admin/transaksi" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">Item Produk</div>
                <div class="card-body">
                    <div id="itemRows">
                        <div class="row g-2 align-items-end item-row mb-2">
                            <div class="col-5">
                                <label class="form-label small fw-semibold">Produk</label>
                                <select name="produk_id[]" class="form-select">
                                    @foreach ($daftarProduk as $id => $label)
                                        <option value="{{ $id }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label small fw-semibold">Qty</label>
                                <input type="number" name="qty[]" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-3">
                                <label class="form-label small fw-semibold">Subtotal</label>
                                <input type="text" class="form-control" value="Rp 0" disabled>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-danger btn-remove-row"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-brand mt-2">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">Detail Transaksi</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach ($daftarCustomer as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Transaksi</label>
                        <input type="date" name="tanggal_transaksi" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span id="totalTransaksi">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="/admin/transaksi" class="btn btn-light w-50">Batal</a>
                <button type="submit" class="btn btn-brand w-50">Simpan Transaksi</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Catatan: ini hanya interaksi tampilan (tambah/hapus baris item),
    // perhitungan subtotal & total riil menyusul saat logic backend dipasang.
    document.getElementById('btnAddRow')?.addEventListener('click', function () {
        const rows = document.getElementById('itemRows');
        const clone = rows.querySelector('.item-row').cloneNode(true);
        rows.appendChild(clone);
    });
    document.getElementById('itemRows')?.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) e.target.closest('.item-row').remove();
        }
    });
</script>
@endpush
