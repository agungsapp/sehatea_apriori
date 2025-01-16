<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class   ApiPengeluaranController extends Controller
{
    public function sendLaporanPengeluaran()
    {
        // Mendapatkan tanggal hari ini
        $today = Carbon::today()->format('d F Y');
        $sendWa = new ApiTransaksiController();

        // Mengambil data pengeluaran untuk hari ini
        $pengeluarans = Pengeluaran::with('bahan') // Load relasi bahan
            ->whereDate('created_at', Carbon::today())
            ->get();

        // Membuat format pesan
        $pesan = "Pengeluaran $today\n";
        foreach ($pengeluarans as $index => $pengeluaran) {
            $pesan .= ($index + 1) . ". " . $pengeluaran->bahan->nama . " (" . number_format($pengeluaran->subtotal, 0, ',', '.') . ")\n";
        }

        // Mengirim pesan melalui metode kirimPesanWhatsApp
        $response = $sendWa->kirimPesanWhatsApp($pesan);

        // Cek respons dari Fonnte API
        if ($response) {
            return response()->json(['message' => 'Laporan pengeluaran berhasil dikirim'], 200);
        } else {
            return response()->json(['message' => 'Gagal mengirim laporan pengeluaran'], 500);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $today = Carbon::today();

        $getPengeluaran = Pengeluaran::whereDate('created_at', $today)->with('bahan')->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'berhasil mengambil data pengeluaran hari ini',
            'data' => $getPengeluaran,
        ], 200);
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
        // Validasi data dari request
        $validatedData = $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'jenis_id' => 'nullable|exists:jenis_pengeluarans,id',
            'sumber_id' => 'nullable|exists:sumber_danas,id',
            'qty' => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        try {

            try {
                $bahan = Bahan::find($validatedData['bahan_id']);
                if ($bahan->harga !== $validatedData['harga_satuan']) {
                    $bahan->update(['harga' => $validatedData['harga_satuan']]);
                }
            } catch (\Throwable $th) {
                //throw $th;
                Log::error('Error updating bahan: ' . $th->getMessage());
            }

            // Simpan data ke database
            $pengeluaran = Pengeluaran::create([
                'bahan_id' => $validatedData['bahan_id'],
                'jenis_pengeluaran_id' => $validatedData['jenis_id'] ?? 1,
                'sumber_dana_id' => $validatedData['sumber_id'] ?? 1,
                'qty' => $validatedData['qty'],
                'harga_satuan' => $validatedData['harga_satuan'],
                'subtotal' => $validatedData['subtotal'],
            ]);

            // Kembalikan respons sukses
            return response()->json([
                'message' => 'Pengeluaran berhasil disimpan.',
                'data' => $pengeluaran
            ], 201);
        } catch (\Exception $e) {
            // Jika ada kesalahan, kembalikan error
            return response()->json([
                'message' => 'Gagal menyimpan pengeluaran.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pengeluaran = Pengeluaran::with(['jenisPengeluaran', 'sumberDana'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran ditemukan',
                'data' => $pengeluaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengeluaran tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
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
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data dari request
        $validatedData = $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'jenis_id' => 'nullable|exists:jenis_pengeluarans,id',
            'sumber_id' => 'nullable|exists:sumber_danas,id',
            'qty' => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        try {
            // Temukan pengeluaran berdasarkan ID
            $pengeluaran = Pengeluaran::findOrFail($id);

            // Update harga di tabel bahan jika ada perubahan
            try {
                $bahan = Bahan::find($validatedData['bahan_id']);
                if ($bahan->harga !== $validatedData['harga_satuan']) {
                    $bahan->update(['harga' => $validatedData['harga_satuan']]);
                }
            } catch (\Throwable $th) {
                Log::error('Error updating bahan: ' . $th->getMessage());
            }

            // Perbarui data pengeluaran
            $pengeluaran->update([
                'bahan_id' => $validatedData['bahan_id'],
                'jenis_pengeluaran_id' => $validatedData['jenis_id'] ?? $pengeluaran->jenis_pengeluaran_id,
                'sumber_dana_id' => $validatedData['sumber_id'] ?? $pengeluaran->sumber_dana_id,
                'qty' => $validatedData['qty'],
                'harga_satuan' => $validatedData['harga_satuan'],
                'subtotal' => $validatedData['subtotal'],
            ]);

            return response()->json([
                'message' => 'Pengeluaran berhasil diperbarui.',
                'data' => $pengeluaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui pengeluaran.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Temukan pengeluaran berdasarkan ID
            $pengeluaran = Pengeluaran::findOrFail($id);

            // Hapus pengeluaran
            $pengeluaran->delete();

            return response()->json([
                'message' => 'Pengeluaran berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus pengeluaran.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
