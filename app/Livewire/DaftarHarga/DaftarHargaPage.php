<?php

namespace App\Livewire\DaftarHarga;

use App\LivewireAlertHelpers;
use App\Models\Barang;
use App\Models\DaftarHarga;
use App\Models\Brand;
use App\Models\Cabang;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\Stok;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class DaftarHargaPage extends Component
{
    use LivewireAlertHelpers;

    public $kategoris, $merks, $brands;
    public $daftarHargaId, $kodeBarang, $namaBarang, $selectedKategori, $hargaBeli, $hargaJual, $catatan;
    public $isEdit = false;

    public $daftarHargas;

    public string $tableName = 'daftar-harga-table-fqnp7w-table';

    protected function rules()
    {
        return [
            'kodeBarang' => 'required|string|max:255',
            'namaBarang' => 'required|string|max:500',
            'selectedKategori' => 'nullable|exists:kategoris,id',
            'hargaBeli' => 'required|numeric',
            'hargaJual' => 'required|numeric',
        ];
    }

    protected function messages()
    {
        return [
            'kodeBarang.required' => 'Kode Barang wajib diisi.',
            'kodeBarang.string' => 'Kode Barang harus berupa teks.',
            'kodeBarang.max' => 'Kode Barang maksimal 255 karakter.',
            'namaBarang.required' => 'Nama Barang wajib diisi.',
            'namaBarang.string' => 'Nama Barang harus berupa teks.',
            'namaBarang.max' => 'Nama Barang maksimal 500 karakter.',
            'selectedKategori.exists' => 'Kategori yang dipilih tidak valid.',
            'hargaBeli.required' => 'Harga Beli wajib diisi.',
            'hargaBeli.numeric' => 'Harga Beli harus berupa angka.',
            'hargaJual.required' => 'Harga Jual wajib diisi.',
            'hargaJual.numeric' => 'Harga Jual harus berupa angka.',
        ];
    }

    #[On('delete-data')]
    public function deleteDaftarHarga($id)
    {
        $this->daftarHargaId = $id;
        $daftarHarga = DaftarHarga::findOrFail($id);
        $this->alert('warning', "Apakah anda yakin ingin menghapus daftar harga <strong>$daftarHarga->nama</strong> ?", [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'deleteDaftarHargaConfirmed',
        ]);
    }

    #[On('deleteDaftarHargaConfirmed')]
    public function deleteDaftarHargaConfirmed()
    {
        try {
            DaftarHarga::destroy($this->daftarHargaId);
            $this->alert('success', 'Berhasil menghapus data daftar harga!');
            $this->daftarHargaId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            $this->alert('error', 'Terjadi kesalahan pada server!');
        }
    }

    #[On('edit-data')]
    public function loadDaftarHargaForEdit($id)
    {
        $daftarHarga = DaftarHarga::findOrFail($id);

        $this->isEdit = true;
        $this->daftarHargaId = $daftarHarga->id;
        $this->kodeBarang = $daftarHarga->kode_barang;
        $this->namaBarang = $daftarHarga->nama_barang;
        $this->selectedKategori = $daftarHarga->id_kategori;
        $this->hargaBeli = $daftarHarga->harga_beli;
        $this->hargaJual = $daftarHarga->harga_jual;
        $this->catatan = $daftarHarga->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->daftarHargaId) {
            $daftarHarga = DaftarHarga::findOrFail($this->daftarHargaId);
            $daftarHarga->update([
                'kode_barang' => $this->kodeBarang,
                'nama_barang' => $this->namaBarang,
                'id_kategori' => $this->selectedKategori,
                'catatan' => $this->catatan,
                'harga_beli' => $this->hargaBeli,
                'harga_jual' => $this->hargaJual,
            ]);
        } else {
            try {
                $daftarHarga = DaftarHarga::create([
                    'kode_barang' => $this->kodeBarang,
                    'nama_barang' => $this->namaBarang,
                    'id_kategori' => $this->selectedKategori,
                    'catatan' => $this->catatan,
                    'harga_beli' => $this->hargaBeli,
                    'harga_jual' => $this->hargaJual,
                ]);
            } catch (\Throwable $th) {
                $this->alert('error', 'Terjadi kesalahan saat menyimpan daftar harga! ' . $th->getMessage(), [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                ]);
            }
        }

        $this->alert('success', 'Berhasil menyimpan data daftar harga!');
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetForm();
        $this->loadData();
    }

    public function mount()
    {
        $this->kategoris = Kategori::orderBy('nama', 'asc')->get();
        $this->merks = Merk::orderBy('nama', 'asc')->get();
        $this->brands = Brand::orderBy('nama', 'asc')->get();
        $this->loadData();
    }

    public function loadData()
    {
        $this->daftarHargas = DaftarHarga::orderBy('nama_barang', 'asc')->get();
    }

    private function resetForm()
    {
        $this->reset(['kodeBarang', 'namaBarang', 'selectedKategori', 'catatan', 'hargaBeli', 'hargaJual', 'daftarHargaId']);
        $this->isEdit = false;
    }

    public function handleSync()
    {
        $this->showCon("anda yakin akan melakukan sinkronisasi data dari data barang ?", 'syncConfirmed');
    }

    #[On('syncConfirmed')]
    public function syncConfirmed()
    {
        $dataBarangs = Barang::all();

        $i = 1;

        foreach ($dataBarangs as $db) {

            $findId = DaftarHarga::where('id_barang', $db->id)->first();
            if ($findId) {
                continue;
            }

            DaftarHarga::create(
                [
                    'kode_barang' => $db->kode_barang,
                    'nama_barang' => $db->nama_barang,
                    'id_barang' => $db->id,
                ]
            );
            $i++;
        }

        $this->loadData();
        $this->showSuccess('Sebanyak ' . $i . ' data barang berhasil disinkronisasi');
    }

    public function batalEdit()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.daftar-harga.daftar-harga-page');
    }
}
