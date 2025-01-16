<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Pengeluaran;
use App\Models\PengeluaranLain;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiHistoryController extends Controller
{

    public function ringkasan()
    {
        $today = Carbon::today();

        $getSaldoCash = Transaksi::whereDate('created_at', $today)
            ->where('metode_pembayaran_id', 2)
            ->sum('grand_total');

        $getSaldoOnline = Transaksi::whereDate('created_at', $today)
            ->where('metode_pembayaran_id', 1)  // Assuming 1 is for online payments (QRIS)
            ->sum('grand_total');

        $getPengeluaran = Pengeluaran::whereDate('created_at', $today)
            ->sum('subtotal');
        $getPengeluaranLain = PengeluaranLain::whereDate('created_at', $today)
            ->sum('harga');

        $totalPengeluaran = $getPengeluaran + $getPengeluaranLain;

        // Get the ID of 'Es Tawar' product using first()
        $esTawar = Produk::where('nama', 'like', '%Es Tawar%')->first();
        $esTawarId = $esTawar ? $esTawar->id : null;

        $getJumlahPenjualan = DetailTransaksi::whereHas('transaksi', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
            ->when($esTawarId, function ($query, $esTawarId) {
                return $query->where('produk_id', '!=', $esTawarId);  // Exclude 'Es Tawar' product
            })
            ->sum('qty');

        return response()->json([
            'data' => [
                'total_cup' => $getJumlahPenjualan,
                'saldo_cash' => $getSaldoCash,
                'saldo_online' => $getSaldoOnline,
                'pengeluaran' => $totalPengeluaran,
            ]
        ]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hariIni = Carbon::today();

        $detail = DetailTransaksi::with('produk')->whereDate('created_at', $hariIni)->orderBy('id', 'desc')->get();

        return response()->json([
            'message' => 'berhasil mengambil data detail transaksi hari ini',
            'data' => $detail,
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
