<?php

namespace App\Livewire\BarangMasuk;

use App\Models\Bank;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\PembayaranBarangMasuk;
use App\Models\PembayaranUtang;
use App\Models\Stok;
use App\Models\StokBatch;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportConsoleCommands\Commands\Upgrade\ThirdPartyUpgradeNotice;
use phpDocumentor\Reflection\Types\This;

class CreateBarangMasuk extends Component
{
    use LivewireAlert;

    public $isTunai = true;
    public $customErrors = [];

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

    // pembayaran section
    public $selectedBank;

    public $keranjangBarang = [];

    public $isEdit = false;
    public $isLunas = false;

    // global data for select
    public $suppliers, $barangs, $banks;

    public $isBaru = false;

    // public function updatedIsTunai($value)
    // {
    //     dd($value);
    // }

    public function mount()
    {
        $this->noInvoice = BarangMasuk::generateKodeInvoice();
        $this->suppliers = Supplier::orderBy('nama', 'asc')->get();
        $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
        $this->banks = Bank::orderBy('bank', 'asc')->get();
        $this->tanggal = Carbon::now()->format('Y-m-d\TH:i');
        $this->jatuhTempo = Carbon::now()->addMonth()->format('Y-m-d');
        $this->customErrors = [];
    }


    // public function updatedSelectedBarang($value)
    // {
    //     if ($this->selectedBarang) {
    //         $barang = Barang::find($this->selectedBarang);
    //         $this->hargaSatuan = $barang->harga_beli;
    //     }
    // }

    public function updatedIsBaru($value)
    {
        if ($value) {
            // Jika baru, reset selectedBarang saja
            $this->selectedBarang = null;
        } else {
            // Jika tidak baru, reset namaBarang saja
            $this->namaBarang = null;
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
        $this->customErrors = [];
    }

    // public function updatedIsBaru($value) {
    //     dd($value);
    // }

    public function updatedStatusPembayaran($value)
    {
        if ($value == 'lunas') {
            $this->isLunas = true;
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
    }

    public function tambahBarang()
    {


        // Validasi standar
        $this->validate([
            'namaBarang' => 'nullable',
            'selectedBarang' => 'nullable|exists:barangs,id',
            'jumlah' => 'required|numeric|min:1',
            'hargaSatuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ], [
            'jumlah.required' => 'Qty barang harus di isi !',
            'hargaSatuan.required' => 'Qty tidak boleh kosong !',
            'subtotal.required' => 'Subtotal tidak boleh kosong.',
        ]);

        try {
            if ($this->isBaru) {
                $barang = Barang::create([
                    'nama_barang' => $this->namaBarang,
                ]);
            } else {
                $barang = Barang::find($this->selectedBarang);
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('error', 'Gagal menyimpan barang masuk: ' . $th->getMessage(), [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);
        }


        // Cari barang yang dipilih

        // Tambahkan ke keranjang
        $this->keranjangBarang[] = [
            'id_barang' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'jumlah' => $this->jumlah,
            'harga_satuan' => $this->hargaSatuan,
            'subtotal' => $this->subtotal
        ];

        // Update total harga
        $this->totalHarga = array_sum(array_column($this->keranjangBarang, 'subtotal'));

        $this->barangs = Barang::orderBy('nama_barang', 'asc')->get();
        $this->isBaru = false;
        $this->dispatch('reset-choices');

        // Reset input
        $this->reset(['selectedBarang', 'namaBarang', 'jumlah', 'hargaSatuan', 'subtotal']);
    }

    public function hapusBarangDariKeranjang($index)
    {
        // Hapus barang dari keranjang
        unset($this->keranjangBarang[$index]);

        // Re-index array
        $this->keranjangBarang = array_values($this->keranjangBarang);

        // Perbarui total harga
        $this->totalHarga = array_sum(array_column($this->keranjangBarang, 'subtotal'));
    }

    #[On('update-selected-supplier')]
    public function updateSelectedSupplier($value)
    {
        // dd($value);
        $this->selectedSupplier = $value;
    }

    #[On('update-selected-barang')]
    public function updateSelectedBarang($value)
    {
        $this->selectedBarang = $value;
    }


    public function simpan()
    {
        // dd($this->isTunai);
        // Validasi input utama dengan aturan yang lebih ketat
        try {
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
            ], [
                'statusPembayaran.required' => 'status pembayaran tidak boleh kosong !'
            ]);

            $this->alert('info', 'lolos validasi');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
            // $this->alert('error', 'Terjadi kesalahan error validasi ' . $e->errors());
        }

        if (!$this->isTunai && $this->statusPembayaran == 'belum lunas' && ($this->bayar == 0 || $this->bayar === null)) {
            // dd("masuk validasi bos");
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
            // return $this->customErrors = [];
            return;
        }

        if (!$this->isTunai && $this->selectedBank == null) {
            return $this->alert(
                'error',
                'Mohon pilih bank terlebih dahulu !',
                [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                    'position' => 'center',
                    'toast' => false
                ]
            );
        }

        // dd("oke lolos", $this->isTunai, $this->bayar, $this->statusPembayaran);

        // Pastikan keranjang tidak kosong
        if (empty($this->keranjangBarang)) {
            return $this->alert('error', 'Keranjang barang masuk masih kosong!', [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);
        }

        // Validasi pembayaran berdasarkan status
        if ($this->statusPembayaran === 'lunas') {
            // Untuk status lunas, pastikan bayar sesuai total harga
            if ($this->bayar < $this->totalHarga) {
                return $this->alert('error', 'Pembayaran harus sesuai total harga untuk status lunas!', [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                ]);
            }

            // Set jatuh tempo menjadi null untuk status lunas
            $jatuhTempo = null;
        } else {
            // Untuk status belum lunas
            if (empty($this->jatuhTempo)) {
                return $this->alert('error', 'Harap tentukan jatuh tempo untuk pembayaran belum lunas!', [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                ]);
            }

            // Pastikan bayar kurang dari total harga
            if ($this->bayar >= $this->totalHarga) {
                return $this->alert('error', 'Gunakan status lunas jika sudah membayar penuh!', [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                ]);
            }

            $jatuhTempo = $this->jatuhTempo;
        }

        // dd();
        // dd([
        //     'noInvoice' => $this->noInvoice,
        //     'selectedSupplier' => $this->selectedSupplier,
        //     'selectedCabang' => Session::get('selected_cabang') ?? 1,
        //     'userId' => Auth::id() ?? 1,
        //     'tanggal' => $this->tanggal,
        //     'totalHarga' => $this->totalHarga,
        //     'bayar' => $this->bayar ?? 0,
        //     'statusPembayaran' => $this->statusPembayaran,
        //     'jatuhTempo' => $jatuhTempo
        // ]);


        // dd($this->keranjangBarang);
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Buat record barang masuk
            $barangMasuk = new BarangMasuk();
            $barangMasuk->no_invoice = $this->noInvoice;
            $barangMasuk->id_suplier = $this->selectedSupplier;
            $barangMasuk->id_cabang = Session::get('selected_cabang') ?? 1;
            $barangMasuk->id_user = Auth::id() ?? 1;
            $barangMasuk->tanggal = $this->tanggal;
            $barangMasuk->total_harga = (int)$this->totalHarga;
            $barangMasuk->total_bayar = (int)$this->bayar ?? 0;
            $barangMasuk->sisa_tagihan = (int)$this->totalHarga - (int)($this->bayar ?? 0);
            $barangMasuk->jatuh_tempo = $this->jatuhTempo;
            $barangMasuk->status_pembayaran = $this->statusPembayaran;
            $barangMasuk->catatan = $this->catatan;
            $barangMasuk->save();



            // Simpan detail barang masuk
            foreach ($this->keranjangBarang as $item) {
                // dd($item['id_barang']);
                BarangMasukDetail::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update atau buat stok
                Stok::updateOrCreate(
                    [
                        'id_barang' => $item['id_barang'],
                        'id_cabang' => Session::get('selected_cabang') ?? 1
                    ],
                    [
                        'stok' => DB::raw('stok + ' . $item['jumlah'])
                    ]
                );
            }

            $pembayaranBarang = new PembayaranBarangMasuk();
            $pembayaranBarang->id_barang_masuk = $barangMasuk->id;
            $pembayaranBarang->periode = 1;
            $pembayaranBarang->tanggal_pembayaran = now();
            $pembayaranBarang->jumlah_dibayar = (int)$this->bayar ?? 0;
            $pembayaranBarang->sisa_hutang = (int)$this->totalHarga - (int)($this->bayar ?? 0);
            $pembayaranBarang->metode_pembayaran = $this->isTunai ? 'tunai' : 'transfer';
            $pembayaranBarang->id_bank = $this->selectedBank;
            $pembayaranBarang->keterangan = 'pembayaran periode pertama';
            $pembayaranBarang->save();

            // Commit transaksi
            DB::commit();

            // Reset form dan generate invoice baru
            $this->reset(['keranjangBarang', 'totalHarga', 'bayar', 'selectedSupplier', 'jatuhTempo', 'catatan', 'selectedBarang']);
            $this->noInvoice = BarangMasuk::generateKodeInvoice();
            $this->dispatch('reset-choices');
            $this->alert('success', 'Barang masuk berhasil di simpan !', [
                'showConfirmButton' => false,
                'timer' => 2000,
                'position' => 'center',
                'toast' => false
            ]);

            session()->flash('success', 'Barang masuk berhasil disimpan');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();

            throw $e;
            // Tampilkan pesan error
            $this->alert('error', 'Gagal menyimpan barang masuk: ' . $e->getMessage(), [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);

            // Log error untuk debugging
            Log::error('Barang Masuk Save Error: ' . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.barang-masuk.create-barang-masuk');
    }
}
