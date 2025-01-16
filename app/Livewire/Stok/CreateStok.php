<?php

namespace App\Livewire\Stok;

use App\LivewireAlertHelpers;
use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateStok extends Component
{

    // use LivewireAlert;
    use LivewireAlertHelpers;

    public $isEdit = false;
    public $read = false;
    public $selectedBarang, $stok;
    public $barangs;
    public $namaBarang = '';

    public string $tableName = 'pg:eventRefresh-stok-table-zuwran-table';


    public function mount()
    {
        $this->loadBarangs();
    }

    public function rules()
    {
        return [
            'stok' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'stok.required' => 'stok tidak boleh kosong !'
        ];
    }

    public function store()
    {
        $selectedCabang = Session::get('selected_cabang') ?? 1;

        // Validasi input
        $this->validate();

        if ($this->selectedBarang == null) {
            return $this->showError('Barang harus diisi!');
        }

        DB::beginTransaction();
        try {
            // Periksa apakah stok barang sudah ada
            $findStok = Stok::where('id_barang', $this->selectedBarang)
                ->where('id_cabang', $selectedCabang)
                ->first();

            if ($findStok) {
                return $this->showError("Stok barang '{$findStok->barang->nama_barang}' sudah ada!");
            }

            // Jika stok belum ada, buat stok baru
            Stok::create([
                'id_barang' => $this->selectedBarang,
                'id_cabang' => $selectedCabang,
                'stok' => $this->stok,
            ]);

            DB::commit();

            $this->dispatch($this->tableName);
            $this->reset(['selectedBarang', 'stok']);
            $this->dispatch('reset-choices');
            $this->loadBarangs();
            $this->showSuccess('Berhasil menyimpan data stok!');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->showError('Terjadi kesalahan saat menyimpan data!');
            throw $th;
        }
    }


    public function update()
    {
        $selectedCabang = Session::get('selected_cabang') ?? 1;

        // Validate input
        $this->validate();

        if ($this->selectedBarang == null) {
            return $this->showError('Barang harus diisi!');
        }

        DB::beginTransaction();
        try {
            // Find existing stock
            $stok = Stok::where('id_barang', $this->selectedBarang)
                ->where('id_cabang', $selectedCabang)
                ->first();

            if (!$stok) {
                return $this->showError('Data stok tidak ditemukan!');
            }

            // Update stock
            $stok->update([
                'stok' => $this->stok
            ]);

            DB::commit();

            $this->dispatch($this->tableName);
            $this->reset(['selectedBarang', 'stok', 'namaBarang']);
            $this->isEdit = false;
            $this->dispatch('reset-choices');
            $this->loadBarangs();
            $this->showSuccess('Berhasil mengupdate data stok!');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->showError('Terjadi kesalahan saat mengupdate data!');
            throw $th;
        }
    }


    #[On('edit-stok')]
    public function loadDataForEdit($id)
    {
        $stok = Stok::findOrFail($id);
        $this->isEdit = true;
        $this->selectedBarang = $stok->barang->id;
        $this->stok = $stok->stok;

        $this->namaBarang = $stok->barang->nama_barang;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function loadBarangs()
    {
        $selectedCabang = Session::get('selected_cabang') ?? 1;

        $this->barangs = Barang::whereNotIn('id', function ($query) use ($selectedCabang) {
            $query->select('id_barang')
                ->from('stoks')
                ->where('id_cabang', $selectedCabang);
        })
            ->orderBy('nama_barang', 'asc')
            ->get();
    }


    public function batalEdit()
    {
        // dd('oke bolo');
        $this->isEdit = false;
        $this->reset(['selectedBarang', 'stok', 'namaBarang']);
        $this->dispatch('reset-choices');
    }


    #[On('update-selected-barang')]
    public function updateSelectedBarang($value)
    {
        $this->selectedBarang = $value;
    }


    public function render()
    {
        return view('livewire.stok.create-stok');
    }
}
