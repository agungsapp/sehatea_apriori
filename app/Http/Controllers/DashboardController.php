<?php

namespace App\Http\Controllers;


use App\Models\Bahan;
use App\Models\DetailTransaksi;
use App\Models\Pengeluaran;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{



    public function getMonthData($month)
    {
        // Validasi bulan
        $month = intval($month);
        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid month'], 400);
        }

        // Ambil data untuk bulan yang dipilih
        $selectedMonthStart = Carbon::create(Carbon::now()->year, $month, 1)->startOfMonth();
        $selectedMonthEnd = Carbon::create(Carbon::now()->year, $month, 1)->endOfMonth();

        // Mengambil data qty per hari untuk bulan yang dipilih
        $qtyPerDay = DetailTransaksi::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(qty) as total_qty'))
            ->whereBetween('created_at', [$selectedMonthStart, $selectedMonthEnd])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Siapkan array data untuk chart
        $labels = [];
        $qtyData = [];
        foreach ($qtyPerDay as $data) {
            $labels[] = $data->date;
            $qtyData[] = $data->total_qty;
        }

        // Kembalikan data sebagai JSON
        return response()->json([
            'labels' => $labels,
            'qtyData' => $qtyData
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil bulan yang ada di tabel transaksi
        $availableMonths = Transaksi::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Ambil data untuk bulan ini
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();

        $topPenjualans = DetailTransaksi::select('produk_id', DB::raw('SUM(qty) as total_qty'))
            // ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->groupBy('produk_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();
        // Hitung omset, pengeluaran, dan qty per hari untuk bulan ini
        $omset = Transaksi::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->sum('grand_total');
        $pengeluaran = Pengeluaran::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->sum('subtotal');
        $terjual = DetailTransaksi::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->sum('qty');

        // Mengambil data qty per hari untuk bulan ini
        $qtyPerDay = DetailTransaksi::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(qty) as total_qty'))
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->groupBy('date')
            ->orderBy('date', 'asc') // Pastikan ada arah pengurutan
            ->get();

        // Siapkan array data untuk chart
        $labels = [];
        $qtyData = [];
        foreach ($qtyPerDay as $data) {
            $labels[] = $data->date;
            $qtyData[] = $data->total_qty;
        }

        // Siapkan data untuk view
        $data = [
            'produk' => Produk::count(),
            'bahan' => Bahan::count(),
            'omset' => $omset,
            'pengeluaran' => $pengeluaran,
            'terjual' => $terjual,
            'labels' => json_encode($labels),
            'qtyData' => json_encode($qtyData),
            'availableMonths' => $availableMonths,  // Kirim bulan yang tersedia ke view
            'topPenjualans' => $topPenjualans
        ];

        return view('dashboard.index', $data);
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
