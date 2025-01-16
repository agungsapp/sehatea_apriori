<?php

namespace App\Livewire\Produk;

use App\LivewireAlertHelpers;
use App\Models\Produk;
use Livewire\Attributes\On;
use Livewire\Component;

class ProdukPage extends Component
{

    use LivewireAlertHelpers;

    public string $tableName = 'produk-table';
    public string $context = 'data';

    public $produkId, $nama, $harga;
    public $isEdit = false;



    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ];
    }
    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
        ];
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->produkId = $id;
        $data = Produk::findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>$data->nama</strong> ?", 'deleteItemConfirmed');
    }
    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        try {
            Produk::destroy($this->produkId);
            $this->showSuccess("Berhasil menghapus data {$this->context} !");
            $this->produkId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            // throw $th;
            $this->showError("Terjadi kesalahan pada server !");
        }
    }

    #[On('edit-item')]
    public function loadDataForEdit($id)
    {
        $data = Produk::findOrFail($id);

        $this->isEdit = true;
        $this->produkId = $data->id;
        $this->nama = $data->nama;
        $this->harga = $data->harga;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->produkId) {
            // Update existing brand
            $data = Produk::findOrFail($this->produkId);
            $data->update([
                'nama' => $this->nama,
                'harga' => $this->harga,
            ]);
        } else {
            // Create new brand
            Produk::create([
                'nama' => $this->nama,
                'harga' => $this->harga,
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data brand!');
        $this->dispatch('pg:eventRefresh-brand-table-g4p3gi-table');
        $this->resetForm();
    }

    public function batalEdit()
    {
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->reset(['nama', 'harga']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.produk.produk-page');
    }
}
