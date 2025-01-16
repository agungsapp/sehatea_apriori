<?php

namespace App\Livewire\Antrian;

use App\LivewireAlertHelpers;
use App\Models\Antrian;
use App\Models\Instansi;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\PelangganKendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class EditAntrian extends Component
{
    use LivewireAlertHelpers;

    public $isInstansi = false;
    public $isTunai = true;
    public $antrianId;
    public $antrians;
    public $noInvoice;
    public $tanggalMasuk;
    public $selectedInstansi;
    public $namaPelanggan;
    public $kendaraan;
    public $nopol;
    public $telepon;
    public $catatan;
    public $status;
    public $isEdit = true;
    public $isLunas = false;
    public $instansis;
    public $oldPelangganId;
    public $oldKendaraanId;
    public $oldPelangganKendaraanId;

    public function mount($id)
    {
        $this->instansis = Instansi::orderBy('nama_instansi', 'asc')->get();
        $this->antrianId = $id;
        $this->antrians = Antrian::with(['pelangganKendaraan.pelanggan', 'pelangganKendaraan.kendaraan'])->findOrFail($id);

        // Store old IDs for reference
        $this->oldPelangganId = $this->antrians->pelangganKendaraan->pelanggan->id;
        $this->oldKendaraanId = $this->antrians->pelangganKendaraan->kendaraan->id;
        $this->oldPelangganKendaraanId = $this->antrians->pelangganKendaraan->id;

        $this->loadData();
    }

    public function loadData()
    {
        $pelanggan = $this->antrians->pelangganKendaraan->pelanggan;

        $this->tanggalMasuk = Carbon::parse($this->antrians->tanggal_masuk)->format('Y-m-d\TH:i');
        $this->status = $this->antrians->status;
        $this->isInstansi = $pelanggan->tipe === 'instansi';
        $this->selectedInstansi = $pelanggan->id_instansi;
        $this->namaPelanggan = $pelanggan->nama;
        $this->kendaraan = $this->antrians->pelangganKendaraan->kendaraan->nama_kendaraan;
        $this->nopol = $this->antrians->pelangganKendaraan->kendaraan->nopol;
        $this->telepon = $pelanggan->telepon;
        $this->catatan = $this->antrians->catatan;
    }

    public function updatedNopol($value)
    {
        $this->nopol = strtoupper(preg_replace('/\s+/', '', $value));
    }

    public function updatedIsInstansi($value)
    {
        if ($value) {
            $this->namaPelanggan = null;
        } else {
            $this->selectedInstansi = null;
        }
    }

    #[On('update-selected-instansi')]
    public function updateSelectedInstansi($value)
    {
        $this->selectedInstansi = $value;
    }

    public function update()
    {
        try {
            $validationRules = [
                'selectedInstansi' => $this->isInstansi ? 'required|exists:instansis,id' : 'nullable',
                'namaPelanggan' => !$this->isInstansi ? 'required|string|min:3' : 'nullable',
                'tanggalMasuk' => 'required|date',
                'kendaraan' => 'required|string',
                'nopol' => 'required|string',
                'telepon' => 'required|string',
                'status' => 'required|in:antri,proses,selesai,batal',
            ];

            $this->validate($validationRules);

            DB::beginTransaction();

            try {
                // Update Pelanggan
                $pelanggan = Pelanggan::find($this->oldPelangganId);
                if (!$this->isInstansi) {
                    $pelanggan->nama = $this->namaPelanggan;
                    $pelanggan->id_instansi = null;
                } else {
                    $pelanggan->nama = null;
                    $pelanggan->id_instansi = $this->selectedInstansi;
                }
                $pelanggan->telepon = $this->telepon;
                $pelanggan->tipe = $this->isInstansi ? 'instansi' : 'reguler';
                $pelanggan->save();

                // Update Kendaraan
                $kendaraan = Kendaraan::find($this->oldKendaraanId);
                $kendaraan->nama_kendaraan = $this->kendaraan;
                $kendaraan->nopol = $this->nopol;
                $kendaraan->save();

                // Update Antrian
                $antrian = Antrian::find($this->antrianId);
                $antrian->tanggal_masuk = $this->tanggalMasuk;
                $antrian->status = $this->status;
                $antrian->catatan = $this->catatan;
                $antrian->save();

                DB::commit();

                $this->showSuccess('Data antrian berhasil diperbarui!');
                return redirect()->route('antrian');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Update Antrian Error: ' . $e->getMessage());
                $this->showError('Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('transaksi')->error('ERROR VALIDASI DATA UPDATE', [
                'errors' => json_encode($e->errors(), JSON_PRETTY_PRINT),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            throw $e;
        }
    }

    private function showError($message)
    {
        return $this->alert('error', $message, [
            'showConfirmButton' => false,
            'timer' => 3000,
            'position' => 'center',
            'toast' => true
        ]);
    }

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
        return view('livewire.antrian.edit-antrian');
    }
}
