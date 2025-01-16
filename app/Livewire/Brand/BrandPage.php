<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use App\Models\Merk;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class BrandPage extends Component
{
    use LivewireAlert;

    public $brandId, $nama;
    public $isEdit = false;



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

    #[On('delete-brand')]
    public function deleteBrand($id)
    {
        $this->brandId = $id;
        $brand = Brand::findOrFail($id);
        $this->alert('warning', "Apakah anda yakin ingin menghapus brand <strong>$brand->nama</strong> ?", [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'deleteBrandConfirmed',
        ]);
    }
    #[On('deleteBrandConfirmed')]
    public function deleteBrandConfirmed()
    {
        try {
            Brand::destroy($this->brandId);
            $this->alert('success', 'Berhasil menghapus data brand !');
            $this->brandId = null;
            $this->dispatch('pg:eventRefresh-brand-table-g4p3gi-table');
        } catch (\Throwable $th) {
            // throw $th;
            $this->alert('error', 'Terjadi kesalahan pada server !');
        }
    }

    #[On('edit-brand')]
    public function loadUserForEdit($id)
    {
        $brand = Brand::findOrFail($id);

        $this->isEdit = true;
        $this->brandId = $brand->id;
        $this->nama = $brand->nama;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->brandId) {
            // Update existing brand
            $brand = Brand::findOrFail($this->brandId);
            $brand->update([
                'nama' => $this->nama,
            ]);
        } else {
            // Create new brand
            Brand::create([
                'nama' => $this->nama,
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
        $this->reset(['nama']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.brand.brand-page');
    }
}
