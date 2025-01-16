<div class="row gap-3">



  <div class="row">
    <h1>Data Jasa </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah Jasa baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">


          <div class="row">
            <div class=" col-3 mb-3">
              <label for="nama" class="form-label wajib">Nama</label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                wire:model="nama" placeholder="Nama jasa...">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="kode" class="form-label">Kode Jasa</label>
              <input type="text" wire:model.live="kode" class="form-control @error('kode') is-invalid @enderror"
                aria-describedby="kodeHelp">

              @error('kode')
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
        <livewire:jasa.jasa-table />
      </div>
    </div>
  </div>
</div>
