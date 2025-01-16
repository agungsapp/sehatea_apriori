<?php

namespace App\Livewire\Kategori;

use App\Models\Kategori;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class KategoriPage extends Component
{
    use LivewireAlert;

    public $kategoriId, $nama;

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

    #[On('edit-kategori')] // Mendengarkan event 'edit-kategori'
    public function loadUserForEdit($id)
    {
        $kategori = Kategori::findOrFail($id);

        $this->kategoriId = $kategori->id;
        $this->nama = $kategori->nama;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->kategoriId) {
            // Update existing kategori
            $kategori = Kategori::findOrFail($this->kategoriId);
            $kategori->update([
                'nama' => $this->nama,
            ]);
        } else {
            // Create new kategori
            Kategori::create([
                'nama' => $this->nama,
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data kategori!');
        $this->dispatch('pg:eventRefresh-kategori-table-lpg4fv-table');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['nama']);
    }


    public function render()
    {
        return view('livewire.kategori.kategori-page');
    }
}
