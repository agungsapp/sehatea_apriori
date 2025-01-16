<div class="row gap-3">



  <div class="row">
    <h3 class="w-100 d-flex justify-content-between"><span>Pelunasan Transaksi : {{ $penjualan->no_invoice }}</span>
      <span class="btn btn-primary">
        {{ $penjualan->pelangganKendaraan->pelanggan->tipe = 'reguler' ? $penjualan->pelangganKendaraan->pelanggan->nama : '' }}</span>
    </h3>
    <h3>
      <span>Nilai Transaksi : {{ Str::rupiah($penjualan->total_harga) }}</span>
    </h3>

  </div>

  <div class="row">
    <div class="card">
      @if (!$isLunas)
        <form wire:submit.prevent="save">
          <div class="card-body">
            <div class="row">

              <div class="col-4 mb-3">
                <label for="tanggal" class="form-label">
                  Tanggal
                </label>
                <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                  wire:model="tanggal">
                @error('tanggal')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-4 mb-3">
                <label for="sisa" class="form-label">Sisa Hutang</label>
                <input type="text" class="form-control @error('sisa') is-invalid @enderror" id="sisa"
                  placeholder="sisa hutang..." style="background-color: #e2dede !important;" readonly
                  value="{{ Str::rupiah($sisa) }}">
                @error('sisa')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-3 offset-1 mb-3">
                <label for="metodePembayaran" class="form-label">Metode Pembayaran</label>
                <div class="col-12">
                  <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <!-- Radio Button Tunai -->
                    <input type="radio" class="btn-check" id="btncheck1" name="metodePembayaran" autocomplete="off"
                      value="1" wire:model.live="isTunai">
                    <label class="btn btn-outline-primary" for="btncheck1">Tunai</label>

                    <!-- Radio Button Transfer -->
                    <input type="radio" class="btn-check" id="btncheck2" name="metodePembayaran" autocomplete="off"
                      value="0" wire:model.live="isTunai">
                    <label class="btn btn-outline-primary" for="btncheck2">Transfer</label>
                  </div>
                </div>
              </div>

              @if (!$isTunai)

                <div class="col-3 mb-3">
                  <label for="bank" class="form-label">
                    Bank
                  </label>
                  <select id="bank" class="form-control  @error('bank') is-invalid @enderror form-select"
                    wire:model="selectedBank">
                    <option selected>-- pilih bank --</option>
                    @forelse ($banks as $bank)
                      <option value="{{ $bank->id }}">
                        {{ $bank->bank . ' -  ' . $bank->norek . ' a/n ' . $bank->atas_nama }}</option>
                    @empty
                      <option>NO_DATA</option>
                    @endforelse
                  </select>
                  @error('bank')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              @endif

              <div class="{{ $isTunai == true ? 'col-6' : 'col-3' }} mb-3">
                <label for="bayar" class="form-label">
                  Di Bayarkan
                </label>
                <input type="number" class="form-control @error('bayar') is-invalid @enderror" id="bayar"
                  wire:model.live.debounce.500ms="bayar" placeholder="0">
                @if ($bayar != null)
                  <div id="bayarHelp" class="form-text text-end">{{ Str::rupiah($bayar) }}</div>
                @endif
                @error('bayar')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

              </div>

              <div class="col-6">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea id="catatan" wire:model="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                  placeholder="catatan transaksi (opsional)"></textarea>
                @error('catatan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>



            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      @else
        <h4 class="text-center p-4">pembayaran ini sudah lunas</h4>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="card">
      <x-modal modalTitle="Detail Pembayaran" eventName="detail-pembayaran" :showSubmit="false" modalSize="modal-lg">
        @if ($pemId != null && $isShow)
          <div class="table-responsive">
            <table class="table table-borderless">
              <tbody>
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">No Invoice:</th>
                  <td>: {{ $pembayaran->penjualan->no_invoice }}</td>
                </tr>
                {{-- <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Supplier:</th>
                  <td>: {{ $pembayaran->barangMasuk->supplier->nama }}</td>
                </tr> --}}
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Periode</th>
                  <td>: {{ $pembayaran->periode }}</td>
                </tr>
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Tanggal Pembayaran</th>
                  <td>: {{ $pembayaran->tanggal_pembayaran }}</td>
                </tr>
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Dibayarkan</th>
                  <td>: {{ Str::rupiah($pembayaran->jumlah_dibayar) }}</td>
                </tr>
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Sisa Hutang</th>
                  <td>: {{ Str::rupiah($pembayaran->sisa_hutang) }}</td>
                </tr>
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Metode Pembayaran</th>
                  <td>:
                    <span
                      class="badge rounded-pill {{ $pembayaran->metode_pembayaran == 'tunai' ? 'bg-success' : 'bg-primary' }}">{{ $pembayaran->metode_pembayaran }}</span>
                  </td>
                </tr>
                @if ($pembayaran->bank)
                  <tr>
                    <th class="text-start pe-3 align-top" style="width: 200px;">Bank</th>
                    <td>:
                      {{ $pembayaran->bank->bank . ' - ' . $pembayaran->bank->norek . ' a/n ' . $pembayaran->bank->atas_nama }}
                    </td>
                  </tr>
                @endif
                <tr>
                  <th class="text-start pe-3 align-top" style="width: 200px;">Keterangan</th>
                  <td>: {{ $pembayaran->keterangan }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        @endif
      </x-modal>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Periode</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Dibayarkan</th>
                <th scope="col">Sisa Hutang</th>
                <th scope="col">Metode Pembayaran</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($pembayarans as $item)
                <tr>
                  <td>{{ $item->periode }}</td>
                  <td>{{ $item->tanggal_pembayaran }}</td>
                  <td>{{ Str::rupiah($item->jumlah_dibayar) }}</td>
                  <td>{{ Str::rupiah($item->sisa_hutang) }}</td>
                  <td>
                    <span
                      class="badge rounded-pill {{ $item->metode_pembayaran == 'tunai' ? 'bg-success' : 'bg-primary' }}">{{ $item->metode_pembayaran }}</span>
                  </td>
                  <td>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                      @click="$wire.dispatchSelf('load-data', { id: {{ $item->id }} })"
                      data-bs-target="#detail-pembayaran">
                      <i class='bx bxs-info-circle'></i>
                    </button>

                    @if ($item->max)
                      <button wire:click="delete({{ $item->id }})" class="btn btn-danger"><i
                          class='bx bxs-trash'></i></button>
                    @endif

                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">-- belum ada data pembayaran --</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener('livewire:init', () => {
        // alert('oke')
        Livewire.on('show-modal-utang-data', () => {
          // alert("show modal")
          const modal = new bootstrap.Modal(document.getElementById('show-utang'));
          modal.show();
        });

        Livewire.on('hide-modal-utang-data', () => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('show-utang'));
          if (modal) {
            modal.hide();
          }
        });
      });
    </script>
  </div>
