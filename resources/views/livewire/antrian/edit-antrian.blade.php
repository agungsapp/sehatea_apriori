<div class="row gap-3">

  <div class="row">
    <h1>Edit antrian : <strong class="text-capitalize"> {{ $namaPelanggan ?? '' }}</strong> </h1>
  </div>

  @if (!$isInstansi)
    <style>
      .is-instansi {
        display: none;
      }
    </style>
  @else
    <style>
      .non-instansi {
        display: none;
      }
    </style>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <h5>
              Edit Antrian
            </h5>
          </div>
        </div>

        <div class="card-body">
          <div class="row">

            <div class="col-3 mb-3">
              <label for="tanggalMasuk" class="form-label wajib">
                Tanggal Masuk
              </label>
              <input type="datetime-local" class="form-control @error('tanggalMasuk') is-invalid @enderror"
                id="tanggalMasuk" wire:model="tanggalMasuk">
              @error('tanggalMasuk')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="status" class="form-label wajib">
                Status
              </label>
              <select class="form-select" wire:model="status" id="status" aria-label="Default select example">
                <option selected> -- pilih status --</option>
                <option value="antri">Antri</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
                <option value="batal">Batal</option>
              </select>
              @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- is instansi --}}
            <div class="col-3 mb-3">
              <label for="tipe_pelanggan" class="form-label wajib">Tipe Pelanggan</label>
              <div class="col-12">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                  <!-- Radio Button Tunai -->
                  <input type="radio" class="btn-check" id="btncheck1" name="tipe_pelanggan" autocomplete="off"
                    value="0" wire:model.live="isInstansi">
                  <label class="btn btn-outline-primary" for="btncheck1">Non Instansi</label>

                  <!-- Radio Button Transfer -->
                  <input type="radio" class="btn-check" id="btncheck2" name="tipe_pelanggan" autocomplete="off"
                    value="1" wire:model.live="isInstansi">
                  <label class="btn btn-outline-primary" for="btncheck2">Instansi</label>
                </div>
              </div>
            </div>
          </div>

          {{-- data konsumen --}}
          <div class="row ">
            <div class="col-3 mb-3 is-instansi" wire:ignore>
              <label for="instansi" class="form-label wajib">
                Instansi
              </label>
              <select id="instansi"
                class="form-control choice @error('selectedInstansi') is-invalid @enderror form-select"
                wire:model="selectedInstansi">
                {{-- <option selected>-- pilih instansi --</option> --}}
                @forelse ($instansis as $instansi)
                  <option value="{{ $instansi->id }}">{{ $instansi->nama_instansi }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedInstansi')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3 non-instansi">
              <label for="namaPelanggan" class="form-label wajib">Nama Pelanggan</label>
              <input type="text" class="form-control @error('namaPelanggan') is-invalid @enderror"
                wire:model="namaPelanggan" placeholder="nama konsumen ...">
              @error('namaPelanggan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="kendaraan" class="form-label wajib">Kendaraan</label>
              <input type="text" class="form-control @error('kendaraan') is-invalid @enderror" wire:model="kendaraan"
                placeholder="nama kendaraan ...">
              @error('kendaraan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="nopol" class="form-label wajib">Nomor Polisi</label>
              <input type="text" class="form-control @error('nopol') is-invalid @enderror" wire:model.live="nopol"
                placeholder="nomor polisi ...">
              @error('nopol')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-3 mb-3">
              <label for="telepon" class="form-label wajib">Nomor Pelanggan</label>
              <input type="text" class="form-control @error('telepon') is-invalid @enderror" wire:model="telepon"
                placeholder="nomor pelanggan ...">
              @error('telepon')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="catatan" class="form-label ">Catatan</label>
              <textarea id="catatan" wire:model="catatan" rows="3"
                class="form-control @error('catatan') is-invalid @enderror" placeholder="catatan transaksi (opsional)"></textarea>
              @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button wire:click="update" class="btn btn-primary">
            Update
          </button>

          <a href="{{ route('antrian') }}" class="btn btn-info">Data Antrian</a>
        </div>
      </div>
    </div>
  </div>






  @if (session()->has('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif


  <script>
    document.addEventListener('livewire:init', () => {
      // let supplierSelect;
      let barangSelect;
      let jasaSelect;

      function initTomSelect() {
        barangSelect = new TomSelect('#selectedBarang', {
          placeholder: '-- pilih --',
          allowEmptyOption: true,
          plugins: ['clear_button'],
          onChange: function(value) {
            if (value) {
              window.Livewire.dispatch('update-selected-barang', {
                value: parseInt(value)
              });
            }
          }
        });
        jasaSelect = new TomSelect('#selectedJasa', {
          placeholder: '-- pilih jasa --',
          allowEmptyOption: true,
          plugins: ['clear_button'],
          onChange: function(value) {
            if (value) {
              window.Livewire.dispatch('update-selected-jasa', {
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
        if (jasaSelect) {
          jasaSelect.clear(true);
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
        if (jasaSelect) jasaSelect.destroy();
        initTomSelect();
      });
    });
  </script>



</div>
