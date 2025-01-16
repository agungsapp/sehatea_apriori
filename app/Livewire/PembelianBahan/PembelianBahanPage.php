<?php

namespace App\Livewire\PembelianBahan;

use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\JenisPengeluaran;
use App\Models\KonversiSatuan;
use App\Models\Pengeluaran;
use App\Models\Satuan;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PembelianBahanPage extends Component
{
    use LivewireAlertHelpers;

    public string $tableName = 'pembelian-bahan-table';
    public string $context = 'bahan';
    public $pengeluaranId;
    public $selectedBahan, $selectedSatuan;
    public $selectedJenis = 1;
    public $selectedDana = 1;
    public $qty, $hargaSatuan, $subtotal, $tanggal, $catatan;
    public $isEdit = false;
    public $oldQty; // Untuk menyimpan qty lama saat update
    public $bahans, $jenisPengeluarans, $sumberDanas, $satuans;

    protected function rules()
    {
        return [
            'selectedBahan' => 'required|exists:bahans,id',
            'selectedJenis' => 'required|exists:jenis_pengeluarans,id',
            'selectedDana' => 'required|exists:sumber_danas,id',
            'selectedSatuan' => 'required|exists:satuans,nama',
            'qty' => 'required|numeric|min:0',
            'hargaSatuan' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'selectedBahan.required' => 'Pilih bahan yang ingin digunakan.',
            'selectedBahan.exists' => 'Bahan yang dipilih tidak valid.',
            'selectedJenis.required' => 'Pilih jenis pengeluaran.',
            'selectedJenis.exists' => 'Jenis pengeluaran yang dipilih tidak valid.',
            'selectedDana.required' => 'Pilih sumber dana.',
            'selectedDana.exists' => 'Sumber dana yang dipilih tidak valid.',
            'selectedSatuan.required' => 'Pilih satuan yang sesuai.',
            'selectedSatuan.exists' => 'Satuan yang dipilih tidak valid.',
            'qty.required' => 'Jumlah (Qty) harus diisi.',
            'qty.numeric' => 'Jumlah (Qty) harus berupa angka.',
            'qty.min' => 'Jumlah (Qty) tidak boleh negatif.',
            'hargaSatuan.required' => 'Harga satuan harus diisi.',
            'hargaSatuan.numeric' => 'Harga satuan harus berupa angka.',
            'hargaSatuan.min' => 'Harga satuan tidak boleh negatif.',
            'subtotal.required' => 'Subtotal harus diisi.',
            'subtotal.numeric' => 'Subtotal harus berupa angka.',
            'subtotal.min' => 'Subtotal tidak boleh negatif.',
            'tanggal.required' => 'Tanggal harus diisi.',
        ];
    }

    public function mount()
    {
        $this->tanggal = Carbon::now()->format('Y-m-d');
        $this->bahans = Bahan::orderBy('nama', 'asc')->get();
        $this->jenisPengeluarans = JenisPengeluaran::all();
        $this->sumberDanas = SumberDana::all();
        $this->satuans = Satuan::all();
    }

    #[On('delete-item')]
    public function deleteItem($id)
    {
        $this->pengeluaranId = $id;
        $data = Pengeluaran::findOrFail($id);
        $this->showCon("Apakah anda yakin ingin menghapus {$this->context} <strong>{$data->bahan->nama}</strong> ?", 'deleteItemConfirmed');
    }

    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        DB::beginTransaction();
        try {
            $pengeluaran = Pengeluaran::findOrFail($this->pengeluaranId);

            // Kurangi stok sebelum menghapus
            if (!$this->kurangiStok($pengeluaran->bahan_id, $pengeluaran->qty, $pengeluaran->satuan)) {
                DB::rollBack();
                return;
            }

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
        $data = Pengeluaran::findOrFail($id);

        $this->isEdit = true;
        $this->pengeluaranId = $data->id;
        $this->selectedBahan = $data->bahan_id;
        $this->selectedSatuan = $data->satuan;
        $this->selectedJenis = $data->jenis_pengeluaran_id;
        $this->selectedDana = $data->sumber_dana_id;
        $this->qty = $data->qty;
        $this->oldQty = $data->qty; // Simpan qty lama
        $this->hargaSatuan = $data->harga_satuan;
        $this->subtotal = $data->subtotal;
        $this->tanggal = $data->tanggal;
        $this->catatan = $data->catatan;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function updatedQty()
    {
        $this->subtotal = (int)$this->qty * (int)$this->hargaSatuan;
    }

    public function updatedHargaSatuan()
    {
        $this->subtotal = (int)$this->qty * (int)$this->hargaSatuan;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->pengeluaranId) {
                // Update
                $pengeluaran = Pengeluaran::findOrFail($this->pengeluaranId);

                // Hitung perubahan stok
                $selisihQty = $this->qty - $this->oldQty;

                // Jika ada perubahan qty
                if ($selisihQty != 0) {
                    if ($selisihQty > 0) {
                        // Jika qty baru lebih besar, tambah selisih ke stok
                        $this->tambahStok($this->selectedBahan, abs($selisihQty), $this->selectedSatuan);
                    } else {
                        // Jika qty baru lebih kecil, kurangi selisih dari stok
                        $this->kurangiStok($this->selectedBahan, abs($selisihQty), $this->selectedSatuan);
                    }
                }

                $pengeluaran->update([
                    'bahan_id' => $this->selectedBahan,
                    'satuan' => $this->selectedSatuan,
                    'jenis_pengeluaran_id' => $this->selectedJenis,
                    'sumber_dana_id' => $this->selectedDana,
                    'qty' => $this->qty,
                    'harga_satuan' => $this->hargaSatuan,
                    'subtotal' => $this->subtotal,
                    'tanggal' => $this->tanggal,
                    'catatan' => $this->catatan,
                ]);
            } else {
                // Create

                Pengeluaran::create([
                    'bahan_id' => $this->selectedBahan,
                    'satuan' => $this->selectedSatuan,
                    'jenis_pengeluaran_id' => $this->selectedJenis,
                    'sumber_dana_id' => $this->selectedDana,
                    'qty' => $this->qty,
                    'harga_satuan' => $this->hargaSatuan,
                    'subtotal' => $this->subtotal,
                    'tanggal' => $this->tanggal,
                    'catatan' => $this->catatan,
                ]);
                // Coba tambah stok dulu
                if (!$this->tambahStok($this->selectedBahan, $this->qty, $this->selectedSatuan)) {
                    DB::rollBack();
                    return; // Keluar dari fungsi jika tambah stok gagal
                }

                // $bahan = Bahan::find($this->selectedBahan);

                // if ($this->selectedSatuan !== $bahan->satuan) {
                //     try {
                //         $konversi = KonversiSatuan::where('bahan_id', $this->selectedBahan)
                //             ->where('satuan_awal', $this->selectedSatuan)->first();

                //         $realStok = $this->qty * $konversi->rasio;

                //         dd("ketemu bre", $konversi);
                //     } catch (\Throwable $th) {
                //         throw $th;
                //     }
                // }

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

    protected function tambahStok($bahanId, $qty, $satuan)
    {
        try {
            $bahan = Bahan::findOrFail($bahanId);

            if ($bahan->satuan == $satuan) {
                $bahan->stok += $qty;
                $bahan->save();
                return true;
            }

            // Cek konversi satuan
            $konversi = KonversiSatuan::where('bahan_id', $bahanId)
                ->where('satuan_awal', $satuan)
                ->where('satuan_tujuan', $bahan->satuan)
                ->first();

            // Jika konversi tidak ditemukan, tampilkan error dan return false
            if (!$konversi) {
                $this->showError("Data konversi satuan untuk bahan {$bahan->nama} dari {$satuan} ke {$bahan->satuan} tidak ditemukan!");
                return false;
            }

            // Jika konversi ditemukan, lakukan perhitungan
            $hasil = $qty * $konversi->rasio;
            $bahan->stok += $hasil;
            $bahan->save();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError('Error saat update stok: ' . $e->getMessage());
            return false;
        }
    }

    protected function kurangiStok($bahanId, $qty, $satuan)
    {
        try {
            $bahan = Bahan::findOrFail($bahanId);
            $qtyDalamSatuanBahan = $qty;

            if ($bahan->satuan != $satuan) {
                $konversi = KonversiSatuan::where('bahan_id', $bahanId)
                    ->where('satuan_awal', $satuan)
                    ->where('satuan_tujuan', $bahan->satuan)
                    ->first();

                // Jika konversi tidak ditemukan
                if (!$konversi) {
                    $this->showError("Data konversi satuan untuk bahan {$bahan->nama} dari {$satuan} ke {$bahan->satuan} tidak ditemukan!");
                    return false;
                }

                $qtyDalamSatuanBahan = $qty * $konversi->rasio;
            }

            // Cek apakah stok mencukupi
            if ($bahan->stok < $qtyDalamSatuanBahan) {
                $this->showError("Stok {$bahan->nama} tidak mencukupi untuk operasi ini. Stok tersedia: {$bahan->stok} {$bahan->satuan}");
                return false;
            }

            $bahan->stok -= $qtyDalamSatuanBahan;
            $bahan->save();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError('Error saat kurangi stok: ' . $e->getMessage());
            return false;
        }
    }

    public function batalEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'selectedBahan',
            'selectedSatuan',
            'selectedJenis',
            'selectedDana',
            'qty',
            'oldQty',
            'hargaSatuan',
            'subtotal',
            'tanggal',
            'catatan'
        ]);
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.pembelian-bahan.pembelian-bahan-page');
    }
}
