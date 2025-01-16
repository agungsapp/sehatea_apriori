<?php

namespace App\Livewire\SumberDana;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\JenisPengeluaran;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\SumberDana;
use Livewire\Attributes\On;
use Livewire\Component;

class SumberDanaPage extends Component
{

    use LivewireAlertHelpers;

    public string $tableName = 'sumber-dana-table';


    public string $context = 'sumber dana';

    public $dataId;

    public $nama;

    public $isEdit = false;

    public $satuans;

    public $model;




    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
        ];
    }
    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
        ];
    }

    public function mount()
    {
        $this->model = new SumberDana();

        $this->satuans = Satuan::all();

        // dd($this->model->all());
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->dataId = $id;
        $data = $this->model->findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>$data->nama</strong> ?", 'deleteItemConfirmed');
    }
    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        try {
            $this->model->destroy($this->dataId);
            $this->showSuccess("Berhasil menghapus data {$this->context} !");
            $this->dataId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            // throw $th;
            $this->showError("Terjadi kesalahan pada server !");
        }
    }

    #[On('edit-item')]
    public function loadDataForEdit($id)
    {
        $data = $this->model->findOrFail($id);

        $this->isEdit = true;
        $this->dataId = $data->id;
        $this->nama = $data->nama;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->dataId) {
            // Update existing brand
            $data = $this->model->findOrFail($this->dataId);
            $data->update([
                'nama' => $this->nama,
            ]);
        } else {
            // Create new brand
            $this->model->create([
                'nama' => $this->nama,
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
        $this->reset(['nama', 'dataId']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.sumber-dana.sumber-dana-page');
    }
}
