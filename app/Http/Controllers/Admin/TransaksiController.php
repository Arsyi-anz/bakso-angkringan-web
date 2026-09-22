<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $transaksi = Transaksi::query()
            ->with('customer')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('tanggal_transaksi', $request->query('tanggal'));
            })
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $daftarCustomer = Customer::latest()->pluck('nama', 'id');
        $daftarProduk = Produk::latest()->get()
            ->mapWithKeys(fn (Produk $produk) => [
                $produk->id => $produk->nama_produk.' - Rp '.number_format($produk->harga, 0, ',', '.'),
            ]);

        return view('admin.transaksi.create', compact('daftarCustomer', 'daftarProduk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'tanggal_transaksi' => ['required', 'date'],
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['integer', 'distinct'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'integer', 'min:1'],
        ]);

        if (strlen((string) $validated['tanggal_transaksi']) === 10) {
            $validated['tanggal_transaksi'] .= ' '.now()->format('H:i:s');
        }

        $transaksi = DB::transaction(function () use ($validated): Transaksi {
            $total = 0;
            $items = [];

            foreach ($validated['produk_id'] as $index => $produkId) {
                $produk = Produk::findOrFail($produkId);
                $jumlah = $validated['qty'][$index];
                $subtotal = $produk->harga * $jumlah;
                $total += $subtotal;
                $items[] = ['produk' => $produk, 'jumlah' => $jumlah];
            }

            $transaksi = Transaksi::create([
                'customer_id' => $validated['customer_id'],
                'admin_id' => auth()->id(),
                'total_transaksi' => $total,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

            foreach ($items as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['produk']->harga,
                    'subtotal' => $item['produk']->harga * $item['jumlah'],
                ]);
            }

            return $transaksi;
        });

        return redirect()
            ->route('admin.transaksi.show', $transaksi)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['customer', 'detailTransaksis.produk']);

        // view memakai key "items" untuk iterasi detail produk.
        $transaksi->setAttribute('items', $transaksi->detailTransaksis);

        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->detailTransaksis()->delete();
        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}