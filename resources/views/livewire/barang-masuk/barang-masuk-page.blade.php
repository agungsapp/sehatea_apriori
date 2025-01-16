<div class="row gap-3">
  <div class="row">
    <h1>Data Nota Barang Masuk</h1>
  </div>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <a href="{{ route('create-barang-masuk') }}" class="btn btn-primary">+ Tambah Barang Masuk</a>
      <a href="{{ route('retur-barang') }}" class="btn btn-outline-danger">Retur Barang</a>
    </div>
    <div class="card-body">
      <livewire:barang-masuk.barang-masuk-table />
    </div>
  </div>

  <div class="row gap-3">

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true"
      wire:ignore.self>
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Barang Masuk</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if ($detailBarangMasuk)
              <div class="row">
                <div class="col-6">
                  <table class="table table-borderless">
                    <tr>
                      <td width="150"><strong>No Invoice</strong></td>
                      <td width="10">:</td>
                      <td><span class="bg-primary p-2 text-white rounded-2">{{ $detailBarangMasuk->no_invoice }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Supplier</strong></td>
                      <td>:</td>
                      <td>{{ $detailBarangMasuk->supplier->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td><strong>Total Harga</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($detailBarangMasuk->total_harga) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Total Di Bayar</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($detailBarangMasuk->total_bayar) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Sisa Tagihan</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($detailBarangMasuk->sisa_tagihan) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Status Pembayaran</strong></td>
                      <td>:</td>
                      <td>
                        @if ($detailBarangMasuk->status_pembayaran == 'lunas')
                          <span class='badge bg-success'>Lunas</span>
                        @else
                          <span class='badge bg-warning'>Belum Lunas</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Tanggal</strong></td>
                      <td>:</td>
                      <td>{{ $detailBarangMasuk->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                      <td><strong>Catatan</strong></td>
                      <td>:</td>
                      <td>{{ $detailBarangMasuk->catatan ?? '- TIDAK ADA CATATAN -' }}</td>
                    </tr>
                  </table>
                </div>
              </div>


              <div class="row">
                <div class="col-12">
                  <ul>

                  </ul>

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Barang</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($detailBarangMasuk->barangMasukDetail as $item)
                        <tr>
                          <th scope="row">{{ $loop->iteration }}</th>
                          <td>{{ $item->barang->nama_barang }}</td>
                          <td>{{ $item->jumlah }}</td>
                          <td>{{ Str::rupiah($item->harga_satuan) }}</td>
                          <td>{{ Str::rupiah($item->subtotal) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>

                </div>
              </div>
            @else
              <p>Data tidak ditemukan.</p>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Script work -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('detailModal');
        const modal = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('shown.bs.modal', function() {
          console.log('Modal telah ditampilkan');
        });

        document.addEventListener('livewire:init', () => {
          Livewire.on('show-detail-modal', (event) => {
            console.log('Event show-detail-modal diterima');
            modal.show();
          });
        });
      });
    </script>
  </div>
</div>
