{{-- resources/views/admin/transaksi/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="section-title mb-1">Detail Transaksi #TRX-{{ $transaksi['id'] }}</h4>
        <p class="section-subtitle mb-0">Rincian item dan informasi customer</p>
    </div>
    <a href="/admin/transaksi" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Informasi Customer</div>
            <div class="card-body small">
                <div class="mb-2"><span class="text-muted d-block">Nama</span><span class="fw-semibold">{{ $transaksi['customer']['nama'] }}</span></div>
                <div class="mb-2"><span class="text-muted d-block">No. HP</span><span class="fw-semibold">{{ $transaksi['customer']['no_hp'] }}</span></div>
                <div class="mb-2"><span class="text-muted d-block">Tanggal Transaksi</span><span class="fw-semibold">{{ \Illuminate\Support\Carbon::parse($transaksi['tanggal_transaksi'])->format('d M Y H:i') }}</span></div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Item Produk</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi['items'] as $item)
                            <tr>
                                <td>{{ $item['produk']['nama_produk'] }}</td>
                                <td>{{ $item['jumlah'] }}</td>
                                <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">Rp {{ number_format($transaksi['total_transaksi'], 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
