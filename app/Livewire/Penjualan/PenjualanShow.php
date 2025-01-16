<?php

namespace App\Livewire\Penjualan;

use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Livewire\Component;

class PenjualanShow extends Component
{
    public $transaksi;
    public $editingDetailId;
    public $editQty;
    public $editProdukId;
    public $products;

    protected $rules = [
        'editQty' => 'required|integer|min:1',
        'editProdukId' => 'required|exists:produks,id',
    ];

    public function mount($id)
    {
        $this->transaksi = Transaksi::findOrFail($id);
        $this->products = Produk::where('active', true)->get();
    }

    public function editDetail($detailId)
    {
        $this->editingDetailId = $detailId;
        $detail = DetailTransaksi::find($detailId);
        $this->editQty = $detail->qty;
        $this->editProdukId = $detail->produk_id;
    }

    public function updateDetail()
    {
        $this->validate();

        $detail = DetailTransaksi::find($this->editingDetailId);
        $oldSubtotal = $detail->subtotal;

        $newProduk = Produk::find($this->editProdukId);
        $detail->produk_id = $this->editProdukId;
        $detail->harga = $newProduk->harga;
        $detail->qty = $this->editQty;
        $detail->subtotal = $detail->harga * $this->editQty;
        $detail->save();

        // Update transaksi grand_total
        $this->transaksi->grand_total += ($detail->subtotal - $oldSubtotal);
        $this->transaksi->save();

        $this->editingDetailId = null;
        $this->dispatch('detailUpdated');
    }

    public function deleteDetail($detailId)
    {
        $detail = DetailTransaksi::find($detailId);
        $this->transaksi->grand_total -= $detail->subtotal;
        $this->transaksi->save();

        $detail->delete();

        $this->dispatch('detailDeleted');
    }

    public function cancelEdit()
    {
        $this->editingDetailId = null;
    }

    public function render()
    {
        return view('livewire.penjualan.penjualan-show');
    }
}
