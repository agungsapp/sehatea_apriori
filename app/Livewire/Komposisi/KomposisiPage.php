<?php

namespace App\Livewire\Komposisi;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\Komposisi;
use App\Models\KonversiSatuan;
use App\Models\Pengeluaran;
use App\Models\Produk;
use Livewire\Component;

class KomposisiPage extends Component
{
    use LivewireAlertHelpers;

    public string $context = 'komposisi';

    // form inputs
    public $selectedProduk;
    public $selectedBahan;
    public $takaran;
    public $satuan;

    // input manual properties
    public $manualInput = false;
    public $hargaSatuanManual;
    public $satuanAwalManual;

    // helper properties
    public $isEdit = false;
    public $komposisi = [];
    public $totalHpp = 0;

    // master data
    public $produks;
    public $bahans;

    protected $rules = [
        'selectedProduk' => 'required',
        'selectedBahan' => 'required',
        'takaran' => 'required|numeric|min:0',
        'hargaSatuanManual' => 'required_if:manualInput,true|numeric|min:0',
        'satuanAwalManual' => 'required_if:manualInput,true',
    ];

    protected $messages = [
        'hargaSatuanManual.required_if' => 'Harga satuan harus diisi ketika menggunakan input manual',
        'satuanAwalManual.required_if' => 'Satuan awal harus diisi ketika menggunakan input manual',
    ];

    public function mount()
    {
        $this->produks = Produk::with('komposisi')->orderBy('nama', 'asc')->get();
        $this->bahans = Bahan::orderBy('nama', 'asc')->get();
    }

    public function updatedSelectedBahan($value)
    {
        if ($value) {
            $bahan = Bahan::find($value);
            $this->satuan = $bahan->satuan;

            // Cek apakah ada data pembelian
            $lastPurchase = Pengeluaran::where('bahan_id', $value)
                ->orderBy('tanggal', 'desc')
                ->first();

            if (!$lastPurchase) {
                $this->manualInput = true;
                $this->alert('info', 'Data pembelian tidak ditemukan. Silahkan input harga satuan manual.');
            } else {
                $this->manualInput = false;
                $this->hargaSatuanManual = null;
                $this->satuanAwalManual = null;
            }
        }
    }

    public function calculateHppBahan($bahanId, $takaran)
    {
        $bahan = Bahan::find($bahanId);
        $hargaSatuan = 0;
        $satuanAwal = '';

        // Tentukan sumber harga dan satuan
        if ($this->manualInput) {
            $hargaSatuan = $this->hargaSatuanManual;
            $satuanAwal = $this->satuanAwalManual;
        } else {
            $lastPurchase = Pengeluaran::where('bahan_id', $bahanId)
                ->orderBy('tanggal', 'desc')
                ->first();

            if (!$lastPurchase) {
                $this->showError('Data pembelian tidak ditemukan');
                return 0;
            }

            $hargaSatuan = $lastPurchase->harga_satuan;
            $satuanAwal = $lastPurchase->satuan;
        }

        // Jika satuan berbeda, lakukan konversi
        if ($satuanAwal !== $bahan->satuan) {
            $konversi = KonversiSatuan::where('bahan_id', $bahanId)
                ->where('satuan_awal', $satuanAwal)
                ->where('satuan_tujuan', $bahan->satuan)
                ->first();

            if (!$konversi) {
                $this->showError("Tidak ditemukan data konversi untuk {$bahan->nama} dari satuan {$satuanAwal} ke {$bahan->satuan}");
                return 0;
            }

            $hargaSatuan = $hargaSatuan / $konversi->rasio;
        }

        return $hargaSatuan * $takaran;
    }

    public function addToKomposisi()
    {
        $this->validate();

        $bahan = Bahan::find($this->selectedBahan);
        $hpp = $this->calculateHppBahan($this->selectedBahan, $this->takaran);

        // Jika HPP 0 dan ada error, jangan lanjutkan
        if ($hpp === 0 && session()->has('error')) {
            return;
        }

        $this->komposisi[] = [
            'bahan_id' => $this->selectedBahan,
            'nama_bahan' => $bahan->nama,
            'takaran' => $this->takaran,
            'satuan' => $this->satuan,
            'hpp' => $hpp,
            'harga_satuan' => $this->manualInput ? $this->hargaSatuanManual : null,
            'satuan_awal' => $this->manualInput ? $this->satuanAwalManual : null,
            'is_manual' => $this->manualInput
        ];

        $this->calculateTotalHpp();
        $this->resetForm();
        $this->alert('success', 'Bahan berhasil ditambahkan');
    }


    private function resetForm()
    {
        $this->selectedBahan = null;
        $this->takaran = null;
        $this->satuan = null;
    }

    public function save()
    {
        if (empty($this->komposisi)) {
            $this->alert('error', 'Tambahkan bahan terlebih dahulu!');
            return;
        }

        try {
            foreach ($this->komposisi as $item) {
                Komposisi::create([
                    'produk_id' => $this->selectedProduk,
                    'bahan_id' => $item['bahan_id'],
                    'takaran' => $item['takaran']
                ]);
            }

            $this->alert('success', 'Komposisi berhasil disimpan');
            $this->komposisi = [];
            $this->totalHpp = 0;
            $this->selectedProduk = null;
            $this->resetForm();
            $this->mount(); // Refresh data
        } catch (\Exception $e) {
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.komposisi.komposisi-page');
    }
}
