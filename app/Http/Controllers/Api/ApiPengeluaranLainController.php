<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranLain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiPengeluaranLainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Query PengeluaranLain with today's records only, ordered by created_at in descending order
            $pengeluarans = PengeluaranLain::with(['jenisPengeluaran', 'sumberDana'])
                ->whereDate('tanggal_pengeluaran', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->get();  // Fetch all records without pagination

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran berhasil diambil',
                'data' => $pengeluarans
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Define validation rules
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'jenis_pengeluaran_id' => 'nullable|exists:jenis_pengeluarans,id',
                'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
                'keterangan' => 'nullable|string|max:1000',
                'tanggal_pengeluaran' => 'required|date',
            ]);

            // Handle validation failure
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Set default values for nullable fields if not provided
            $data = $validator->validated();
            $data['jenis_pengeluaran_id'] = $data['jenis_pengeluaran_id'] ?? 1;
            $data['sumber_dana_id'] = $data['sumber_dana_id'] ?? 1;

            // Create the PengeluaranLain record
            $pengeluaran = PengeluaranLain::create($data);

            // Return success response with related data loaded
            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran->load(['jenisPengeluaran', 'sumberDana'])
            ], 201);
        } catch (\Exception $e) {
            // Return error response in case of exception
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data pengeluaran',
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
            $pengeluaran = PengeluaranLain::with(['jenisPengeluaran', 'sumberDana'])->findOrFail($id);

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'jenis_pengeluaran_id' => 'required|exists:jenis_pengeluarans,id',
                'sumber_dana_id' => 'required|exists:sumber_danas,id',
                'keterangan' => 'nullable|string|max:1000',
                'tanggal_pengeluaran' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pengeluaran = PengeluaranLain::findOrFail($id);
            $pengeluaran->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran berhasil diperbarui',
                'data' => $pengeluaran->load(['jenisPengeluaran', 'sumberDana'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data pengeluaran',
                'error' => $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pengeluaran = PengeluaranLain::findOrFail($id);
            $pengeluaran->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data pengeluaran',
                'error' => $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Get summary of expenses
     */
    public function getSummary(Request $request)
    {
        try {
            $query = PengeluaranLain::query();

            // Filter by date range
            if ($request->has(['start_date', 'end_date'])) {
                $query->whereBetween('tanggal_pengeluaran', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $summary = [
                'total_pengeluaran' => $query->sum('harga'),
                'jumlah_transaksi' => $query->count(),
                'rata_rata_pengeluaran' => $query->avg('harga'),
                'pengeluaran_tertinggi' => $query->max('harga'),
                'pengeluaran_terendah' => $query->min('harga'),
                'per_jenis_pengeluaran' => $query->select('jenis_pengeluaran_id')
                    ->selectRaw('COUNT(*) as jumlah_transaksi')
                    ->selectRaw('SUM(harga) as total_harga')
                    ->groupBy('jenis_pengeluaran_id')
                    ->with('jenisPengeluaran:id,nama')
                    ->get()
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Ringkasan pengeluaran berhasil diambil',
                'data' => $summary
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil ringkasan pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
