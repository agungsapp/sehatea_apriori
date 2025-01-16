<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('produk.index', [
            'produks' => Produk::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric|min:3',
        ], [
            'nama.required' => 'nama produk wajib di isi',
            'harga.required' => 'harga produk wajib di isi',
            'harga.numeric' => 'harga produk harus berupa angka',
            'harga.min' => 'harga produk minimal 3 digit',
        ]);

        try {
            $produk = new Produk();
            $produk->nama = $request->nama;
            $produk->harga = $request->harga;
            $produk->save();

            alert()->success('Berhasil', "berhasil menambahkan produk $request->nama");
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error('Gagal', "terjadi kesalahan pada sever !");
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric|min:3',
        ], [
            'nama.required' => 'nama produk wajib di isi',
            'harga.required' => 'harga produk wajib di isi',
            'harga.numeric' => 'harga produk harus berupa angka',
            'harga.min' => 'harga produk minimal 3 digit',
        ]);
        try {
            $produk = Produk::find($id);
            $produk->nama = $request->nama;
            $produk->harga = $request->harga;
            $produk->save();

            alert()->success('Berhasil', "berhasil update produk $request->nama");
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error('Gagal', "terjadi kesalahan pada sever !");
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produk = Produk::find($id);
        try {
            $produk->delete();

            alert()->success('Berhasil', "berhasil menghapus produk $produk->nama");
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error('Gagal', "terjadi kesalahan pada sever !");
            return redirect()->back();
        }
    }





    public function toggleStatus(Request $request)
    {
        $produk = Produk::find($request->id);
        if ($produk) {
            $produk->active = $request->status;
            $produk->save();

            return response()->json(['message' => 'Status updated successfully']);
        }

        return response()->json(['message' => 'Produk not found'], 404);
    }
}
