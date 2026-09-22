<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::latest()->paginate(10);

        return view('admin.produk.index', compact('produk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
        ]);

        Produk::create($validated);

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
        ]);

        $produk->update($validated);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        try {
            $produk->delete();

            return back()->with('success', 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->withErrors([
                'delete' => 'Produk tidak dapat dihapus karena sudah dipakai di transaksi.',
            ]);
        }
    }
}