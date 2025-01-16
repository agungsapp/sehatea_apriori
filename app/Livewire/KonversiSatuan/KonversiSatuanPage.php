<?php

namespace App\Livewire\KonversiSatuan;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\KonversiSatuan;
use App\Models\Produk;
use App\Models\Satuan;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Database\QueryException;


class KonversiSatuanPage extends Component
{

    use LivewireAlertHelpers;

    public string $tableName = 'konversi-satuan-table';

    public string $context = 'konversi satuan';

    public $konversiId;

    public $selectedBahan, $satuanAwal, $satuanTujuan, $rasio, $catatan;

    public $isEdit = false;

    public $bahans, $satuans;



    protected function rules()
    {
        return [
            'selectedBahan' => 'required|exists:bahans,id',
            'satuanAwal' => 'required|exists:satuans,nama',
            'satuanTujuan' => 'required|exists:satuans,nama',
            'rasio' => 'required|numeric',
        ];
    }
    protected function messages()
    {
        return [
            'selectedBahan.required' => 'Bahan harus dipilih.',
            'selectedBahan.exists' => 'Bahan tidak ditemukan.',
            'satuanAwal.required' => 'Satuan awal harus dipilih.',
            'satuanAwal.exists' => 'Satuan awal tidak ditemukan.',
            'satuanTujuan.required' => 'Satuan tujuan harus dipilih.',
            'satuanTujuan.exists' => 'Satuan tujuan tidak ditemukan.',
            'rasio.required' => 'Rasio harus diisi.',
            'rasio.numeric' => 'Rasio harus berupa angka.',
        ];
    }

    public function mount()
    {
        $this->bahans = Bahan::orderBy('nama', 'asc')->get();
        $this->satuans = Satuan::all();
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->konversiId = $id;
        $data = KonversiSatuan::findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>$data->nama</strong> ?", 'deleteItemConfirmed');
    }
    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        try {
            KonversiSatuan::destroy($this->konversiId);
            $this->showSuccess("Berhasil menghapus data {$this->context} !");
            $this->konversiId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Throwable $th) {
            // throw $th;
            $this->showError("Terjadi kesalahan pada server !");
        }
    }

    #[On('edit-item')]
    public function loadDataForEdit($id)
    {
        $data = KonversiSatuan::findOrFail($id);

        $this->isEdit = true;
        $this->konversiId = $data->id;
        $this->selectedBahan = $data->bahan_id;
        $this->satuanAwal = $data->satuan_awal;
        $this->satuanTujuan = $data->satuan_tujuan;
        $this->rasio = $data->rasio;
        $this->catatan = $data->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 3000,
        ]);
    }


    public function save()
    {
        $this->validate();

        try {
            if ($this->konversiId) {
                // Update existing record
                $data = KonversiSatuan::findOrFail($this->konversiId);
                $data->update([
                    'bahan_id' => $this->selectedBahan,
                    'satuan_awal' => $this->satuanAwal,
                    'satuan_tujuan' => $this->satuanTujuan,
                    'rasio' => $this->rasio,
                    'catatan' => $this->catatan,
                ]);
            } else {
                // Create new record
                KonversiSatuan::create([
                    'bahan_id' => $this->selectedBahan,
                    'satuan_awal' => $this->satuanAwal,
                    'satuan_tujuan' => $this->satuanTujuan,
                    'rasio' => $this->rasio,
                    'catatan' => $this->catatan,
                ]);
            }

            $this->showSuccess("Berhasil menyimpan data {$this->context}!");
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
            $this->resetForm();
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Code for UniqueConstraintViolation
                $this->showError("Data konversi dengan kombinasi yang sama sudah ada!");
            } else {
                $this->showError("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
            }
        }
    }


    public function batalEdit()
    {
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->reset(['selectedBahan', 'satuanAwal', 'satuanTujuan', 'rasio', 'catatan']);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.konversi-satuan.konversi-satuan-page');
    }
}
