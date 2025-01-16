<?php

namespace App\Livewire\Instansi;

use App\Models\Instansi;
use App\Models\Supplier;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class InstansiPage extends Component
{
    use LivewireAlert;
    public string $tableName = 'instansi-table-pil3wm-table';
    public $supplierId, $nama, $telepon, $alamat;
    public $isEdit = false;



    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'telepon' => 'required|numeric|min:11',
            'alamat' => 'required|string|max:500',
        ];
    }
    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'telepon.required' => 'Nomor Telepon wajib diisi.',
            'telepon.numeric' => 'Nomor Telepon harus berupa angka.',
            'telepon.min' => 'Nomor Telepon minimal 11 angka.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa text.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
        ];
    }

    #[On('delete-supplier')]
    public function deleteSupplier($id)
    {
        $this->supplierId = $id;
        $supplier = Instansi::findOrFail($id);
        $this->alert('warning', "Apakah anda yakin ingin menghapus supplier <strong>$supplier->nama</strong> ?", [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'deleteSupplierConfirmed',
        ]);
    }
    #[On('deleteSupplierConfirmed')]
    public function deleteSupplierConfirmed()
    {
        try {
            Instansi::destroy($this->supplierId);
            $this->alert('success', 'Berhasil menghapus data supplier !');
            $this->supplierId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('error', 'Terjadi kesalahan pada server !');
        }
    }

    #[On('edit-supplier')]
    public function loadUserForEdit($id)
    {
        $supplier = Instansi::findOrFail($id);

        $this->isEdit = true;
        $this->supplierId = $supplier->id;
        $this->nama = $supplier->nama_instansi;
        $this->telepon = $supplier->telepon;
        $this->alamat = $supplier->alamat;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->supplierId) {
            // Update existing supplier
            $supplier = Instansi::findOrFail($this->supplierId);
            $supplier->update([
                'nama_instansi' => $this->nama,
                'telepon' => $this->telepon,
                'alamat' => $this->alamat,
            ]);
        } else {
            // Create new supplier
            Instansi::create([
                'nama_instansi' => $this->nama,
                'telepon' => $this->telepon,
                'alamat' => $this->alamat,
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data supplier!');
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetForm();
    }

    public function batalEdit()
    {
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->reset(['nama', 'telepon', 'alamat']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.instansi.instansi-page');
    }
}
