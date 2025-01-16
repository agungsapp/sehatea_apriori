<div>
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h2>Detail Transaksi: {{ $transaksi->kode }}</h2>
        </div>
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($transaksi->detailTransaksi as $detail)
                <tr>
                  <td><span class="btn btn-success">{{ $detail->kode }}</span></td>
                  <td>
                    @if ($editingDetailId === $detail->id)
                      <select wire:model="editProdukId" class="form-control">
                        @foreach ($products as $product)
                          <option value="{{ $product->id }}">{{ $product->nama }}</option>
                        @endforeach
                      </select>
                    @else
                      {{ $detail->produk->nama }}
                    @endif
                  </td>
                  <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                  <td>
                    @if ($editingDetailId === $detail->id)
                      <input type="number" wire:model="editQty" class="form-control" min="1">
                    @else
                      {{ $detail->qty }}
                    @endif
                  </td>
                  <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                  <td>
                    @if ($editingDetailId === $detail->id)
                      <button class="btn btn-success btn-sm" wire:click="updateDetail">Simpan</button>
                      <button class="btn btn-secondary btn-sm" wire:click="cancelEdit">Batal</button>
                    @else
                      <button class="btn btn-warning btn-sm" wire:click="editDetail({{ $detail->id }})">Edit</button>
                      <button class="btn btn-danger btn-sm" wire:click="deleteDetail({{ $detail->id }})"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">Hapus</button>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Tidak ada detail transaksi</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
