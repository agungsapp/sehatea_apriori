<div class="container-fluid mt-4">
  <div class="row mb-3">
    <div class="col">
      <h1>Daftar Harga</h1>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="alert alert-info" role="alert">
        <h4 class="alert-heading">Informasi !</h4>
        <p>Silahkan klik Sync Data untuk mengambil data otomatis dari daftar barang dan lakukan edit harga.</p>
      </div>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between">
            <div class="card-title">Tambah barang baru</div>
            <button type="button" class="btn btn-primary d-flex align-content-baseline"
              wire:click.prevent="handleSync">
              <i class='bx fs-4 bx-sync'></i>
              <span class="ms-2">Sync Data</span>
            </button>

          </div>
        </div>
        <form wire:submit.prevent="save">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-4 mb-3">
                <label for="kodeBarang" class="form-label wajib">Kode Barang</label>
                <input type="text" class="form-control @error('kodeBarang') is-invalid @enderror" id="kodeBarang"
                  wire:model="kodeBarang" placeholder="kode barang...">
                @error('kodeBarang')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6 col-md-8 mb-3">
                <label for="namaBarang" class="form-label wajib">Nama Barang</label>
                <input type="text" class="form-control @error('namaBarang') is-invalid @enderror" id="namaBarang"
                  wire:model="namaBarang" placeholder="Nama barang...">
                @error('namaBarang')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6 col-md-4 mb-3" wire:ignore>
                <label for="kategori" class="form-label">Kategori</label>
                <select id="kategori" class="form-control select2 @error('kategori') is-invalid @enderror form-select"
                  wire:model="selectedKategori">
                  <option selected>-- pilih kategori --</option>
                  @forelse ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                  @empty
                    <option>NO_DATA</option>
                  @endforelse
                </select>
                @error('kategori')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6 col-md-4 mb-3">
                <label for="hargaBeli" class="form-label wajib">Harga Beli</label>
                <input type="number" class="form-control @error('hargaBeli') is-invalid @enderror" id="hargaBeli"
                  wire:model="hargaBeli" placeholder="harga...">
                @error('hargaBeli')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-6 col-md-4 mb-3">
                <label for="hargaJual" class="form-label wajib">Harga Jual</label>
                <input type="number" class="form-control @error('hargaJual') is-invalid @enderror" id="hargaJual"
                  wire:model="hargaJual" placeholder="harga...">
                @error('hargaJual')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea id="catatan" wire:model="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                  placeholder="catatan (opsional)"></textarea>
                @error('catatan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
            @if ($isEdit)
              <button type="button" wire:click='batalEdit' class="btn btn-danger">Batal</button>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <livewire:tables.daftar-harga-table />


          {{-- <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col" class="text-capitalize">kode barang</th>
                <th scope="col" class="text-capitalize">nama barang</th>
                <th scope="col" class="text-capitalize">kategori</th>
                <th scope="col" class="text-capitalize">harga beli</th>
                <th scope="col" class="text-capitalize">harga jual</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($daftarHargas as $df)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td>{{ $df->kode_barang }}</td>
                  <td>{{ $df->nama_barang }}</td>
                  <td>{{ $df->id_kategori }}</td>
                  <td>{{ $df->harga_beli }}</td>
                  <td>{{ $df->harga_jual }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">-- belum ada data --</td>
                </tr>
              @endforelse

            </tbody>
          </table> --}}

        </div>
      </div>
    </div>
  </div>
</div>

@push('css')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2().on('select2:select', function(e) {
        @this.set('selectedKategori', $('#kategori').val());
        @this.set('selectedBrand', $('#brand').val());
      });
    });
  </script>
@endpush
