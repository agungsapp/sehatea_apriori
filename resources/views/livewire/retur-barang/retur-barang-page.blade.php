<div class="row gap-3">



  <div class="row">
    <h3 class="w-100 d-flex justify-content-between"><span>Retur Barang :</span>

    </h3>

  </div>

  <div class="row">
    <div class="card">
      <form wire:submit.prevent="save">
        <div class="card-body">

          <div class="col-12 mb-3">
            <label for="metodePembayaran" class="form-label">Metode Retur</label>
            <div class="col-12">
              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <!-- Radio Button Tunai -->
                <input type="radio" class="btn-check" id="btncheck1" name="metodePembayaran" autocomplete="off"
                  value="1" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btncheck1">Tunai</label>

                <!-- Radio Button Transfer -->
                <input type="radio" class="btn-check" id="btncheck2" name="metodePembayaran" autocomplete="off"
                  value="0" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btncheck2">Tukar</label>
              </div>
            </div>
          </div>

          <div class="row">

            <div class="{{ $isTunai ? 'col-3' : 'col-4' }} mb-3 select-barang" wire:ignore>
              <label for="barang" class="form-label">Barang</label>
              <select wire:model.live="selectedBarang" id="selectedBarang"
                class="form-control choice @error('selectedBarang') is-invalid @enderror">
                {{-- <option value="">-- pilih barang --</option> --}}
                @foreach ($barangs as $barang)
                  <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                @endforeach
              </select>
              @error('selectedBarang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="{{ $isTunai ? 'col-3' : 'col-4' }} mb-3" wire:ignore>
              <label for="selectedSupplier" class="form-label">
                Supplier
              </label>
              <select id="selectedSupplier"
                class="form-control choice @error('selectedSupplier') is-invalid @enderror form-select"
                wire:model="selectedSupplier">
                {{-- <option selected>-- pilih supplier --</option> --}}
                @forelse ($suppliers as $supplier)
                  <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedSupplier')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="{{ $isTunai ? 'col-3' : 'col-3' }} mb-3">
              <label for="jumlah" class="form-label">Qty Retur</label>
              <input type="number" wire:model.live="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                placeholder="qty">
              @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            @if ($isTunai)
              <div class="col-3 mb-3">
                <label for="hargaSatuan" class="form-label">Harga Beli</label>
                <input type="number" wire:model.live="hargaSatuan"
                  class="form-control @error('hargaSatuan') is-invalid @enderror" aria-describedby="hargaSatuanHelp">
                @if ($hargaSatuan != null)
                  <div id="hargaSatuanHelp" class="form-text text-end">{{ Str::rupiah($hargaSatuan) }}</div>
                @endif
                @error('hargaSatuan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            @endif

            <div class="col-12">
              <label for="catatan" class="form-label">Catatan</label>
              <textarea id="catatan" wire:model="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                placeholder="catatan transaksi (opsional)"></textarea>
              @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>
          @if ($isTunai)
            <div class="row mt-3">
              <div class="col-3 offset-9 mb-3">
                <label for="totalHarga" class="form-label">Total Harga</label>
                <input type="number" class="form-control @error('totalHarga') is-invalid @enderror"
                  style="background-color: #e2dede !important;" aria-describedby="totalHargaHelp" readonly
                  value="{{ $totalHarga }}">
                @if ($totalHarga != null)
                  <div id="totalHargaHelp" class="form-text text-end">{{ Str::rupiah($totalHarga) }}</div>
                @endif
                @error('totalHarga')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          @endif
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>

    </div>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        <div class="table-reponsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">barang</th>
                <th scope="col">supplier</th>
                <th scope="col">harga satuan</th>
                <th scope="col">jumlah</th>
                <th scope="col">total harga</th>
                <th scope="col">metode</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($returs as $retur)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td>{{ $retur->barang->nama_barang }}</td>
                  <td>{{ $retur->supplier->nama }}</td>
                  <td>{{ $retur->harga_satuan }}</td>
                  <td>{{ $retur->jumlah }}</td>
                  <td>{{ $retur->total_harga }}</td>
                  <td>{{ $retur->metode }}</td>
                </tr>
              @empty
              @endforelse

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">

    <script>
      document.addEventListener('livewire:init', () => {
        // alert('oke')
        Livewire.on('show-modal-utang-data', () => {
          // alert("show modal")
          const modal = new bootstrap.Modal(document.getElementById('show-utang'));
          modal.show();
        });

        Livewire.on('hide-modal-utang-data', () => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('show-utang'));
          if (modal) {
            modal.hide();
          }
        });
      });
    </script>


    <script>
      document.addEventListener('livewire:init', () => {
        let supplierSelect;
        let barangSelect;

        function initTomSelect() {
          supplierSelect = new TomSelect('#selectedSupplier', {
            placeholder: '-- pilih --',
            allowEmptyOption: true,
            plugins: ['clear_button'],
            items: [],
            onChange: function(value) {
              if (value) {
                window.Livewire.dispatch('update-selected-supplier', {
                  value: parseInt(value)
                });
              }
            }
          });

          barangSelect = new TomSelect('#selectedBarang', {
            placeholder: '-- pilih --',
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
          if (supplierSelect) {
            supplierSelect.clear(true);
          }
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
          if (supplierSelect) supplierSelect.destroy();
          if (barangSelect) barangSelect.destroy();
          initTomSelect();
        });
      });
    </script>
  </div>
