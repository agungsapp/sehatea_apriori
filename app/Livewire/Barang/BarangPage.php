<?php

namespace App\Livewire\Barang;

use App\LivewireAlertHelpers;
use App\Models\Barang;
use App\Models\Brand;
use App\Models\Cabang;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\Stok;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangPage extends Component
{
    use LivewireAlertHelpers;

    public $kategoris, $merks, $brands;
    public $barangId, $kodeBarang, $noSeri,  $namaBarang, $selectedKategori, $selectedBrand, $catatan;
    public $isEdit = false;



    protected function rules()
    {
        return [
            'kodeBarang' => 'required|string|max:255',
            'noSeri' => 'nullable|numeric|min:11',
            'namaBarang' => 'required|string|max:500',
            'selectedKategori' => 'nullable|exists:kategoris,id',
            // 'selectedMerk' => 'required|exists:merks,id',
            'selectedBrand' => 'nullable|exists:merks,id',
        ];
    }



    protected function messages()
    {
        return [
            'kodeBarang.required' => 'Kode Barang wajib diisi.',
            'kodeBarang.string' => 'Kode Barang harus berupa teks.',
            'kodeBarang.max' => 'Kode Barang maksimal 255 karakter.',
            'noSeri.numeric' => 'Nomor Seri harus berupa angka.',
            'noSeri.min' => 'Nomor Seri minimal 11 angka.',
            'namaBarang.required' => 'Nama Barang wajib diisi.',
            'namaBarang.string' => 'Nama Barang harus berupa teks.',
            'namaBarang.max' => 'Nama Barang maksimal 500 karakter.',
            'selectedKategori.exists' => 'Kategori yang dipilih tidak valid.',
            // 'selectedMerk.required' => 'Merk wajib dipilih.',
            // 'selectedMerk.exists' => 'Merk yang dipilih tidak valid.',
            'selectedBrand.exists' => 'Brand yang dipilih tidak valid.',
        ];
    }


    #[On('delete-barang')]
    public function deleteBarang($id)
    {
        $this->barangId = $id;
        $barang = Barang::findOrFail($id);
        // $this->alert('warning', "Apakah anda yakin ingin menghapus barang <strong>$barang->nama</strong> ?", [
        //     'showConfirmButton' => true,
        //     'confirmButtonText' => 'Ya, Hapus!',
        //     'showCancelButton' => true,
        //     'cancelButtonText' => 'Batal',
        //     'onConfirmed' => 'deleteBarangConfirmed',
        // ]);
        $this->showCon("Apakah anda yakin ingin menghapus barang <strong>$barang->nama</strong> ?", 'deleteBarangConfirmed');
    }
    #[On('deleteBarangConfirmed')]
    public function deleteBarangConfirmed()
    {
        try {
            Barang::destroy($this->barangId);
            $this->alert('success', 'Berhasil menghapus data barang!');
            $this->barangId = null;
            $this->dispatch('pg:eventRefresh-barang-table-lp0z94-table');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'stoks_id_barang_foreign')) {
                // Pesan khusus untuk error karena relasi stok
                $this->showError('Data barang ini tidak dapat dihapus karena masih digunakan di tabel stok.');
            } else {
                // Pesan default untuk error lainnya
                $this->showError('Terjadi kesalahan pada server!');
            }
        } catch (\Throwable $th) {
            $this->showError('Terjadi kesalahan yang tidak terduga.');
        }
    }




    #[On('edit-barang')]
    public function loadUserForEdit($id)
    {
        $barang = Barang::findOrFail($id);

        $this->isEdit = true;
        $this->barangId = $barang->id;
        $this->kodeBarang = $barang->kode_barang;
        $this->noSeri = $barang->no_seri;
        $this->namaBarang = $barang->nama_barang;
        $this->selectedKategori = $barang->id_kategori;
        $this->selectedBrand = $barang->id_brand;
        $this->catatan = $barang->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }



    public function save()
    {

        // dd($this->getDataForValidation($this->validate()));

        $this->validate();
        // dd($this->selectedBrand);

        if ($this->barangId) {
            // dd(true);
            // Update existing barang
            $barang = Barang::findOrFail($this->barangId);
            $barang->update([
                'kode_barang' => $this->kodeBarang,
                'no_seri' => $this->noSeri,
                'nama_barang' => $this->namaBarang,
                'id_kategori' => $this->selectedKategori,
                'catatan' => $this->catatan,
                'id_brand' => $this->selectedBrand,
            ]);
        } else {
            // dd(false);
            // Create new barang
            try {
                $barang = Barang::create([
                    'kode_barang' => $this->kodeBarang,
                    'no_seri' => $this->noSeri,
                    'nama_barang' => $this->namaBarang,
                    'id_kategori' => $this->selectedKategori,
                    'catatan' => $this->catatan,
                    'id_brand' => $this->selectedBrand,
                ]);
            } catch (\Throwable $th) {
                throw $th;
                $this->alert('error', 'Terjadi kesalahan saat menyimpan barang ! ' . $th->getMessage(), [
                    'showConfirmButton' => false,
                    'timer' => 2000,
                ]);
            }


            $cabangs = Cabang::all();

            // dd($barang);

            Stok::create([
                'id_barang' => $barang->id,
                'id_cabang' => Session::get('selected_cabang'),
                'stok' => 0
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data supplier!');
        $this->dispatch('pg:eventRefresh-barang-table-lp0z94-table');
        $this->resetForm();
    }


    public function mount()
    {
        $this->kategoris = Kategori::orderBy('nama', 'asc')->get();
        $this->merks = Merk::orderBy('nama', 'asc')->get();
        $this->brands = Brand::orderBy('nama', 'asc')->get();
    }


    private function resetForm()
    {
        $this->reset(['kodeBarang', 'noSeri', 'namaBarang', 'selectedKategori', 'catatan']);
        $this->isEdit = false;
    }

    public function batalEdit()
    {
        // dd('oke bolo');
        $this->isEdit = false;
        $this->resetForm();
        $this->dispatch('reset-choices');
    }

    public function render()
    {
        return view('livewire.barang.barang-page');
    }
}
