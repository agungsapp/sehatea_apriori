<?php

namespace App\Livewire\Cabang;

use App\Models\Cabang;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class CabangPage extends Component
{

    use LivewireAlert;

    public $cabangId, $nama, $telepon, $alamat;
    public $isEdit = false;



    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|numeric|min:11',
            'alamat' => 'nullable|string|max:500',
        ];
    }
    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            // 'telepon.required' => 'Nomor Telepon wajib diisi.',
            'telepon.numeric' => 'Nomor Telepon harus berupa angka.',
            'telepon.min' => 'Nomor Telepon minimal 11 angka.',
            // 'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa text.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
        ];
    }

    #[On('delete-cabang')]
    public function delete($id)
    {
        $this->cabangId = $id;
        $cabang = Cabang::findOrFail($id);
        $this->alert('warning', "Apakah anda yakin ingin menghapus cabang <strong>$cabang->nama</strong> ?", [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'deleteConfirmed',
        ]);
    }
    #[On('deleteConfirmed')]
    public function deleteConfirmed()
    {
        try {
            Cabang::destroy($this->cabangId);
            $this->alert('success', 'Berhasil menghapus data cabang !');
            $this->cabangId = null;
            $this->dispatch('pg:eventRefresh-cabang-table-nkrpnu-table');
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('error', 'Terjadi kesalahan pada server !');
        }
    }

    #[On('edit-cabang')]
    public function loadDataForEdit($id)
    {
        $cabang = Cabang::findOrFail($id);

        $this->isEdit = true;
        $this->cabangId = $cabang->id;
        $this->nama = $cabang->nama;
        $this->alamat = $cabang->alamat;
        $this->telepon = $cabang->telepon;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->cabangId) {
            // Update existing cabang
            $cabang = Cabang::findOrFail($this->cabangId);
            $cabang->update([
                'nama' => $this->nama,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
            ]);
        } else {
            // Create new cabang
            Cabang::create([
                'nama' => $this->nama,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data cabang!');
        $this->dispatch('pg:eventRefresh-cabang-table-nkrpnu-table');
        $this->dispatch('update-option-cabang');
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
        return view('livewire.cabang.cabang-page');
    }
}
