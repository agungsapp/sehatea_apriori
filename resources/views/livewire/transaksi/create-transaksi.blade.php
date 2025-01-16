<div class="row gap-3">

  <div class="row">
    <h1>Buat transaksi baru </h1>
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
              Tambah transaksi baru
            </h5>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-3 mb-3">
              <label for="noInvoice" class="form-label wajib">No Invoice</label>
              <input type="text" class="form-control @error('noInvoice') is-invalid @enderror" wire:model="noInvoice"
                placeholder="Auto" aria-describedby="noInvoiceHelp">
              <div id="noInvoiceHelp" class="form-text">sesuaikan no invoice.</div>
              @error('noInvoice')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

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
              <label for="tanggalKeluar" class="form-label wajib">
                Tanggal Keluar
              </label>
              <input type="datetime-local" class="form-control @error('tanggalKeluar') is-invalid @enderror"
                id="tanggalKeluar" wire:model="tanggalKeluar">
              @error('tanggalKeluar')
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
      </div>
    </div>
  </div>

  <div class="row">
    {{-- List sparepart --}}
    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <h5>Sparepart</h5>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 mb-3 select-barang" wire:ignore>
              <label for="barang" class="form-label">Barang</label>
              <select wire:model.live="selectedBarang" id="selectedBarang"
                class="form-control choice @error('selectedBarang') is-invalid @enderror">
                <option value="">-- pilih barang --</option>
                @foreach ($barangs as $barang)
                  <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                @endforeach
              </select>
              @error('selectedBarang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mb-3">
              <label for="jumlah" class="form-label">Qty</label>
              <input type="number" wire:model.live="jumlah"
                class="form-control @error('jumlah') is-invalid @enderror" placeholder="qty">
              @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mb-3">
              <label for="hargaSatuan" class="form-label">Harga Jual</label>
              @if ($lastPrice != null)
                <div id="hargaSatuanHelp" class="form-text text-start">Harga beli terakhir dari supplier
                  adalah <strong>{{ Str::rupiah($lastPrice->harga_satuan) }}</strong></div>
              @endif
              <input type="number" wire:model.live.debouce.500ms="hargaSatuan"
                class="form-control @error('hargaSatuan') is-invalid @enderror" aria-describedby="hargaSatuanHelp">
              @error('hargaSatuan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @if ($hargaSatuan != null)
                <div id="hargaSatuanHelp" class="form-text text-end">{{ Str::rupiah($hargaSatuan) }}</div>
              @endif

            </div>

            <div class="col-12 mb-3">
              <label for="subtotal" class="form-label">Subtotal</label>
              <input type="number" wire:model.live.debounce.900ms="subtotal"
                class="form-control @error('subtotal') is-invalid @enderror" aria-describedby="subtotalHelp">
              @if ($subtotal != null)
                <div id="subtotalHelp" class="form-text text-end">{{ Str::rupiah($subtotal) }}</div>
              @endif
              @error('subtotal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row">
            <button wire:click="tambahBarang" class="btn btn-outline-primary" type="button">
              + tambah barang
            </button>
          </div>
        </div>
      </div>
    </div>
    {{-- keranjang sparepart --}}
    <div class="col-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-flex align-items-center">
            <i class='bx fs-2 bxs-cart-add'></i><span class="ms-2">sparepart</span>
          </div>
        </div>
        <div style="max-height: 550px; overflow-y: scroll;" class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Barang</th>
                <th scope="col">Qty</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Subtotal</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($keranjangBarang as $index => $item)
                <tr>
                  <th scope="row">{{ $index + 1 }}</th>
                  <td>{{ $item['nama_barang'] }}</td>
                  <td>{{ $item['jumlah'] }}</td>
                  <td>{{ Str::rupiah($item['harga_satuan']) }}</td>
                  <td>{{ Str::rupiah($item['subtotal']) }}</td>
                  <td>
                    <button wire:click="hapusBarangDariKeranjang({{ $index }})" class="btn btn-danger btn-sm">
                      <i class='bx bx-trash'></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Keranjang kosong</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end"><strong>Total Sparepart</strong></td>
                <td colspan="2">{{ number_format($totalSparepart) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- List jasa --}}
  <div class="row">
    {{-- sectoion jasa --}}
    <div class="col-4">
      <div class="card h-100">
        <div class="card-header">
          <div class="card-title">
            <h5>Jasa</h5>
          </div>
        </div>
        <div class="card-body">
          <div class="row">

            <div class="col-12 mb-3 select-jasa" wire:ignore>
              <label for="jasa" class="form-label">Jasa</label>
              <select wire:model.live="selectedJasa" id="selectedJasa"
                class="form-control choice @error('selectedJasa') is-invalid @enderror">
                <option value="">-- pilih jasa --</option>
                @foreach ($jasas as $jasa)
                  <option value="{{ $jasa->id }}">{{ $jasa->nama }}</option>
                @endforeach
              </select>
              @error('selectedJasa')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- <div class="col-12 mb-3">
              <label for="jasa" class="form-label">Keterangan Jasa</label>
              <input type="text" wire:model.live="jasa" class="form-control @error('jasa') is-invalid @enderror"
                placeholder="Cth. ganti oli">
              @error('jasa')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div> --}}

            <div class="col-12 mb-3">
              <label for="hargaJasa" class="form-label">Harga Jasa</label>
              <input type="number" wire:model.live.debounce.900ms="hargaJasa"
                class="form-control @error('hargaJasa') is-invalid @enderror" placeholder="harga jasa ..."
                aria-describedby="hargaJasaHelp">
              @if ($hargaJasa != null)
                <div id="hargaJasaHelp" class="form-text text-end">{{ Str::rupiah($hargaJasa) }}</div>
              @endif
              @error('hargaJasa')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row">
            <button wire:click="tambahJasa" class="btn btn-outline-primary" type="button">
              + tambah
            </button>
          </div>
        </div>
      </div>
    </div>
    {{-- keranjang jasa --}}
    <div class="col-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-flex align-items-center">
            <i class='bx fs-2 bxs-cart-add'></i><span class="ms-2"> jasa </span>
          </div>
        </div>
        <div style="max-height: 550px; overflow-y: scroll;" class="card-body ">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Jasa</th>
                <th scope="col">Harga</th>
                <th scope="col" style="width: 10%;">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($keranjangJasa as $index => $item)
                <tr>
                  <th scope="row">{{ $index + 1 }}</th>
                  <td>{{ $item['nama'] }}</td>
                  <td>{{ Str::rupiah($item['harga_jasa']) }}</td>
                  <td>
                    <button wire:click="hapusJasaDariKeranjang({{ $index }})" class="btn btn-danger btn-sm">
                      <i class='bx bx-trash'></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center">Keranjang kosong</td>
                </tr>
              @endforelse

            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end"><strong>Total Jasa</strong></td>
                <td colspan="2">{{ number_format($totalJasa) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- card metode pembayaran --}}
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <label for="metodePembayaran" class="form-label wajib">Metode Pembayaran</label>
            <div class="col-12">
              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <!-- Radio Button Tunai -->
                <input type="radio" class="btn-check" id="btnTunai" name="metodePembayaran" autocomplete="off"
                  value="1" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btnTunai">Tunai</label>

                <!-- Radio Button Transfer -->
                <input type="radio" class="btn-check" id="btnTransfer" name="metodePembayaran" autocomplete="off"
                  value="0" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btnTransfer">Transfer</label>
              </div>
            </div>
          </div>

          <div class="row">

            @if (!$isTunai)

              <div class="col-6 mb-3">
                <label for="bank" class="form-label wajib">
                  Bank
                </label>
                <select id="bank" class="form-control  @error('selectedBank') is-invalid @enderror form-select"
                  wire:model="selectedBank">
                  <option selected>-- pilih bank --</option>
                  @forelse ($banks as $bank)
                    <option value="{{ $bank->id }}">
                      {{ $bank->bank . ' -  ' . $bank->norek . ' a/n ' . $bank->atas_nama }}</option>
                  @empty
                    <option>NO_DATA</option>
                  @endforelse
                </select>
                @error('selectedBank')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            @endif

            <div class="col-6 mb-3">
              <label for="bayar" class="form-label ">
                Di Bayarkan
              </label>
              <input type="number"
                class="form-control @error('bayar') is-invalid @enderror @if (isset($customErrors['bayar'])) is-invalid @endif"
                id="bayar" wire:model.live="bayar" placeholder="0" aria-describedby="bayarHelp">
              @if ($bayar != null)
                <div id="bayarHelp" class="form-text text-end">{{ Str::rupiah($bayar) }}</div>
              @endif
              @error('bayar')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @if (isset($customErrors['bayar']))
                <div class="invalid-feedback">{{ $customErrors['bayar'] }}</div>
              @endif
              <div class="invalid-feedback">ga valid</div>
            </div>

          </div>


        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6 mb-3">
              <label for="totalSparepart" class="form-label ">
                Total Sparepart
              </label>
              <input type="number" class="form-control @error('totalSparepart') is-invalid @enderror"
                style="background-color: #e2dede !important;" id="totalSparepart" wire:model="totalSparepart"
                placeholder="total sparepart">
              @error('totalSparepart')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-6 mb-3">
              <label for="totalJasa" class="form-label ">
                Total Jasa
              </label>
              <input type="number" class="form-control @error('totalJasa') is-invalid @enderror" id="totalJasa"
                style="background-color: #e2dede !important;" wire:model="totalJasa" placeholder="total jasa">
              @error('totalJasa')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-4 mb-3">
              <label for="diskon" class="form-label ">
                Diskon
              </label>
              <input type="number" class="form-control @error('diskon') is-invalid @enderror" id="diskon"
                wire:model.live="diskon" placeholder="(opsional)">
              @error('diskon')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-4 mb-3">
              <label for="ppn" class="form-label ">
                PPN %
              </label>
              <input type="number" class="form-control @error('ppn') is-invalid @enderror" id="ppn"
                wire:model.live="ppn" placeholder="dalam %">
              @error('ppn')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-4 mb-3">
              <label for="pph" class="form-label ">
                PPH %
              </label>
              <input type="number" class="form-control @error('pph') is-invalid @enderror" id="pph"
                wire:model.live="pph" placeholder="dalam %">
              @error('pph')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        {{-- total atas --}}
        <div class="row">


        </div>

        {{-- total  tengahh --}}
        <div class="row">

        </div>
        {{-- total bawah --}}
        <div class="row">
          <div class="col-3 mb-3">
            <label for="totalHarga" class="form-label ">
              Total Harga
            </label>
            <input type="number" style="background-color: #e2dede !important;"
              class="form-control @error('totalHarga') is-invalid @enderror" id="totalHarga" wire:model="totalHarga"
              placeholder="total harga" readonly>
            @error('totalHarga')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-3 mb-3">
            <label for="statusPembayaran" class="form-label ">
              Status Pembayaran
            </label>
            <input type="text" class="form-control @error('statusPembayaran') is-invalid @enderror"
              style="background-color: #e2dede !important;" id="statusPembayaran" wire:model="statusPembayaran"
              placeholder="-- status pembayaran --" readonly>
            @error('statusPembayaran')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>


        </div>
      </div>
      <div class="card-footer">
        <button wire:click="simpan" class="btn btn-primary">
          {{ $isEdit ? 'Update' : 'Simpan' }}
        </button>

        <a href="{{ route('transaksi') }}" class="btn btn-info">History Transaksi</a>
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
