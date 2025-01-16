<div class="row gap-3">
  <div class="row">
    <h1>Laporan Stok</h1>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-5 col-lg-3 d-md-flex align-items-baseline gap-3 mb-3">
            <label for="startDate" class="form-label">Dari</label>
            <input type="date" class="form-control" wire:model.defer="startDate" id="startDate" />
          </div>

          <div class="col-12 col-md-5 col-lg-3 d-md-flex align-items-baseline gap-3 mb-3">
            <label for="endDate" class="form-label">Sampai</label>
            <input type="date" class="form-control" wire:model.defer="endDate" id="endDate" />
          </div>

          <div class="col-12 col-md-2 col-lg-3 mb-3">
            <button type="button" class="btn btn-primary" wire:click="terapkan">
              Terapkan
            </button>
            <button type="button" class="btn btn-secondary" wire:click="resetFilter">
              Reset
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        <livewire:laporan.stok.laporan-stok-table />
      </div>
    </div>
  </div>
</div>
