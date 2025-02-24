<?php

namespace App\Livewire\Komposisi;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\Komposisi;
use App\Models\KonversiSatuan;
use App\Models\Pengeluaran;
use App\Models\Produk;
use App\Models\Satuan;
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
    public $hargaTakaran;
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
    public $satuans;

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
        $this->produks = Produk::select('id', 'nama', 'hpp')->with('komposisi')->orderBy('nama', 'asc')->get();
        $this->bahans = Bahan::orderBy('nama', 'asc')->get();
        $this->satuans = Satuan::select('nama')->get()->pluck('nama')->toArray();
        // dd($this->satuans);
    }

    public function updatedSelectedBahan($value)
    {
        if ($value) {
            $bahan = Bahan::find($value);

            // Set satuan default tapi tetap bisa diubah
            if (!$this->satuan) {
                $this->satuan = $bahan->satuan;
            }

            // Cari harga satuan
            $hargaSatuan = $this->fetchAndUpdateHargaSatuan($value);

            if ($hargaSatuan === null) {
                // Jika tidak ditemukan harga satuan, aktifkan mode manual input
                $this->manualInput = true;
                $this->alert('info', 'Data pembelian tidak ditemukan. Silahkan input harga satuan manual.');
            } else {
                // Jika ditemukan harga satuan, gunakan mode otomatis
                $this->manualInput = false;
                $this->hargaSatuanManual = null;
                $this->satuanAwalManual = null;

                // Set satuan awal sesuai dengan satuan bahan
                $this->satuanAwalManual = $bahan->satuan;
            }
        }
    }



    protected function fetchAndUpdateHargaSatuan($bahanId)
    {
        $bahan = Bahan::find($bahanId);
        if (!$bahan) {
            $this->alert('error', "Bahan dengan ID {$bahanId} tidak ditemukan.");
            return null;
        }

        // Cek apakah harga satuan sudah ada di tabel bahans
        if ($bahan->harga_satuan) {
            return $bahan->harga_satuan;
        }

        // Cari harga satuan terakhir dari pengeluarans
        $lastPurchase = Pengeluaran::where('bahan_id', $bahanId)
            ->orderBy('tanggal', 'desc')
            ->first();

        if ($lastPurchase) {
            // Update harga satuan di tabel bahans
            $bahan->harga_satuan = $lastPurchase->harga_satuan;
            $bahan->save();
            return $lastPurchase->harga_satuan;
        }

        // Tidak ditemukan harga satuan
        return null;
    }

    public function calculateHppBahan($bahanId, $takaran)
    {
        $bahan = Bahan::find($bahanId);
        if (!$bahan) {
            $this->alert('error', "Bahan tidak ditemukan");
            return 0;
        }

        // Ambil semua data konversi untuk bahan ini
        $konversiData = KonversiSatuan::where('bahan_id', $bahanId)->get()->keyBy(function ($item) {
            return "{$item->satuan_awal}_{$item->satuan_tujuan}";
        });

        // Jika input manual, gunakan harga manual
        if ($this->manualInput) {
            if ($this->satuanAwalManual !== $this->satuan) {
                $ratio = $this->findConversionRatio($konversiData, $this->satuanAwalManual, $this->satuan);
                if ($ratio === null) {
                    $this->alert('error', "Konversi satuan tidak ditemukan untuk {$this->satuanAwalManual} ke {$this->satuan}");
                    return 0;
                }
                Log::info('Rasio Konversi:', [
                    'satuan_awal' => $this->satuanAwalManual,
                    'satuan_tujuan' => $this->satuan,
                    'ratio' => $ratio,
                ]);

                // Hitung harga per satuan dasar
                $hargaPerSatuanDasar = $this->hargaSatuanManual / $ratio;

                // Set harga takaran
                $this->hargaTakaran = $hargaPerSatuanDasar;

                // Hitung harga untuk takaran tertentu
                $hargaAkhir = $hargaPerSatuanDasar * $takaran;

                Log::info('Harga Akhir:', [
                    'harga_per_satuan_dasar' => $hargaPerSatuanDasar,
                    'takaran' => $takaran,
                    'harga_akhir' => $hargaAkhir,
                ]);

                return $hargaAkhir;
            }
            return $this->hargaSatuanManual * $takaran;
        }

        // Untuk input otomatis (dari data pembelian)
        if ($bahan->satuan !== $this->satuan) {
            $ratio = $this->findConversionRatio($konversiData, $bahan->satuan, $this->satuan);
            if ($ratio === null) {
                $this->alert('error', "Konversi satuan tidak ditemukan untuk {$bahan->satuan} ke {$this->satuan}");
                return 0;
            }

            // Hitung harga per satuan dasar
            $hargaPerSatuanDasar = $bahan->harga_satuan / $ratio;

            // Set harga takaran
            $this->hargaTakaran = $hargaPerSatuanDasar;

            // Hitung harga untuk takaran tertentu
            $hargaAkhir = $hargaPerSatuanDasar * $takaran;

            return $hargaAkhir;
        }

        return $bahan->harga_satuan * $takaran;
    }
    // Fungsi rekursif untuk mencari konversi berlapis
    protected function findConversionRatio($konversiData, $satuanAwal, $satuanTujuan, $visited = [])
    {
        // Cek apakah sudah pernah dikunjungi untuk menghindari loop tak terbatas
        if (in_array("{$satuanAwal}_{$satuanTujuan}", $visited)) {
            return null;
        }
        $visited[] = "{$satuanAwal}_{$satuanTujuan}";

        // Cek konversi langsung
        $key = "{$satuanAwal}_{$satuanTujuan}";
        if (isset($konversiData[$key])) {
            return $konversiData[$key]->rasio;
        }

        // Cari konversi melalui satuan perantara
        foreach ($konversiData as $konversi) {
            if ($konversi->satuan_awal === $satuanAwal) {
                $intermediateRatio = $this->findConversionRatio($konversiData, $konversi->satuan_tujuan, $satuanTujuan, $visited);
                if ($intermediateRatio !== null) {
                    return $konversi->rasio * $intermediateRatio;
                }
            }
        }

        // Tidak ditemukan konversi
        return null;
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
            'harga_takaran' => $this->hargaTakaran ?? 0,
            'harga_satuan' => $this->manualInput ? $this->hargaSatuanManual : null,
            'satuan_awal' => $this->manualInput ? $this->satuanAwalManual : null,
            'is_manual' => $this->manualInput
        ];

        Log::info($this->komposisi);

        $this->calculateTotalHpp();
        $this->resetForm();
        $this->alert('success', 'Bahan berhasil ditambahkan');
    }


    private function resetForm()
    {
        $this->selectedBahan = null;
        $this->takaran = null;
        $this->satuan = null;
        $this->reset('hargaTakaran');
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
            DB::beginTransaction();

            foreach ($this->komposisi as $item) {
                Komposisi::create([
                    'produk_id' => $this->selectedProduk,
                    'bahan_id' => $item['bahan_id'],
                    'takaran' => $item['takaran']
                ]);

                // Simpan harga satuan manual ke tabel bahans jika mode manual input
                if ($item['is_manual']) {
                    $bahan = Bahan::find($item['bahan_id']);
                    $bahan->harga_satuan = $item['harga_takaran'];
                    $bahan->save();
                }
            }

            // Update HPP produk
            try {
                $produk = Produk::find($this->selectedProduk);
                $produk->hpp += $this->totalHpp;
                $produk->save();
                Log::info("update hpp produk berhasil!");
            } catch (\Throwable $th) {
                Log::info("update hpp produk gagal!");
                //throw $th;
            }

            DB::commit();

            $this->alert('success', 'Komposisi berhasil disimpan');
            $this->dispatch('update-card');
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
