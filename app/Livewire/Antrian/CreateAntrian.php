<?php

namespace App\Livewire\Antrian;

use App\Models\Antrian;
use App\Models\Bank;
use App\Models\Barang;
use App\Models\Instansi;
use App\Models\Jasa;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\PelangganKendaraan;
use App\Models\PembayaranPenjualan;
use App\Models\PembayaranTransaksi;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PenjualanJasa;
use App\Models\Stok;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateAntrian extends Component
{
    use LivewireAlert;

    public $isInstansi = false;

    public $isTunai = true;


    public
        $noInvoice,
        $tanggalMasuk,
        $selectedInstansi,
        $namaPelanggan,
        $kendaraan,
        $nopol,
        $telepon,
        $catatan,
        $status;

    // cart form

    public $isEdit = false;
    public $isLunas = false;

    public $instansis, $barangs, $banks, $jasas;


    // public $isBaru = true;

    public function mount()
    {
        $this->instansis = Instansi::orderBy('nama_instansi', 'asc')->get();
    }


    public function updatedNopol($value)
    {
        $this->nopol = strtoupper(preg_replace('/\s+/', '', $value));
    }


    public function updatedIsInstansi($value)
    {
        if ($value) {
            // Jika baru, reset selectedBarang saja
            $this->selectedInstansi = null;
        } else {
            // Jika tidak baru, reset namaBarang saja
            $this->namaPelanggan = null;
        }
    }


    #[On('update-selected-instansi')]
    public function updateSelectedInstansi($value)
    {
        $this->selectedInstansi = $value;
    }


    public function simpan()
    {
        try {

            // Jika ada jasa dalam keranjang, gabungkan rules
            $validationRules = [
                'selectedInstansi' => $this->isInstansi ? 'required|exists:instansis,id' : 'nullable',
                'namaPelanggan' => !$this->isInstansi ? 'required|string|min:3' : 'nullable',
                'tanggalMasuk' => 'required|date',
                'kendaraan' => 'required|string',
                'nopol' => 'required|string',
                'telepon' => 'required|string',
            ];

            // Validasi dengan rules yang sesuai
            try {
                $this->validate($validationRules);
            } catch (\Illuminate\Validation\ValidationException $e) {
                dd($e->errors());
                // $this->alert('error', 'Terjadi kesalahan error validasi ' . $e->errors());
            }

            DB::beginTransaction();

            try {


                // create pelanggan data & kendaraan
                $pelanggan = new Pelanggan();
                if (!$this->isInstansi) {
                    $pelanggan->nama = $this->namaPelanggan;
                } else {
                    $pelanggan->id_instansi = $this->selectedInstansi;
                }
                $pelanggan->telepon = $this->telepon;
                $pelanggan->tipe = $this->isInstansi ? 'instansi' : 'reguler';
                $pelanggan->save();

                $kendaraan = new Kendaraan();
                $kendaraan->nama_kendaraan = $this->kendaraan;
                $kendaraan->nopol = $this->nopol;
                $kendaraan->save();

                $pelangganKendaraan = new PelangganKendaraan();
                $pelangganKendaraan->id_pelanggan = $pelanggan->id;
                $pelangganKendaraan->id_kendaraan = $kendaraan->id;
                $pelangganKendaraan->save();

                // Buat record Penjualan
                $antrian = new Antrian();
                $antrian->id_user = Auth::id() ?? 1;
                $antrian->id_pelanggan_kendaraan = $pelangganKendaraan->id;
                $antrian->status = 'antri';
                $antrian->tanggal_masuk = $this->tanggalMasuk;
                $antrian->catatan = $this->catatan;
                $antrian->save();

                DB::commit();

                // Reset form
                $this->reset([
                    'tanggalMasuk',
                    'selectedInstansi',
                    'namaPelanggan',
                    'kendaraan',
                    'nopol',
                    'telepon',
                    'catatan',
                    'status',
                ]);

                $this->dispatch('reset-choices');
                $this->showSuccess('Transaksi berhasil disimpan!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaksi Save Error: ' . $e->getMessage());
                $this->showError('Gagal menyimpan transaksi: ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $validationErrors = json_encode($e->errors(), JSON_PRETTY_PRINT);
            Log::channel('transaksi')->error('ERROR VALIDASI DATA CREATE', [
                'errors' => $validationErrors,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    // Helper method untuk menampilkan error
    private function showError($message)
    {
        return $this->alert('error', $message, [
            'showConfirmButton' => false,
            'timer' => 3000,
            'position' => 'center',
            'toast' => true
        ]);
    }

    // Helper method untuk menampilkan success
    private function showSuccess($message)
    {
        return $this->alert('success', $message, [
            'showConfirmButton' => false,
            'timer' => 2000,
            'position' => 'center',
            'toast' => false
        ]);
    }

    public function render()
    {
        return view('livewire.antrian.create-antrian');
    }
}
