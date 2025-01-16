<div class="row">
  @if ($isEdit)
    <style>
      .select-barang {
        display: none;
      }
    </style>
  @else
    <style>
      .barang-read {
        display: none;
      }
    </style>
  @endif

  @if ($isEdit)
    <div class="col-12">
      <div class="alert alert-warning" role="alert">
        <strong>Perhatian !</strong> <br>
        Lakukan edit stok dengan hati - hati agar tidak terjadi anomali stok.
      </div>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form wire:submit.prevent='{{ $isEdit ? 'update' : 'store' }}'>

        <div class="row">
          <div class="col-4 mb-3 barang-read">
            <label for="barang" class="form-label wajib">Barang</label>
            <input type="text" value="{{ $namaBarang }}" class="form-control"
              style="background-color: #e2dede !important;" readonly placeholder="0">
          </div>

          <div class="col-4 mb-3 select-barang" wire:ignore>
            <label for="barang" class="form-label wajib">Barang</label>
            <select wire:model.live="selectedBarang" id="selectedBarang"
              class="form-control choice @error('selectedBarang') is-invalid @enderror">
              @forelse ($barangs as $barang)
                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
              @empty
                <option>-- NO DATA --</option>
              @endforelse
            </select>
            @error('selectedBarang')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>



          <div class="col-4 mb-3">
            <label for="stok" class="form-label wajib">Stok</label>
            <input type="number" wire:model="stok" class="form-control @error('stok') is-invalid @enderror"
              placeholder="0">
            @error('stok')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-4 mb-3 d-flex align-items-end">
            @if ($isEdit)
              <button type="submit" class="btn btn-primary">Update</button>
              <button type="button" wire:click.prevent="batalEdit" class="btn ms-2 btn-danger">Batal</button>
            @else
              <button type="submit" class="btn btn-primary">Simpan</button>
            @endif
          </div>

        </div>

      </form>
    </div>
  </div>
  <script>
    document.addEventListener('livewire:init', () => {
      let barangSelect;

      function initTomSelect() {
        barangSelect = new TomSelect('#selectedBarang', {
          placeholder: '-- pilih barang --',
          allowEmptyOption: true,
          plugins: ['clear_button'],
          items: [],
          onChange: function(value) {
            if (value) {
              window.Livewire.dispatch('update-selected-barang', {
                value: parseInt(value)
              });
            }
          }
        });
      }

      function resetTomSelect() {
        if (barangSelect) {
          barangSelect.clear(true);
        }
      }

      initTomSelect();

      // Reset TomSelect
      Livewire.on('reset-choices', () => {
        console.log("reset-choices di trigger");
        resetTomSelect();
      });

      // Reinitialize TomSelect if needed (e.g., after Livewire updates)
      Livewire.on('reinit-tom-select', () => {
        if (barangSelect) barangSelect.destroy();
        initTomSelect();
      });
    });
  </script>


</div>
