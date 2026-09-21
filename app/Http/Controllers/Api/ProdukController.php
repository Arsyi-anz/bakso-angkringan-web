<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProdukController extends Controller
{
    /**
     * Daftar produk (produk unggulan untuk halaman Home).
     */
    public function index(): AnonymousResourceCollection
    {
        return ProdukResource::collection(
            Produk::orderBy('nama_produk')->get(),
        );
    }

    /**
     * Detail satu produk.
     */
    public function show(int $id): ProdukResource
    {
        return new ProdukResource(Produk::findOrFail($id));
    }
}
