<?php

namespace App\Livewire\Stok;

use App\LivewireAlertHelpers;
use App\Models\Stok;
use Livewire\Attributes\On;
use Livewire\Component;

class StokPage extends Component
{

    use LivewireAlertHelpers;

    public string $tableName = 'stok-table-zuwran-table';
    public $stokId;

    #[On('delete-stok')]
    public function deleteBarang($id)
    {
        $this->stokId = $id;
        $barang = Stok::findOrFail($id);
        $this->showCon("Harap hati hati dalam melakukan hapus data stok, anda ingin lanjut menghapus ?", 'deleteStokConfirmed');
    }
    #[On('deleteStokConfirmed')]
    public function deleteStokConfirmed()
    {
        try {
            Stok::destroy($this->stokId);
            $this->alert('success', 'Berhasil menghapus data barang!');
            $this->stokId = null;
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'stoks_id_barang_foreign')) {
                // Pesan khusus untuk error karena relasi stok
                $this->showError('Data barang ini tidak dapat dihapus karena masih digunakan di tabel stok.');
            } else {
                // Pesan default untuk error lainnya
                $this->showError('Terjadi kesalahan pada server!');
            }
        } catch (\Throwable $th) {
            $this->showError('Terjadi kesalahan yang tidak terduga.');
        }
    }

    public function render()
    {
        return view('livewire.stok.stok-page');
    }
}
