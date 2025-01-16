<div class="row gap-3">

  <div class="row">
    <h1 class="text-capitalize">Pembelian {{ $context }} </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">data pembelian {{ $context }} </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="row">


            <div class="col-3 mb-3">
              <label for="bahan" class="form-label text-capitalize wajib">
                bahan
              </label>
              <select id="bahan"
                class="form-control choice @error('selectedBahan') is-invalid @enderror form-select"
                wire:model="selectedBahan">
                <option selected> -- pilih bahan --</option>
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
              <label for="satuan" class="form-label wajib">
                Satuan Bahan
              </label>
              <select id="satuan"
                class="form-control choice @error('selectedSatuan') is-invalid @enderror form-select"
                wire:model="selectedSatuan">
                <option selected> -- pilih satuan --</option>

                @forelse ($satuans as $satuan)
                  <option value="{{ $satuan->nama }}">{{ $satuan->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('selectedSatuan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>


            <div class="col-3 mb-3">
              <label for="qty" class="form-label text-capitalize wajib">qty</label>
              <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty"
                wire:model.live="qty" placeholder="qty {{ $context }}...">
              @error('qty')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>



            <div class="col-3 mb-3">
              <label for="hargaSatuan" class="form-label wajib">Harga Beli</label>
              <input type="number" wire:model.live="hargaSatuan"
                class="form-control @error('hargaSatuan') is-invalid @enderror" aria-describedby="hargaSatuanHelp">
              @if ($hargaSatuan != null)
                <div id="hargaSatuanHelp" class="form-text text-end">{{ Str::rupiah($hargaSatuan) }}</div>
              @endif
              @error('hargaSatuan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="subtotal" class="form-label wajib">Subtotal</label>
              <input type="number" wire:model="subtotal" class="form-control @error('subtotal') is-invalid @enderror"
                aria-describedby="subtotalHelp">
              @if ($subtotal != null)
                <div id="subtotalHelp" class="form-text text-end">{{ Str::rupiah($subtotal) }}</div>
              @endif
              @error('subtotal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="tanggal" class="form-label text-capitalize wajib">tanggal</label>
              <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                wire:model="tanggal" placeholder="tanggal {{ $context }}...">
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
        <livewire:PembelianBahan.pembelian-bahan-table />
      </div>
    </div>
  </div>
</div>
