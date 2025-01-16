<div class="row gap-3">

  <div class="row">
    <h1>Data Pengeluaran </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah pengeluaran baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">

          <div class="row">

            <div class="col-3 mb-3">
              <label for="noInvoice" class="form-label">No Invoice</label>
              <input type="text" class="form-control @error('noInvoice') is-invalid @enderror" wire:model="noInvoice"
                placeholder="Auto" aria-describedby="noInvoiceHelp">
              <div id="noInvoiceHelp" class="form-text">sesuaikan no invoice.</div>
              @error('noInvoice')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="nama" class="form-label wajib">Nama</label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                wire:model="nama" placeholder="Nama ...">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="harga" class="form-label wajib">harga</label>
              <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga"
                wire:model="harga" placeholder="harga ...">
              @error('harga')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="tanggal" class="form-label wajib">tanggal</label>
              <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                wire:model="tanggal" placeholder="tanggal ...">
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
        {{-- <livewire:brand.brand-table /> --}}
        <livewire:transaksi.pengeluaran.pengeluaran-table />
      </div>
    </div>
  </div>
</div>
