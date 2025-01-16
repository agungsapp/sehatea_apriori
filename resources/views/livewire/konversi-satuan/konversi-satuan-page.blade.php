<div class="row gap-3">

  <div class="row">
    <h1 class="text-capitalize">Data {{ $context }} </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah {{ $context }} baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="row">

            <div class="col-3 mb-3">
              <label for="bahan" class="form-label wajib">
                bahan Bahan
              </label>
              <select id="bahan"
                class="form-control choice @error('selectedBahan') is-invalid @enderror form-select"
                wire:model="selectedBahan">
                <option selected>-- pilih bahan --</option>
                @forelse ($bahans as $bahan)
                  <option value="{{ $bahan->id }}">{{ $bahan->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedBahan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="satuanAwal" class="form-label wajib">
                satuan Awal
              </label>
              <select id="satuanAwal" class="form-control choice @error('satuanAwal') is-invalid @enderror form-select"
                wire:model="satuanAwal">
                <option selected>-- pilih satuan --</option>

                @forelse ($satuans as $satuan)
                  <option value="{{ $satuan->nama }}">{{ $satuan->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('satuanAwal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="satuanTujuan" class="form-label wajib">
                satuan Tujuan
              </label>
              <select id="satuanTujuan"
                class="form-control choice @error('satuanTujuan') is-invalid @enderror form-select"
                wire:model="satuanTujuan">
                <option selected>-- pilih satuan --</option>

                @forelse ($satuans as $satuan)
                  <option value="{{ $satuan->nama }}">{{ $satuan->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('satuanTujuan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="rasio" class="form-label text-capitalize wajib">rasio</label>
              <input type="number" class="form-control @error('rasio') is-invalid @enderror" id="rasio"
                wire:model="rasio" placeholder="rasio {{ $context }}...">
              @error('rasio')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>

          <div class="row">
            <div class="col-12">
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
        <livewire:KonversiSatuan.konversi-satuan-table />
      </div>
    </div>
  </div>
</div>
