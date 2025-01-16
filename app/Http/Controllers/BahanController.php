<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('bahan.index', [
            'bahans' => Bahan::all(),
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
            'harga' => 'required',
            'harga_satuan' => 'required',
            'bobot' => 'required',
            'satuan' => 'required',
        ]);
        try {
            $bahan = new Bahan();
            $bahan->nama = $request->nama;
            $bahan->harga = $request->harga;
            $bahan->harga_satuan = $request->harga_satuan;
            $bahan->bobot = $request->bobot;
            $bahan->satuan = $request->satuan;
            $bahan->save();
            alert()->success("Berhasil", "Berhasil menyimpan bahan $bahan->nama");
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error("Gagal", "Terjadi kesalahan pada server");
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
