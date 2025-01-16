<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class ApiTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hariIni = Carbon::today();

        $transaksi = Transaksi::with(['metodePembayaran', 'metodePembelian', 'detailTransaksi' => function ($q) {
            $q->with('produk');
        }])->whereDate('created_at', $hariIni)->orderBy('id', 'desc')->get();

        return response()->json([
            'message' => 'berhasil mengambil data transaksi hari ini',
            'data' => $transaksi,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'produk_id' => 'required|array',
            'jumlah' => 'required|array',
            'metode_pembayaran_id' => 'required|integer',
            'metode_pembelian_id' => 'required|integer',
            'total' => 'required|numeric',
        ]);

        // Generate a transaction code
        $kode_transaksi = 'TRX-' . Str::random(8);

        // Create the transaction
        $transaksi = Transaksi::create([
            'kode' => $kode_transaksi,
            'grand_total' => $validatedData['total'],
            'metode_pembayaran_id' => $validatedData['metode_pembayaran_id'],
            'metode_pembelian_id' => $validatedData['metode_pembelian_id'],
            'created_at' => Carbon::now(),
        ]);

        // Create detail transaksi and prepare WhatsApp message
        foreach ($validatedData['produk_id'] as $index => $produkId) {
            $produk = Produk::find($produkId);
            $subtotal = $validatedData['jumlah'][$index] * $produk->harga;

            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $produkId,
                'kode' => 'DTX-' . Str::random(8),
                'qty' => $validatedData['jumlah'][$index],
                'harga' => $produk->harga,
                'subtotal' => $subtotal,
            ]);
        }

        // Prepare and send WhatsApp message
        $this->sendDailySalesReport();

        // Return success response
        return response()->json(['message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function sendDailySalesReport()
    {
        $today = Carbon::today();
        $transactions = Transaksi::whereDate('created_at', $today)->get();

        $salesReport = [];
        $counter = 1;

        foreach ($transactions as $transaction) {
            foreach ($transaction->detailTransaksi as $detail) {
                $metodePembayaran = $transaction->metode_pembayaran_id == 1 ? ' (QRIS)' : '';
                $salesReport[] = $counter . '. ' . $detail->qty . ' ' . $detail->produk->nama . $metodePembayaran;
                $counter++;
            }
        }

        $tanggal = $today->format('d F Y');
        $pesan = "Penjualan $tanggal\n" . implode("\n", $salesReport);

        $this->kirimPesanWhatsApp($pesan);
    }

    public function kirimPesanWhatsApp($pesan)
    {
        $token = '2d4Bah4PZv7uqM2ATitX';  // Masukkan token Fonnte Anda
        $target = '120363334556671889@g.us';  // ID grup yang kamu dapatkan

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,    // Target berupa ID grup
                'message' => $pesan,    // Pesan yang ingin dikirim
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token",  // Pastikan menggunakan format Bearer untuk token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
    // protected function kirimPesanWhatsApp($pesan)
    // {
    //     $token = '2d4Bah4PZv7uqM2ATitX';  // Masukkan token Fonnte Anda
    //     $target = '6285839023590';      // Nomor tujuan

    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://api.fonnte.com/send',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => array(
    //             'target' => $target,
    //             'message' => $pesan,
    //             'countryCode' => '62',  // Kode negara Indonesia
    //         ),
    //         CURLOPT_HTTPHEADER => array(
    //             "Authorization: $token",  // Token Fonnte Anda
    //         ),
    //     ));

    //     $response = curl_exec($curl);
    //     curl_close($curl);

    //     return $response;
    // }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $transaksi = DetailTransaksi::with('produk')->where('transaksi_id', $id)->get();

            return response()->json([
                'message' => 'data transaksi berhasil di ambil',
                'data' => $transaksi
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'terjadi kesalahan pada server !',
            ], 500);
        }

        // return response()->json("mantap", 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);

            foreach ($request->details as $detail) {
                $detailTransaksi = DetailTransaksi::findOrFail($detail['id']);
                $detailTransaksi->produk_id = $detail['produk_id'];
                $detailTransaksi->qty = $detail['qty'];
                $detailTransaksi->harga = $detail['harga'];
                $detailTransaksi->subtotal = $detail['subtotal'];
                $detailTransaksi->save();
            }

            // Recalculate and update grand total
            $grandTotal = DetailTransaksi::where('transaksi_id', $id)->sum('subtotal');
            $transaksi->grand_total = $grandTotal;
            $transaksi->save();

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $transaksi->load('detailTransaksi.produk')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $detailTransaksi = DetailTransaksi::findOrFail($id);
            $transaksiId = $detailTransaksi->transaksi_id;

            $detailTransaksi->delete();

            // Recalculate and update grand total
            $transaksi = Transaksi::findOrFail($transaksiId);
            $grandTotal = DetailTransaksi::where('transaksi_id', $transaksiId)->sum('subtotal');
            $transaksi->grand_total = $grandTotal;
            $transaksi->save();

            return response()->json([
                'message' => 'Detail transaksi berhasil dihapus',
                'data' => $transaksi->load('detailTransaksi.produk')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus detail transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyTransaksi(string $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            $transaksi->delete();

            return response()->json([
                'message' => 'transaksi berhasil dihapus',
                'data' => $transaksi->load('Transaksi.produk')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
