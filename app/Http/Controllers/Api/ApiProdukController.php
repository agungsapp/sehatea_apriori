<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use App\Models\MetodePembelian;
use App\Models\Produk;
use Illuminate\Http\Request;

class ApiProdukController extends Controller
{

    public function metodePembelian()
    {
        $pembelian = MetodePembelian::all();

        return response()->json([
            'message' => 'berhasil mengambil data metode pembelian',
            'data' => $pembelian,
        ], 200);
    }
    public function metodePembayaran()
    {
        $pembayaran = MetodePembayaran::all();

        return response()->json([
            'message' => 'berhasil mengambil data metode pembayaran',
            'data' => $pembayaran,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::where('active', true)->get();

        return response()->json([
            'message' => 'berhasil mengambil data produk',
            'data' => $produk,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produk = Produk::find($id);

        return response()->json([
            'message' => "berhasil mendapatkan data produk $produk->nama",
            'data' => $produk,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
