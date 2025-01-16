<div>
  <div class="row">
    <div class="col-8">
      <div class="card">
        <div class="card-header">
          <h2>Buat Transaksi</h2>
        </div>
        <div class="card-body">
          <div class="row">
            @foreach ($produk as $item)
              <div class="col-4 mb-3">
                <div class="card">
                  <div class="card-body">
                    <h5>{{ $item->nama }}</h5>
                    <p>Harga: Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                    <div class="btn-group" role="group">
                      <button class="btn btn-danger" wire:click="kurangiDariKeranjang({{ $item->id }})">-</button>
                      <span class="btn btn-light">{{ $jumlah[$item->id] ?? 0 }}</span>
                      <button class="btn btn-success" wire:click="tambahKeKeranjang({{ $item->id }})">+</button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <h2>Keranjang</h2>
        </div>
        <div class="card-body">


          <div class="row mt-4">
            {{-- keranjang --}}
            <div class="col-12">
              @if (count($cart) > 0)
                <table class="table">
                  <thead>
                    <tr>
                      <th>Produk</th>
                      <th>Jumlah</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($cart as $id => $item)
                      <tr>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $jumlah[$id] }}</td>
                        <td>Rp {{ number_format($item->harga * $jumlah[$id], 0, ',', '.') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="2">Total:</th>
                      <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
                    </tr>
                  </tfoot>
                </table>
              @else
                <p>Keranjang Kosong</p>
              @endif
            </div>

            <hr>
            {{-- co --}}
            <div class="col-12">
              <div class="form-group">
                <label>Metode Pembayaran:</label>
                <div class="d-flex gap-3">
                  @foreach ($metode_pembayaran as $metode)
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" wire:model.live="metode_pembayaran_selected"
                        value="{{ $metode->id }}" id="metode_pembayaran_{{ $metode->id }}">
                      <label class="form-check-label" for="metode_pembayaran_{{ $metode->id }}">
                        {{ $metode->nama }}
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="form-group mt-3">
                <label>Metode Pembelian:</label>
                <div class="d-flex gap-3">
                  @foreach ($metode_pembelian as $metode)
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" wire:model.live="metode_pembelian_selected"
                        value="{{ $metode->id }}" id="metode_pembelian_{{ $metode->id }}">
                      <label class="form-check-label" for="metode_pembelian_{{ $metode->id }}">
                        {{ $metode->nama }}
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>

              <button class="btn btn-primary mt-3" wire:click="checkout">Checkout</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h2>Transaksi Hari Ini</h2>
        </div>
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Total</th>
                <th>Metode Pembayaran</th>
                <th>Metode Pembelian</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($transaksiHariIni as $transaksi)
                <tr>
                  <td><a class="btn btn-primary"
                      href="{{ route('mode-kasir-show', $transaksi->id) }}">{{ $transaksi->kode }}</a>
                  </td>
                  <td>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
                  <td>
                    @if ($editingTransaksiId === $transaksi->id)
                      <select wire:model="editMetodePembayaranId" class="form-control">
                        @foreach ($metode_pembayaran as $metode)
                          <option value="{{ $metode->id }}">{{ $metode->nama }}</option>
                        @endforeach
                      </select>
                    @else
                      {{ $transaksi->metodePembayaran->nama }}
                    @endif
                  </td>
                  <td>
                    @if ($editingTransaksiId === $transaksi->id)
                      <select wire:model="editMetodePembelianId" class="form-control">
                        @foreach ($metode_pembelian as $metode)
                          <option value="{{ $metode->id }}">{{ $metode->nama }}</option>
                        @endforeach
                      </select>
                    @else
                      {{ $transaksi->metodePembelian->nama }}
                    @endif
                  </td>
                  <td>
                    @if ($editingTransaksiId === $transaksi->id)
                      <button class="btn btn-success btn-sm" wire:click="updateTransaksi">Simpan</button>
                      <button class="btn btn-secondary btn-sm" wire:click="cancelEdit">Batal</button>
                    @else
                      <button class="btn btn-info btn-sm" wire:click="showDetail({{ $transaksi->id }})">Detail</button>
                      <button class="btn btn-warning btn-sm"
                        wire:click="editTransaksi({{ $transaksi->id }})">Edit</button>
                      <button class="btn btn-danger btn-sm"
                        wire:click="deleteTransaksi({{ $transaksi->id }})">Hapus</button>
                      <button wire:click="confirmDelete">test delete</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="mt-4">
            <h4>Ringkasan Transaksi Hari Ini</h4>
            <p>Total Transaksi: Rp {{ number_format($totalTransaksiHariIni, 0, ',', '.') }}</p>
            <p>Total Cash: Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
            <p>Total Non-Cash: Rp {{ number_format($totalNonCash, 0, ',', '.') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail Transaksi -->
  @if ($selectedTransaksi)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
      role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Transaksi: {{ $selectedTransaksi->kode }}</h5>
            <button type="button" class="close" wire:click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($selectedTransaksi->detailTransaksi as $detail)
                  <tr>
                    <td>{{ $detail->produk->nama }}</td>
                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3">Grand Total:</th>
                  <th>Rp {{ number_format($selectedTransaksi->grand_total, 0, ',', '.') }}</th>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
