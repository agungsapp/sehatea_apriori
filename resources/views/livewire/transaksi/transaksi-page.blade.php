@php
  use Carbon\Carbon;

@endphp
<div class="row gap-3">
  <div class="row">
    <h1>History Transaksi</h1>
  </div>
  <div class="card">
    <div class="card-header">
      <a href="{{ route('create-transaksi') }}" class="btn btn-primary">+ Buat Transaksi Baru</a>
    </div>
    <div class="card-body">
      <livewire:transaksi.transaksi-table />
    </div>
  </div>


  <div class="row gap-3">

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true"
      wire:ignore.self>
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Transaksi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if ($transaksi)
              <div class="row">
                <div class="col-6">
                  <table class="table table-borderless">
                    <tr>
                      <td width="150"><strong>No Invoice</strong></td>
                      <td width="10">:</td>
                      <td><span class="bg-primary p-2 text-white rounded-2">{{ $transaksi->no_invoice }}</span></td>
                    </tr>
                    @if ($transaksi->pelangganKendaraan->pelanggan->tipe == 'reguler')
                      <tr>
                        <td><strong>Nama Pelanggan</strong></td>
                        <td>:</td>
                        <td>{{ $transaksi->pelangganKendaraan->pelanggan->nama }}</td>
                      </tr>
                    @else
                      <tr>
                        <td><strong>Nama Instansi</strong></td>
                        <td>:</td>
                        <td>{{ $transaksi->pelangganKendaraan->pelanggan->instansi->nama_instansi }}</td>
                      </tr>
                    @endif
                    <tr>
                      <td><strong>Total Harga</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($transaksi->total_harga) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Total Di Bayar</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($transaksi->total_bayar) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Sisa Tagihan</strong></td>
                      <td>:</td>
                      <td>{{ Str::rupiah($transaksi->sisa_tagihan) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Status Pembayaran</strong></td>
                      <td>:</td>
                      <td>
                        @if ($transaksi->status_pembayaran == 'lunas')
                          <span class='badge bg-success'>Lunas</span>
                        @else
                          <span class='badge bg-danger'>Belum Lunas</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Tanggal</strong></td>
                      <td>:</td>
                      <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                      <td><strong>Catatan</strong></td>
                      <td>:</td>
                      <td>{{ $transaksi->catatan ?? '- TIDAK ADA CATATAN -' }}</td>
                    </tr>
                  </table>
                </div>
                <div class="col-6">
                  <table class="table table-borderless">
                    <tr>
                      <td width="150"><strong>Kendaraan</strong></td>
                      <td width="10">:</td>
                      <td>{{ $transaksi->pelangganKendaraan->kendaraan->nama_kendaraan }}</td>
                    </tr>
                    <tr>
                      <td><strong>Nomor Polisi</strong></td>
                      <td>:</td>
                      <td>{{ $transaksi->pelangganKendaraan->kendaraan->nopol }}</td>
                    </tr>
                    <tr>
                      <td><strong>Tanggal Masuk</strong></td>
                      <td>:</td>
                      <td>{{ Carbon::parse($transaksi->tanggal_masuk)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                      <td><strong>Tanggal Keluar</strong></td>
                      <td>:</td>
                      <td>{{ Carbon::parse($transaksi->tanggal_keluar)->format('d/m/Y') }}</td>
                    </tr>
                  </table>
                </div>
              </div>



              @if ($transaksi->penjualanDetail)
                <div class="card mb-3">
                  <div class="card-header">
                    <h5 class="card-title">
                      Barang
                    </h5>
                  </div>
                  <div class="card-body">
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
                            @foreach ($transaksi->penjualanDetail as $item)
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

                  </div>
                </div>
              @endif


              @if ($transaksi->penjualanJasa)
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title">
                      Jasa
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <ul>

                        </ul>

                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Nama Jasa</th>
                              <th scope="col">Harga Jasa</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($transaksi->penjualanJasa as $jasa)
                              <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $jasa->jasa->nama }}</td>
                                <td>{{ Str::rupiah($jasa->harga_jasa) }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>

                      </div>
                    </div>
                  </div>
                </div>
              @endif
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
