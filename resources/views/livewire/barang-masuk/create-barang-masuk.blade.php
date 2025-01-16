<div class="row gap-3">

  @if (!$isBaru)
    <style>
      .nama-barang {
        display: none;
      }
    </style>
  @else
    <style>
      .select-barang {
        display: none;
      }
    </style>
  @endif

  <div class="row">
    <h1>Input Data Barang Masuk</h1>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <h5>
              Tambah barang baru
            </h5>
          </div>
        </div>

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
              <label for="tanggal" class="form-label">
                Tanggal
              </label>
              <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                wire:model="tanggal">
              @error('tanggal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>


            <div class="col-3 mb-3" wire:ignore>
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
      </div>
    </div>
  </div>

  <div class="row">
    {{-- List item --}}
    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <h5>Barang masuk</h5>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 d-flex justify-content-end">
              <div class="form-check">
                <input wire:model.live="isBaru" class="form-check-input" type="checkbox" value=""
                  id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                  barang baru
                </label>
              </div>
            </div>
          </div>
          {{-- <div class="row">
            <div class="col-12">test isi check : {{ $isBaru == true ? 'oke' : 'tidak' }}</div>
          </div> --}}
          <div class="row">


            <div class="col-12 mb-3 nama-barang">
              <label for="namaBarang" class="form-label">Nama Barang</label>
              <input type="text" wire:model.live="namaBarang"
                class="form-control @error('namaBarang') is-invalid @enderror" placeholder="masukan nama barang...">
              @error('namaBarang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mb-3 select-barang" wire:ignore>
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

            <div class="col-12 mb-3">
              <label for="jumlah" class="form-label">Qty</label>
              <input type="number" wire:model.live="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                placeholder="qty">
              @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12 mb-3">
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

            <div class="col-12 mb-3">
              <label for="subtotal" class="form-label">Subtotal</label>
              <input type="number" wire:model="subtotal"
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

    <div class="col-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-flex align-items-center">
            <i class='bx fs-2 bxs-cart-add'></i><span class="ms-2"> keranjang</span>
          </div>
        </div>
        <div class="card-body">
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
                <td colspan="4" class="text-end"><strong>Total Harga</strong></td>
                <td colspan="2">{{ number_format($totalHarga) }}</td>
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
            <label for="metodePembayaran" class="form-label">Metode Pembayaran</label>
            <div class="col-12">
              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <!-- Radio Button Tunai -->
                <input type="radio" class="btn-check" id="btncheck1" name="metodePembayaran" autocomplete="off"
                  value="1" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btncheck1">Tunai</label>

                <!-- Radio Button Transfer -->
                <input type="radio" class="btn-check" id="btncheck2" name="metodePembayaran" autocomplete="off"
                  value="0" wire:model.live="isTunai">
                <label class="btn btn-outline-primary" for="btncheck2">Transfer</label>
              </div>
            </div>
          </div>

          <div class="row">

            @if (!$isTunai)

              <div class="col-6 mb-3">
                <label for="bank" class="form-label">
                  Bank
                </label>
                <select id="bank" class="form-control  @error('bank') is-invalid @enderror form-select"
                  wire:model="selectedBank">
                  <option selected>-- pilih bank --</option>
                  @forelse ($banks as $bank)
                    <option value="{{ $bank->id }}">
                      {{ $bank->bank . ' -  ' . $bank->norek . ' a/n ' . $bank->atas_nama }}</option>
                  @empty
                    <option>NO_DATA</option>
                  @endforelse
                </select>
                @error('bank')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            @endif

            <div class="col-6 mb-3">
              <label for="bayar" class="form-label">
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
    {{-- card total pembayaran --}}
    <div class="col-6">
      <div class="card ">
        <div class="card-body">
          <div class="row">
            <div class="col-4 mb-3">
              <label for="totalHarga" class="form-label">
                Total Harga
              </label>
              <input type="number" style="background-color: #e2dede !important;"
                class="form-control @error('totalHarga') is-invalid @enderror" id="totalHarga"
                wire:model="totalHarga" placeholder="total harga" readonly>
              @error('totalHarga')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-4 mb-3">
              <label for="statusPembayaran" class="form-label">
                Status Pembayaran
              </label>
              <input type="text" class="form-control @error('statusPembayaran') is-invalid @enderror"
                style="background-color: #e2dede !important;" id="statusPembayaran" wire:model="statusPembayaran"
                placeholder="-- status pembayaran --" readonly>
              @error('statusPembayaran')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            @if (!$isLunas)
              <div class="col-4 mb-3">
                <label for="jatuhTempo" class="form-label">
                  Jatuh Tempo
                </label>
                <input type="date" class="form-control @error('jatuhTempo') is-invalid @enderror" id="jatuhTempo"
                  wire:model="jatuhTempo">
                @error('jatuhTempo')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            @endif
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
          <button wire:click="simpan" class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Simpan' }}
          </button>

          <a href="{{ route('barang-masuk') }}" class="btn btn-info">Lihat Data</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">

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
