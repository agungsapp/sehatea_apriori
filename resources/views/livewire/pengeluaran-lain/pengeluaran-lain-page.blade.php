<div class="row gap-3">

  <div class="row">
    <h1 class="text-capitalize">Data {{ $context }} </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title text-capitalize">data {{ $context }} </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="row">

            <div class="col-3 mb-3">
              <label for="nama" class="form-label text-capitalize wajib">nama</label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                wire:model.live="nama" placeholder="nama {{ $context }}...">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="harga" class="form-label text-capitalize wajib">harga</label>
              <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga"
                wire:model.live="harga" placeholder="harga {{ $context }}...">
              @error('harga')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="jenisPengeluaran" class="form-label text-capitalize wajib">
                jenisPengeluaran
              </label>
              <select id="jenisPengeluaran"
                class="form-control choice @error('selectedJenis') is-invalid @enderror form-select"
                wire:model="selectedJenis">
                <option selected> -- pilih jenisPengeluaran --</option>
                @forelse ($jenisPengeluarans as $jenisPengeluaran)
                  <option value="{{ $jenisPengeluaran->id }}">{{ $jenisPengeluaran->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedJenis')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="sumberDana" class="form-label text-capitalize wajib">
                sumberDana
              </label>
              <select id="sumberDana"
                class="form-control choice @error('selectedDana') is-invalid @enderror form-select"
                wire:model="selectedDana">
                <option selected> -- pilih sumberDana --</option>
                @forelse ($sumberDanas as $sumberDana)
                  <option value="{{ $sumberDana->id }}">{{ $sumberDana->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedDana')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="tanggal" class="form-label text-capitalize wajib">tanggal</label>
              <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                wire:model="tanggal">
              @error('tanggal')
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
        <livewire:PengeluaranLain.pengeluaran-lain-table />
      </div>
    </div>
  </div>
</div>
