<?php

namespace App\Livewire\Jasa;

use App\LivewireAlertHelpers;
use App\Models\Jasa;
use Livewire\Attributes\On;
use Livewire\Component;

class JasaPage extends Component
{
    use LivewireAlertHelpers;

    public $jasaId, $nama, $kode, $catatan;
    public $isEdit = false;

    public string $tableName = 'pg:eventRefresh-jasa-table-iv9srx-table';




    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'nullable',
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

    #[On('delete-jasa')]
    public function deleteJasa($id)
    {
        $this->jasaId = $id;
        $jasa = Jasa::findOrFail($id);
        $this->showCon(
            "Apakah anda yakin ingin menghapus jasa <strong>$jasa->nama</strong> ?",
            'deleteJasaConfirmed'
        );
    }
    #[On('deleteJasaConfirmed')]
    public function deleteJasaConfirmed()
    {
        try {
            Jasa::destroy($this->jasaId);
            $this->alert('success', 'Berhasil menghapus data jasa !');
            $this->jasaId = null;
            $this->dispatch($this->tableName);
        } catch (\Throwable $th) {
            // throw $th;
            $this->alert('error', 'Terjadi kesalahan pada server !');
        }
    }

    #[On('edit-jasa')]
    public function loadUserForEdit($id)
    {
        $jasa = Jasa::findOrFail($id);

        $this->isEdit = true;
        $this->jasaId = $jasa->id;
        $this->nama = $jasa->nama;
        $this->kode = $jasa->kode_jasa;
        $this->catatan = $jasa->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->jasaId) {
                // Update existing jasa
                $jasa = Jasa::findOrFail($this->jasaId);
                $jasa->update([
                    'nama' => $this->nama,
                    'kode_jasa' => $this->kode,
                    'catatan' => $this->catatan,
                ]);
            } else {
                // Create new jasa
                Jasa::create([
                    'nama' => $this->nama,
                    'kode_jasa' => $this->kode,
                    'catatan' => $this->catatan,
                ]);
            }

            $this->showSuccess('Berhasil menyimpan data !');
            $this->dispatch($this->tableName);
            $this->resetForm();
        } catch (\Throwable $th) {
            //throw $th;
            $this->showError('Terjadi kesalahan saat menyimpan data !');
        }
    }

    public function batalEdit()
    {
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->reset(['nama', 'kode', 'catatan', 'jasaId']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.jasa.jasa-page');
    }
}
