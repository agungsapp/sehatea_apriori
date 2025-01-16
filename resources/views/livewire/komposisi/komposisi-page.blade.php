<div class="row gap-3">
  <div class="row">
    <h1>Data {{ $context }}</h1>
  </div>

  <div class="row">
    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Tambah {{ $context }} baru</div>
        </div>
        <form wire:submit.prevent="addToKomposisi">
          <div class="card-body">
            <div class="row">
              <div class="col-12 mb-3">
                <label for="produk" class="form-label text-capitalize wajib">produk</label>
                <select id="produk"
                  class="form-control choice @error('selectedProduk') is-invalid @enderror form-select"
                  wire:model="selectedProduk">
                  <option selected>-- pilih produk --</option>
                  @foreach ($produks as $produk)
                    <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                  @endforeach
                </select>
                @error('selectedProduk')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 mb-3">
                <label for="bahan" class="form-label text-capitalize wajib">bahan</label>
                <select id="bahan"
                  class="form-control choice @error('selectedBahan') is-invalid @enderror form-select"
                  wire:model.live="selectedBahan">
                  <option selected>-- pilih bahan --</option>
                  @foreach ($bahans as $bahan)
                    <option value="{{ $bahan->id }}">{{ $bahan->nama }}</option>
                  @endforeach
                </select>
                @error('selectedBahan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6 mb-3">
                <label for="takaran" class="form-label text-capitalize wajib">takaran</label>
                <input type="number" class="form-control @error('takaran') is-invalid @enderror" id="takaran"
                  wire:model="takaran" placeholder="takaran ...">
                @error('takaran')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6 mb-3">
                <label for="satuan" class="form-label text-capitalize">satuan</label>
                <input type="text" class="form-control" id="satuan" wire:model="satuan" readonly>
              </div>

              <!-- Manual Input Section -->
              @if ($manualInput)
                <div class="col-12">
                  <div class="alert alert-info">
                    Data pembelian tidak tersedia. Silahkan input harga satuan manual.
                  </div>
                </div>

                <div class="col-6 mb-3">
                  <label for="hargaSatuanManual" class="form-label text-capitalize wajib">
                    Harga Satuan Manual
                  </label>
                  <input type="number" class="form-control @error('hargaSatuanManual') is-invalid @enderror"
                    id="hargaSatuanManual" wire:model="hargaSatuanManual" placeholder="Masukkan harga satuan...">
                  @error('hargaSatuanManual')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-6 mb-3">
                  <label for="satuanAwalManual" class="form-label text-capitalize wajib">
                    Satuan Awal
                  </label>
                  <input type="text" class="form-control @error('satuanAwalManual') is-invalid @enderror"
                    id="satuanAwalManual" wire:model="satuanAwalManual" placeholder="Masukkan satuan awal...">
                  @error('satuanAwalManual')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              @endif

            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Tambah ke Komposisi</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-flex justify-content-between align-items-center">
            <span>Komposisi Produk</span>
            <button type="button" class="btn btn-primary" wire:click="save"
              @if (empty($komposisi)) disabled @endif>
              Simpan Komposisi
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nama Bahan</th>
                  <th scope="col">Takaran</th>
                  <th scope="col">HPP</th>
                  <th scope="col">Input Manual</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($komposisi as $index => $item)
                  <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item['nama_bahan'] }}</td>
                    <td>{{ $item['takaran'] }} {{ $item['satuan'] }}</td>
                    <td>{{ number_format($item['hpp'], 0, ',', '.') }}</td>
                    <td>
                      @if ($item['is_manual'])
                        <span class="badge bg-info">Manual</span>
                        <small>
                          ({{ number_format($item['harga_satuan'], 0, ',', '.') }}/{{ $item['satuan_awal'] }})
                        </small>
                      @else
                        <span class="badge bg-success">Auto</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm"
                        wire:click="removeFromKomposisi({{ $index }})">
                        Hapus
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">Belum ada bahan</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Total HPP:</td>
                  <td colspan="3" class="fw-bold">
                    {{ number_format($totalHpp, 0, ',', '.') }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
