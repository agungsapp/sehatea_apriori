<?php

namespace App\Livewire\PengeluaranLain;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\JenisPengeluaran;
use App\Models\KonversiSatuan;
use App\Models\Pengeluaran;
use App\Models\PengeluaranLain;
use App\Models\Satuan;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PengeluaranLainPage extends Component
{
    use LivewireAlertHelpers;

    public string $tableName = 'pengeluaran-lain-table';
    public string $context = 'pengeluaran lain lain';
    public $pengeluaranId;
    public $selectedJenis = 1;
    public $selectedDana = 1;
    public $nama, $harga, $tanggal, $catatan;
    public $isEdit = false;
    public  $jenisPengeluarans, $sumberDanas;

    protected function rules()
    {
        return [
            'selectedDana' => 'required|exists:sumber_danas,id',
            'selectedJenis' => 'required|exists:jenis_pengeluarans,id',
            'harga' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'selectedDana.required' => 'Pilih sumber dana pengeluaran.',
            'selectedDana.exists' => 'Sumber dana pengeluaran yang dipilih tidak valid.',
            'selectedJenis.required' => 'Pilih jenis pengeluaran.',
            'selectedJenis.exists' => 'Jenis pengeluaran yang dipilih tidak valid.',
            'harga.required' => 'Harga satuan harus diisi.',
            'harga.numeric' => 'Harga satuan harus berupa angka.',
            'harga.min' => 'Harga satuan tidak boleh negatif.',
            'tanggal.required' => 'Tanggal harus diisi.',
        ];
    }

    public function mount()
    {
        $this->tanggal = Carbon::now()->format('Y-m-d\TH:i');
        $this->jenisPengeluarans = JenisPengeluaran::all();
        $this->sumberDanas = SumberDana::all();
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->pengeluaranId = $id;
        $data = PengeluaranLain::findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>$data->bahan->nama</strong> ?", 'deleteItemConfirmed');
    }

    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        DB::beginTransaction();
        try {
            $pengeluaran = PengeluaranLain::findOrFail($this->pengeluaranId);
            $pengeluaran->delete();
            DB::commit();
            $this->showSuccess("Berhasil menghapus data {$this->context} !");
            $this->pengeluaranId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Exception $th) {
            DB::rollBack();
            $this->showError("Terjadi kesalahan: " . $th->getMessage());
        }
    }

    #[On('edit-item')]
    public function loadDataForEdit($id)
    {
        $data = PengeluaranLain::findOrFail($id);

        $this->isEdit = true;
        $this->pengeluaranId = $data->id;
        $this->selectedJenis = $data->jenis_pengeluaran_id;
        $this->selectedDana = $data->sumber_dana_id;
        $this->harga = $data->harga;
        $this->tanggal = $data->tanggal_pengeluaran;
        $this->catatan = $data->keterangan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->pengeluaranId) {
                // Update
                $pengeluaran = PengeluaranLain::findOrFail($this->pengeluaranId);

                $pengeluaran->update([
                    'nama' => $this->nama,
                    'jenis_pengeluaran_id' => $this->selectedJenis,
                    'sumber_dana_id' => $this->selectedDana,
                    'harga' => $this->harga,
                    'tanggal_pengeluaran' => $this->tanggal,
                    'keterangan' => $this->catatan,
                ]);
            } else {
                // Create
                PengeluaranLain::create([
                    'nama' => $this->nama,
                    'jenis_pengeluaran_id' => $this->selectedJenis,
                    'sumber_dana_id' => $this->selectedDana,
                    'harga' => $this->harga,
                    'tanggal_pengeluaran' => $this->tanggal,
                    'keterangan' => $this->catatan,
                ]);
            }

            DB::commit();
            $this->showSuccess("Berhasil menyimpan data {$this->context}!");
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
            $this->resetForm();
        } catch (\Exception $th) {
            DB::rollBack();
            $this->showError("Terjadi kesalahan: " . $th->getMessage());
        }
    }

    public function batalEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'nama',
            'selectedJenis',
            'selectedDana',
            'harga',
            'tanggal',
            'catatan'
        ]);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.pengeluaran-lain.pengeluaran-lain-page');
    }
}
