<?php

namespace App\Livewire\Komposisi;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\Komposisi;
use App\Models\KonversiSatuan;
use App\Models\Pengeluaran;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        if (!$bahan) {
            $this->alert('error', "Bahan tidak ditemukan");
            return 0;
        }

        return $bahan->harga_satuan * $takaran;
    }

    public function addToKomposisi()
    {
        // $this->validate();
        // dd("oke");

        $bahan = Bahan::find($this->selectedBahan);
        $hpp = $this->calculateHppBahan($this->selectedBahan, $this->takaran);

        // Tambahkan debugging
        if ($hpp === 0) {
            Log::info('HPP adalah 0 untuk bahan:', [
                'bahan_id' => $this->selectedBahan,
                'takaran' => $this->takaran,
                'manual_input' => $this->manualInput,
                'harga_satuan_manual' => $this->hargaSatuanManual,
                'satuan_awal_manual' => $this->satuanAwalManual
            ]);

            if (session()->has('error')) {
                return;
            }
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

    public function removeFromKomposisi($index)
    {
        // Hapus item dari array komposisi berdasarkan index
        unset($this->komposisi[$index]);
        // Re-index array
        $this->komposisi = array_values($this->komposisi);
        // Hitung ulang total HPP
        $this->calculateTotalHpp();
        $this->alert('success', 'Bahan berhasil dihapus');
    }

    public function calculateTotalHpp()
    {
        // Hitung total HPP dari semua bahan dalam komposisi
        $this->totalHpp = array_sum(array_column($this->komposisi, 'hpp'));
    }

    public function editKomposisi($produkId)
    {
        $this->isEdit = true;
        $this->selectedProduk = $produkId;

        // Ambil data komposisi yang ada
        $existingKomposisi = Komposisi::with('bahan')
            ->where('produk_id', $produkId)
            ->get();

        // Reset komposisi array
        $this->komposisi = [];

        // Isi komposisi array dengan data yang ada
        foreach ($existingKomposisi as $item) {
            $hpp = $this->calculateHppBahan($item->bahan_id, $item->takaran);

            $this->komposisi[] = [
                'bahan_id' => $item->bahan_id,
                'nama_bahan' => $item->bahan->nama,
                'takaran' => $item->takaran,
                'satuan' => $item->bahan->satuan,
                'hpp' => $hpp,
                'is_manual' => false // Sesuaikan dengan kebutuhan
            ];
        }

        $this->calculateTotalHpp();
    }

    // Tambahkan method untuk update
    public function save()
    {
        if (empty($this->komposisi)) {
            $this->alert('error', 'Tambahkan bahan terlebih dahulu!');
            return;
        }

        try {
            // Mulai transaksi
            DB::beginTransaction();

            foreach ($this->komposisi as $item) {
                Komposisi::create([
                    'produk_id' => $this->selectedProduk,
                    'bahan_id' => $item['bahan_id'],
                    'takaran' => $item['takaran'],
                ]);
            }

            // Update HPP produk
            $produk = Produk::find($this->selectedProduk);
            $produk->hpp = $this->totalHpp;
            $produk->save();

            DB::commit();

            $this->alert('success', 'Komposisi berhasil disimpan');
            $this->resetAll();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update()
    {
        if (empty($this->komposisi)) {
            $this->alert('error', 'Tambahkan bahan terlebih dahulu!');
            return;
        }

        try {
            DB::beginTransaction();

            // Hapus komposisi lama
            Komposisi::where('produk_id', $this->selectedProduk)->delete();

            // Simpan komposisi baru
            foreach ($this->komposisi as $item) {
                Komposisi::create([
                    'produk_id' => $this->selectedProduk,
                    'bahan_id' => $item['bahan_id'],
                    'takaran' => $item['takaran']
                ]);
            }

            // Update HPP produk
            $produk = Produk::find($this->selectedProduk);
            $produk->hpp = $this->totalHpp;
            $produk->save();

            DB::commit();

            $this->alert('success', 'Komposisi berhasil diperbarui');
            $this->resetAll();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function resetAll()
    {
        $this->isEdit = false;
        $this->komposisi = [];
        $this->totalHpp = 0;
        $this->selectedProduk = null;
        $this->resetForm();
        $this->mount();
    }



    public function render()
    {
        return view('livewire.komposisi.komposisi-page');
    }
}
