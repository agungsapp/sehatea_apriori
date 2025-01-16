<div class="row gap-3">
  <div class="row">
    <h1>Utang Supplier Sudah Lunas</h1>
  </div>
  <div class="card">
    <div class="card-header">
      <a href="{{ route('create-barang-masuk') }}" class="btn btn-primary">+ Tambah Barang Masuk</a>
    </div>
    <div class="card-body">
      <livewire:supplier.sub.supplier-sudah-lunas-table />
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
              <p><strong>No Invoice:</strong> {{ $detailBarangMasuk->no_invoice }}</p>
              <p><strong>Supplier:</strong> {{ $detailBarangMasuk->supplier->nama ?? 'N/A' }}</p>
              <p><strong>Total Harga:</strong> {{ Str::rupiah($detailBarangMasuk->total_harga) }}</p>
              <p><strong>Bayar:</strong> {{ Str::rupiah($detailBarangMasuk->total_bayar) }}</p>
              <p><strong>Status Pembayaran:</strong>
                @if ($detailBarangMasuk->status_pembayaran == 'lunas')
                  <span class='badge bg-success'>Lunas</span>
                @else
                  <span class='badge bg-warning'>Belum Lunas</span>
                @endif
              </p>
              <p><strong>Tanggal:</strong> {{ $detailBarangMasuk->created_at->format('d/m/Y H:i') }}</p>
              <p><strong>Catatan :</strong> {{ $detailBarangMasuk->catatan ?? '- TIDAK ADA CATATAN -' }}</p>

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
