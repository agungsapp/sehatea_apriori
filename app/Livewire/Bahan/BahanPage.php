<?php

namespace App\Livewire\Bahan;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\Produk;
use App\Models\Satuan;
use Livewire\Attributes\On;
use Livewire\Component;

class BahanPage extends Component
{

    use LivewireAlertHelpers;

    public string $tableName = 'bahan-table-66tahd-table';
    public string $context = 'bahan';

    public $bahanId;

    public $nama, $satuan, $stok = 0, $catatan;

    public $isEdit = false;

    public $satuans;



    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'satuan' => 'required',
            'stok' => 'required|numeric',
        ];
    }
    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'satuan.required' => 'Satuan wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.numeric' => 'Stok harus berupa angka.',
        ];
    }

    public function mount()
    {
        $this->satuans = Satuan::all();
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->bahanId = $id;
        $data = Bahan::findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>$data->nama</strong> ?", 'deleteItemConfirmed');
    }
    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        try {
            Bahan::destroy($this->bahanId);
            $this->showSuccess("Berhasil menghapus data {$this->context} !");
            $this->bahanId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            // throw $th;
            $this->showError("Terjadi kesalahan pada server !");
        }
    }

    #[On('edit-item')]
    public function loadDataForEdit($id)
    {
        $data = Bahan::findOrFail($id);

        $this->isEdit = true;
        $this->bahanId = $data->id;
        $this->nama = $data->nama;
        $this->satuan = $data->satuan;
        $this->stok = $data->stok;
        $this->catatan = $data->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->bahanId) {
            // Update existing brand
            $data = Bahan::findOrFail($this->bahanId);
            $data->update([
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'stok' => $this->stok,
                'catatan' => $this->catatan,
            ]);
        } else {
            // Create new brand
            Bahan::create([
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'stok' => $this->stok,
                'catatan' => $this->catatan,
            ]);
        }

        $this->showSuccess("Berhasil menyimpan data {$this->context}!");
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetForm();
    }

    public function batalEdit()
    {
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->reset(['nama', 'satuan', 'stok', 'catatan']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.bahan.bahan-page');
    }
}
