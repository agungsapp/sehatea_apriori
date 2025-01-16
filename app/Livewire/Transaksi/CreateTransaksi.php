<?php

namespace App\Livewire\Transaksi;

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

class CreateTransaksi extends Component
{
    use LivewireAlert;

    public $isInstansi = false;

    public $isTunai = true;
    public $customErrors = [];
    public $baseTotal, $oriTotal, $lastPrice;

    public $selectedBank;

    public
        $noInvoice,
        $tanggalMasuk,
        $tanggalKeluar,
        $selectedInstansi,
        $namaPelanggan,
        $kendaraan,
        $nopol,
        $telepon,

        $totalSparepart = 0,
        $totalJasa = 0,
        $totalHarga = 0,
        $ppn,
        $pph,
        $diskon,

        // $selectedInstansi,
        $bayar,
        $statusPembayaran = 'belum lunas',
        $jatuhTempo,
        $namaBarang;

    // cart form
    public
        $jasa,
        $hargaJasa,

        $selectedBarang,
        $selectedJasa,
        $hargaSatuan,
        $jumlah,
        $subtotal,
        $catatan;

    public $keranjangBarang = [];
    public $keranjangJasa = [];

    public $isEdit = false;
    public $isLunas = false;

    public $instansis, $barangs, $banks, $jasas;

    public $requireServiceFields;

    // public $isBaru = true;
    public $antrianId = null;

    public function mount($id = null)
    {
        $this->noInvoice = Penjualan::generateKodeInvoice();
        $this->instansis = Instansi::orderBy('nama_instansi', 'asc')->get();
        $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
        $this->jasas = Jasa::orderBy('nama', 'asc')->get();
        $this->tanggalKeluar = Carbon::now()->format('Y-m-d\TH:i');
        $this->banks = Bank::all();
        // dd($this->diskon);
        // Jika ada ID antrian, load data dari antrian
        if ($id) {
            $this->antrianId = $id;
            $antrian = Antrian::with(['pelangganKendaraan.pelanggan', 'pelangganKendaraan.kendaraan'])
                ->findOrFail($id);

            $this->loadDataFromAntrian($antrian);
        }
    }


    protected function loadDataFromAntrian($antrian)
    {
        $pelanggan = $antrian->pelangganKendaraan->pelanggan;

        // Set data dari antrian
        $this->tanggalMasuk = Carbon::parse($antrian->tanggal_masuk)->format('Y-m-d\TH:i');
        $this->isInstansi = $pelanggan->tipe === 'instansi';
        $this->selectedInstansi = $pelanggan->id_instansi;
        $this->namaPelanggan = $pelanggan->nama;
        $this->kendaraan = $antrian->pelangganKendaraan->kendaraan->nama_kendaraan;
        $this->nopol = $antrian->pelangganKendaraan->kendaraan->nopol;
        $this->telepon = $pelanggan->telepon;

        // Tambahan data yang mungkin diperlukan
        if ($antrian->catatan) {
            $this->catatan = $antrian->catatan;
        }
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
    public function updatedBayar($value)
    {
        if ($this->totalHarga == $value) {
            $this->statusPembayaran = 'lunas';
            $this->isLunas = true;
        } elseif ($this->totalHarga >= $value) {
            $this->statusPembayaran = 'belum lunas';
            $this->isLunas = false;
        } else {
            $this->alert('error', 'Jumlah yang di bayarkan melebihi total harga yang harus di bayar !.', [
                'showConfirmButton' => false,
                'timer' => 2000,
                'position' => 'center',
                'toast' => false
            ]);
        }
    }
    public function updatedJumlah()
    {
        // Hitung subtotal otomatis
        if ($this->jumlah && $this->hargaSatuan) {
            $this->subtotal = $this->jumlah * $this->hargaSatuan;
        }
    }

    public function updatedHargaSatuan()
    {
        // Hitung subtotal otomatis
        if ($this->jumlah && $this->hargaSatuan) {
            $this->subtotal = $this->jumlah * $this->hargaSatuan;
        }

        if ($this->selectedBarang != null) {
            $barang = Barang::select('id', 'nama_barang')
                ->with(['barangMasukDetail' => function ($q) {
                    $q->select(['id', 'id_barang', 'harga_satuan'])
                        ->orderBy('created_at', 'desc')
                        ->take(1);
                }])
                ->find($this->selectedBarang);
            // dd($barang->barangMasukDetail->first());
            $this->lastPrice = $barang->barangMasukDetail->first() ?? null;
        }
    }

    public function tambahBarang()
    {


        // Validasi standar
        $this->validate([
            'selectedBarang' => 'required|exists:barangs,id',
            'jumlah' => 'required|numeric|min:1',
            'hargaSatuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ], [
            'selectedBarang.required' => 'barang harus di pilih !',
            'jumlah.required' => 'Qty barang harus di isi !',
            'hargaSatuan.required' => 'Qty tidak boleh kosong !',
            'subtotal.required' => 'Subtotal tidak boleh kosong.',
        ]);
        // Cari barang yang dipilih
        $barang = Barang::find($this->selectedBarang);
        // Tambahkan ke keranjang
        $this->keranjangBarang[] = [
            'id_barang' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'jumlah' => $this->jumlah,
            'harga_satuan' => $this->hargaSatuan,
            'subtotal' => $this->subtotal
        ];

        // Update total harga
        $this->totalSparepart = array_sum(array_column($this->keranjangBarang, 'subtotal'));

        $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
        // Reset input
        $this->reset(['selectedBarang', 'jumlah', 'hargaSatuan', 'subtotal']);
        $this->updateTotalHarga();
        $this->dispatch('reset-choices');
    }


    public function tambahJasa()
    {
        try {
            // Validasi standar
            $this->validate([
                'selectedJasa' => 'nullable|exists:jasas,id',
                'hargaJasa' => 'required|numeric|min:0',
            ], [
                // 'selectedJasa.required' => 'jasa harus di isi !',
                'hargaJasa.required' => 'harga jasa tidak boleh kosong !',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors());
            // $this->alert('error', 'Terjadi kesalahan error validasi ' . $e->errors());
        }
        // dd("oke");

        $jasa = Jasa::findOrFail($this->selectedJasa);

        // Tambahkan ke keranjang
        $this->keranjangJasa[] = [
            'id_jasa' => $jasa->id,
            'nama' => $jasa->nama,
            'harga_jasa' => $this->hargaJasa,
        ];

        // Update total harga
        $this->totalJasa = array_sum(array_column($this->keranjangJasa, 'harga_jasa'));
        // Reset input
        $this->reset(['jasa', 'hargaJasa']);
        $this->updateTotalHarga();
    }








    public function hapusBarangDariKeranjang($index)
    {
        // Hapus barang dari keranjang
        unset($this->keranjangBarang[$index]);

        // Re-index array
        $this->keranjangBarang = array_values($this->keranjangBarang);

        // Perbarui total harga
        $this->totalSparepart = array_sum(array_column($this->keranjangBarang, 'subtotal'));
        $this->updateTotalHarga();
    }
    public function hapusJasaDariKeranjang($index)
    {
        // Hapus barang dari keranjang
        unset($this->keranjangJasa[$index]);

        // Re-index array
        $this->keranjangJasa = array_values($this->keranjangJasa);

        // Perbarui total harga
        $this->totalJasa = array_sum(array_column($this->keranjangJasa, 'harga_jasa'));
        $this->updateTotalHarga();
    }

    public function updatedDiskon()
    {
        $this->calculateFinalTotal();
    }

    public function updatedPpn()
    {
        $this->calculateFinalTotal();
    }

    public function updatedPph()
    {
        $this->calculateFinalTotal();
    }

    public function updateTotalHarga()
    {
        // Only calculate base total from items and services
        $this->totalHarga = $this->totalSparepart + $this->totalJasa;
        $this->calculateFinalTotal();
    }

    private function calculateFinalTotal()
    {
        $baseTotal = $this->totalSparepart + $this->totalJasa;

        $ppn = 0;
        $pph = 0;
        // Apply discount
        if ($this->diskon > 0) {
            $baseTotal -= $this->diskon;
        }

        // Apply PPN
        if ($this->ppn > 0) {
            $ppn = ($baseTotal * $this->ppn) / 100;
        }

        // Apply PPH
        if ($this->pph > 0) {
            $pph = ($baseTotal * $this->pph) / 100;
        }
        $baseTotal += $ppn + $pph;

        $this->totalHarga = $baseTotal;
    }


    #[On('update-selected-instansi')]
    public function updateSelectedInstansi($value)
    {
        $this->selectedInstansi = $value;
    }
    #[On('update-selected-jasa')]
    public function updateSelectedJasa($value)
    {
        $this->selectedJasa = $value;
        // dd($this->selectedJasa);
    }

    #[On('update-selected-barang')]
    public function updateSelectedBarang($value)
    {
        $this->selectedBarang = $value;
        if ($this->selectedBarang != null && $this->hargaSatuan) {
            $barang = Barang::select('id', 'nama_barang')
                ->with(['barangMasukDetail' => function ($q) {
                    $q->select(['id', 'id_barang', 'harga_satuan'])
                        ->orderBy('created_at', 'desc')
                        ->take(1);
                }])
                ->find($this->selectedBarang);
            // dd($barang->barangMasukDetail->first());
            $this->lastPrice = $barang->barangMasukDetail->first() ?? null;
        }
    }






    public function updatedKeranjangJasa()
    {
        $this->validateServiceFields();
    }

    public function updatedKeranjangBarang()
    {
        $this->validateServiceFields();
    }

    private function validateServiceFields()
    {
        // Jika ada jasa dalam keranjang, maka field service wajib diisi
        $this->requireServiceFields = !empty($this->keranjangJasa);
    }




    public function simpan()
    {
        try {
            // Base validation rules yang selalu divalidasi
            $baseRules = [
                'selectedBank' => !$this->isTunai ? 'required|exists:banks,id' : 'nullable',
                'selectedInstansi' => $this->isInstansi ? 'required|exists:instansis,id' : 'nullable',
                'namaPelanggan' => !$this->isInstansi ? 'required|string|min:3' : 'nullable',
                'totalHarga' => 'required|numeric|min:1',
                'bayar' => 'nullable|numeric|min:0',
                'statusPembayaran' => 'required|in:lunas,belum lunas',
            ];

            // Service-related validation rules yang hanya divalidasi jika ada jasa
            $serviceRules = [
                'tanggalMasuk' => 'required|date',
                'tanggalKeluar' => [
                    'required',
                    'date',
                    'after_or_equal:tanggalMasuk'
                ],
                'kendaraan' => 'required|string',
                'nopol' => 'required|string',
                'telepon' => 'required|string',
            ];

            // Jika ada jasa dalam keranjang, gabungkan rules
            $validationRules = !empty($this->keranjangJasa)
                ? array_merge($baseRules, $serviceRules)
                : $baseRules;

            // Validasi dengan rules yang sesuai
            try {
                $this->validate($validationRules);
            } catch (\Illuminate\Validation\ValidationException $e) {
                dd($e->errors());
                // $this->alert('error', 'Terjadi kesalahan error validasi ' . $e->errors());
            }

            if (!$this->isTunai && $this->statusPembayaran == 'belum lunas' &&  ($this->bayar == 0 || $this->bayar == null)) {
                $this->customErrors['bayar'] = 'Harap masukan jumlah di bayar yang valid untuk metode transfer !';
                $this->alert(
                    'error',
                    'Harap masukan jumlah di bayar yang valid untuk metode transfer !',
                    [
                        'showConfirmButton' => false,
                        'timer' => 2000,
                        'position' => 'center',
                        'toast' => false
                    ]
                );
                return;
            }

            // Validasi keranjang - minimal salah satu harus terisi
            if (empty($this->keranjangBarang) && empty($this->keranjangJasa)) {
                return $this->showError('Minimal harus ada barang atau jasa dalam transaksi!');
            }

            // Validasi stok jika ada barang
            if (!empty($this->keranjangBarang)) {
                foreach ($this->keranjangBarang as $item) {
                    $stok = Stok::where('id_barang', $item['id_barang'])
                        ->where('id_cabang', Session::get('selected_cabang') ?? 1)
                        ->first();

                    if (!$stok || $stok->stok < $item['jumlah']) {
                        return $this->showError(
                            "Stok tidak mencukupi untuk barang {$item['nama_barang']}! " .
                                "Tersedia: " . ($stok ? $stok->stok : 0) .
                                ", Dibutuhkan: {$item['jumlah']}"
                        );
                    }
                }
            }

            // Validasi pembayaran
            if ($this->statusPembayaran === 'lunas') {
                if ($this->bayar < $this->totalHarga) {
                    return $this->showError('Pembayaran harus sesuai total harga untuk status lunas!');
                }
            } else {
                if ($this->bayar >= $this->totalHarga) {
                    return $this->showError('Gunakan status lunas jika sudah membayar penuh!');
                }
            }

            if ($this->selectedBank == null && !$this->isTunai) {
                return $this->alert('error', 'Nomor rekening harus di pilih untuk metode transfer !');
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

                if (!empty($this->keranjangJasa)) {
                    $kendaraan = new Kendaraan();
                    $kendaraan->nama_kendaraan = $this->kendaraan;
                    $kendaraan->nopol = $this->nopol;
                    $kendaraan->save();
                }


                $pelangganKendaraan = new PelangganKendaraan();
                $pelangganKendaraan->id_pelanggan = $pelanggan->id;
                $pelangganKendaraan->id_kendaraan = $kendaraan->id;
                $pelangganKendaraan->save();

                // Buat record Penjualan
                $penjualan = new Penjualan();
                $penjualan->no_invoice = $this->noInvoice;
                $penjualan->id_cabang = Session::get('selected_cabang') ?? 1;
                $penjualan->id_user = Auth::id() ?? 1;
                $penjualan->id_pelanggan_kendaraan = $pelangganKendaraan->id;

                // if ($this->isInstansi) {
                //     $penjualan->id_instansi = $this->selectedInstansi;
                //     $penjualan->nama_pelanggan = null;
                // } else {
                //     $penjualan->id_instansi = null;
                //     $penjualan->nama_pelanggan = $this->namaPelanggan;
                // }

                // Hanya set field service jika ada jasa
                if (!empty($this->keranjangJasa)) {
                    $penjualan->tanggal_masuk = $this->tanggalMasuk;
                }
                $penjualan->tanggal_keluar = $this->tanggalKeluar;

                $penjualan->total_harga = $this->totalHarga;
                $penjualan->total_bayar = $this->bayar ?? 0;
                $penjualan->sisa_tagihan = $this->totalHarga - ($this->bayar ?? 0);
                $penjualan->status_pembayaran = $this->statusPembayaran;
                $penjualan->catatan = $this->catatan;
                $penjualan->save();

                // Simpan detail barang jika ada
                if (!empty($this->keranjangBarang)) {
                    foreach ($this->keranjangBarang as $item) {
                        PenjualanDetail::create([
                            'id_penjualan' => $penjualan->id,
                            'id_barang' => $item['id_barang'],
                            'jumlah' => $item['jumlah'],
                            'harga_satuan' => $item['harga_satuan'],
                            'subtotal' => $item['subtotal']
                        ]);

                        Stok::where('id_barang', $item['id_barang'])
                            ->where('id_cabang', Session::get('selected_cabang') ?? 1)
                            ->decrement('stok', $item['jumlah']);
                    }
                }

                // dd($this->keranjangJasa);
                // Simpan detail jasa jika ada
                if (!empty($this->keranjangJasa)) {
                    foreach ($this->keranjangJasa as $jasa) {
                        PenjualanJasa::create([
                            'id_penjualan' => $penjualan->id,
                            'id_jasa' => $jasa['id_jasa'],
                            'harga_jasa' => $jasa['harga_jasa'],
                        ]);
                    }
                }

                // Catat pembayaran
                $pembayaran = new PembayaranPenjualan();
                $pembayaran->id_penjualan = $penjualan->id;
                $pembayaran->periode = 1;
                $pembayaran->tanggal_pembayaran = now();
                $pembayaran->jumlah_dibayar = $this->bayar ?? 0;
                $pembayaran->sisa_hutang = $this->totalHarga - ($this->bayar ?? 0);
                $pembayaran->metode_pembayaran = $this->isTunai ? 'tunai' : 'transfer';
                if (!$this->isTunai) {
                    $pembayaran->id_bank = $this->selectedBank;
                }
                $pembayaran->keterangan = 'pembayaran transaksi periode pertama';
                $pembayaran->save();


                if ($this->antrianId != null) {
                    $antrian = Antrian::findOrfail($this->antrianId);
                    $antrian->status = 'selesai';
                    $antrian->save();
                }


                DB::commit();

                // Reset form
                $this->reset([
                    'keranjangBarang',
                    'keranjangJasa',
                    'totalHarga',
                    'totalSparepart',
                    'totalJasa',
                    'tanggalMasuk',
                    'tanggalKeluar',
                    'bayar',
                    'selectedInstansi',
                    'selectedBank',
                    'namaPelanggan',
                    'kendaraan',
                    'nopol',
                    'telepon',
                    'catatan',
                    'selectedInstansi'
                ]);

                $this->dispatch('reset-choices');
                $this->noInvoice = Penjualan::generateKodeInvoice();
                $this->customErrors = [];

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
        return view('livewire.transaksi.create-transaksi');
    }
}
