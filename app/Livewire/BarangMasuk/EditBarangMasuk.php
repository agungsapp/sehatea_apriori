<?php

namespace App\Livewire\BarangMasuk;

use App\LivewireAlertHelpers;
use App\Models\Bank;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\PembayaranBarangMasuk;
use App\Models\Stok;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class EditBarangMasuk extends Component
{
    use LivewireAlertHelpers;

    public $isTunai = true;
    public $customErrors = [];
    public $barangMasukId;

    public
        $noInvoice,
        $selectedSupplier,
        $tanggal,
        $totalHarga = 0,
        $bayar,
        $statusPembayaran = 'belum lunas',
        $jatuhTempo,
        $catatan,
        $namaBarang;

    public
        $selectedBarang,
        $jumlah,
        $hargaSatuan,
        $subtotal;

    public $selectedBank;
    public $keranjangBarang = [];
    public $isEdit = true;
    public $isLunas = false;
    public $suppliers, $barangs, $banks;
    public $originalDetails = [];
    public $originalStockData = [];
    public $deletedItems = [];
    public $isBaru = false;

    public function mount($id)
    {
        $this->barangMasukId = $id;
        $barangMasuk = BarangMasuk::with('barangMasukDetail.barang')->findOrFail($id);

        // Load main data
        $this->noInvoice = $barangMasuk->no_invoice;
        $this->selectedSupplier = $barangMasuk->id_suplier;
        $this->tanggal = Carbon::parse($barangMasuk->tanggal)->format('Y-m-d\TH:i');
        $this->totalHarga = $barangMasuk->total_harga;
        $this->bayar = $barangMasuk->total_bayar;
        $this->statusPembayaran = $barangMasuk->status_pembayaran;
        $this->jatuhTempo = $barangMasuk->jatuh_tempo ? Carbon::parse($barangMasuk->jatuh_tempo)->format('Y-m-d') : null;
        $this->catatan = $barangMasuk->catatan;

        // Load payment details
        $pembayaran = $barangMasuk->pembayaranBarangMasuk->first();
        if ($pembayaran) {
            $this->isTunai = $pembayaran->metode_pembayaran === 'tunai';
            $this->selectedBank = $pembayaran->id_bank;
        }

        // Load items into keranjangBarang
        foreach ($barangMasuk->barangMasukDetail as $detail) {
            $this->originalStockData[$detail->id_barang] = [
                'jumlah' => $detail->jumlah,
                'deleted' => false
            ];
            $this->keranjangBarang[] = [
                'id_barang' => $detail->id_barang,
                'nama_barang' => $detail->barang->nama_barang,
                'jumlah' => $detail->jumlah,
                'harga_satuan' => $detail->harga_satuan,
                'subtotal' => $detail->subtotal
            ];
        }

        // Load related data for dropdowns
        $this->suppliers = Supplier::orderBy('nama', 'asc')->get();
        $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
        $this->banks = Bank::orderBy('bank', 'asc')->get();


        $this->originalDetails = $barangMasuk->barangMasukDetail->map(function ($detail) {
            return [
                'id_barang' => $detail->id_barang,
                'jumlah' => $detail->jumlah,
                'harga_satuan' => $detail->harga_satuan,
                'subtotal' => $detail->subtotal
            ];
        })->toArray();


        $this->isLunas = $this->statusPembayaran === 'lunas';
        $this->customErrors = [];
    }

    public function updatedIsBaru($value)
    {
        $value ? $this->selectedBarang = null : $this->namaBarang = null;
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
            $this->alert('error', 'Jumlah yang dibayarkan melebihi total harga!');
        }
        $this->customErrors = [];
    }

    public function updatedStatusPembayaran($value)
    {
        $this->isLunas = $value === 'lunas';
    }

    public function updatedJumlah()
    {
        if ($this->jumlah && $this->hargaSatuan) {
            $this->subtotal = $this->jumlah * $this->hargaSatuan;
        }
    }

    public function updatedHargaSatuan()
    {
        if ($this->jumlah && $this->hargaSatuan) {
            $this->subtotal = $this->jumlah * $this->hargaSatuan;
        }
    }

    public function tambahBarang()
    {
        $this->validate([
            'namaBarang' => 'nullable',
            'selectedBarang' => 'nullable|exists:barangs,id',
            'jumlah' => 'required|numeric|min:1',
            'hargaSatuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        try {
            $barang = $this->isBaru
                ? Barang::create(['nama_barang' => $this->namaBarang])
                : Barang::find($this->selectedBarang);

            $this->keranjangBarang[] = [
                'id_barang' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'jumlah' => $this->jumlah,
                'harga_satuan' => $this->hargaSatuan,
                'subtotal' => $this->subtotal
            ];

            $this->totalHarga = array_sum(array_column($this->keranjangBarang, 'subtotal'));
            $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
            $this->reset(['selectedBarang', 'namaBarang', 'jumlah', 'hargaSatuan', 'subtotal']);
            $this->isBaru = false;
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal menambah barang: ' . $e->getMessage());
        }
    }

    public function hapusBarangDariKeranjang($index)
    {
        $itemToDelete = $this->keranjangBarang[$index];

        if (isset($this->originalStockData[$itemToDelete['id_barang']])) {
            $this->originalStockData[$itemToDelete['id_barang']]['deleted'] = true;
        }

        unset($this->keranjangBarang[$index]);
        $this->keranjangBarang = array_values($this->keranjangBarang);
        $this->totalHarga = array_sum(array_column($this->keranjangBarang, 'subtotal'));
    }

    #[On('update-selected-supplier')]
    public function updateSelectedSupplier($value)
    {
        $this->selectedSupplier = $value;
    }

    #[On('update-selected-barang')]
    public function updateSelectedBarang($value)
    {
        $this->selectedBarang = $value;
    }

    public function update()
    {
        $this->validate([
            'selectedSupplier' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'totalHarga' => 'required|numeric|min:1',
            'bayar' => 'nullable|numeric|min:0',
            'statusPembayaran' => 'required|in:lunas,belum lunas',
            'jatuhTempo' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($this->statusPembayaran === 'belum lunas' && empty($value)) {
                        $fail('Jatuh tempo harus diisi untuk status pembayaran belum lunas');
                    }
                    if (!empty($value) && strtotime($value) < strtotime($this->tanggal)) {
                        $fail('Jatuh tempo tidak boleh kurang dari tanggal transaksi');
                    }
                }
            ]
        ]);

        if (!$this->isTunai && $this->statusPembayaran == 'belum lunas' && ($this->bayar == 0 || $this->bayar === null)) {
            $this->customErrors['bayar'] = 'Harap masukan jumlah pembayaran yang valid untuk metode transfer!';
            return;
        }

        if (!$this->isTunai && $this->selectedBank == null) {
            $this->alert('error', 'Mohon pilih bank terlebih dahulu!');
            return;
        }

        if (empty($this->keranjangBarang)) {
            $this->alert('error', 'Keranjang barang masih kosong!');
            return;
        }

        DB::beginTransaction();
        try {
            $barangMasuk = BarangMasuk::findOrFail($this->barangMasukId);
            $oldTotalHarga = $barangMasuk->total_harga;

            // Update main data
            $barangMasuk->update([
                'id_suplier' => $this->selectedSupplier,
                'tanggal' => $this->tanggal,
                'total_harga' => $this->totalHarga,
                'total_bayar' => $this->bayar ?? 0,
                'sisa_tagihan' => $this->totalHarga - ($this->bayar ?? 0),
                'jatuh_tempo' => $this->jatuhTempo,
                'status_pembayaran' => $this->statusPembayaran,
                'catatan' => $this->catatan
            ]);

            // Proses item yang dihapus (pengurangan stok)
            // Handle stock updates
            foreach ($this->originalStockData as $idBarang => $originalData) {
                // Find current item in keranjangBarang
                $currentItem = collect($this->keranjangBarang)
                    ->first(function ($item) use ($idBarang) {
                        return $item['id_barang'] == $idBarang;
                    });

                $stok = Stok::where('id_barang', $idBarang)
                    ->where('id_cabang', Session::get('selected_cabang') ?? 1)
                    ->first();

                if (!$stok) {
                    $stok = Stok::create([
                        'id_barang' => $idBarang,
                        'id_cabang' => Session::get('selected_cabang') ?? 1,
                        'stok' => 0
                    ]);
                }

                if ($originalData['deleted'] && !$currentItem) {
                    // Cek apakah pengurangan stok akan menghasilkan nilai negatif
                    $finalStock = $stok->stok - $originalData['jumlah'];
                    if ($finalStock < 0) {
                        // Ambil nama barang untuk pesan error
                        $barang = Barang::find($idBarang);
                        return $this->showError("Stok {$barang->nama_barang} tidak mencukupi untuk dihapus. Sisa stok: {$stok->stok}");
                    }
                    $stok->decrement('stok', $originalData['jumlah']);
                } elseif ($currentItem) {

                    $difference = $currentItem['jumlah'] - $originalData['jumlah'];
                    if ($difference < 0) {

                        $finalStock = $stok->stok + $difference;
                        if ($finalStock < 0) {
                            $barang = Barang::find($idBarang);
                            return $this->showError("Pengurangan stok {$barang->nama_barang} melebihi stok yang tersedia. Sisa stok: {$stok->stok}");
                        }
                    }
                    if ($difference != 0) {
                        $stok->increment('stok', $difference);
                    }
                }
            }

            // Add new items that weren't in original
            foreach ($this->keranjangBarang as $item) {
                if (!isset($this->originalStockData[$item['id_barang']])) {
                    $stok = Stok::firstOrNew([
                        'id_barang' => $item['id_barang'],
                        'id_cabang' => Session::get('selected_cabang') ?? 1
                    ]);
                    $stok->increment('stok', $item['jumlah']);
                }
            }

            // Update barang masuk details
            $barangMasuk->barangMasukDetail()->delete();
            foreach ($this->keranjangBarang as $item) {
                BarangMasukDetail::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // Update pembayaran jika total harga berubah
            if ($oldTotalHarga != $this->totalHarga) {
                $payments = PembayaranBarangMasuk::where('id_barang_masuk', $barangMasuk->id)
                    ->orderBy('periode', 'asc')
                    ->get();

                $totalBayar = 0;
                foreach ($payments as $payment) {
                    $totalBayar += $payment->jumlah_dibayar;
                    $payment->sisa_hutang = $this->totalHarga - $totalBayar;
                    $payment->save();
                }
            }

            DB::commit();
            $this->alert('success', 'Data barang masuk berhasil diperbarui!');
            return redirect()->route('barang-masuk');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Edit Barang Masuk Error: ' . $e->getMessage());
            $this->alert('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.barang-masuk.edit-barang-masuk');
    }
}
