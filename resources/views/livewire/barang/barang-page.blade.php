<div class="row gap-3">



  <div class="row">
    <h1>Data Barang</h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah barang baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">

          <div class="row">
            <div class="col-6 col-md-4 mb-3">
              <label for="kodeBarang" class="form-label wajib">
                Kode Barang
              </label>
              <input type="text" class="form-control @error('kodeBarang') is-invalid @enderror" id="kodeBarang"
                wire:model="kodeBarang" placeholder="Cth. VBT-001">
              @error('kodeBarang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>


            <div class="col-6 col-md-8 mb-3">
              <label for="namaBarang" class="form-label wajib">
                Nama Barang
              </label>
              <input type="text" class="form-control @error('namaBarang') is-invalid @enderror" id="namaBarang"
                wire:model="namaBarang" placeholder="Nama barang...">
              @error('namaBarang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror


            </div>

            <div class="col-6 col-md-4 mb-3">
              <label for="noSeri" class="form-label">
                No Seri
              </label>
              <input type="text" class="form-control @error('noSeri') is-invalid @enderror" id="noSeri"
                wire:model="noSeri" placeholder="Cth. 098765xxxxx">
              @error('noSeri')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-6 col-md-4 mb-3" wire:ignore>
              <label for="kategori" class="form-label">
                Kategori
              </label>
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


            <div class="col-6 col-md-4 mb-3" wire:ignore>
              <label for="brand" class="form-label">
                Brand
              </label>
              <select id="brand" class="form-control select2 @error('brand') is-invalid @enderror form-select"
                wire:model="selectedBrand">
                <option selected>-- pilih brand --</option>
                @forelse ($brands as $brand)
                  <option value="{{ $brand->id }}">{{ $brand->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('brand')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="catatan" class="form-label">Catatan</label>
              <textarea id="catatan" wire:model="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                placeholder="catatan jasa (opsional)"></textarea>
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

  <div class="row">
    <div class="card">
      <div class="card-body">
        <livewire:barang.barang-table />
      </div>
    </div>
  </div>
</div>

{{-- @push('css')
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script')
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></scrip>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script>
				$(document).ready(function() {
						console.log("oke aman")
				})
				$(document).ready(function() {
						$('.select2').select2().on('select2:select', function(e) {
								@this.set('selectedKategori', $('.select2').select2("val"));
						});
				});
		</script>
@endpush --}}
