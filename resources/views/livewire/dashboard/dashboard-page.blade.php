<div class="row">

  <div class="row mb-3">
    <div class="col-12">
      <h4>Periode Data</h4>
      <div class="row">
        <div class="col-12 col-md-5 col-lg-3 d-md-flex align-items-baseline gap-3 mb-3">
          <label for="startDate" class="form-label">Dari</label>
          <input type="date" class="form-control" wire:model="startDate" id="startDate" placeholder="" />
        </div>

        <div class="col-12 col-md-5 col-lg-3 d-md-flex align-items-baseline gap-3 mb-3">
          <label for="endDate" class="form-label">Sampai</label>
          <input type="date" class="form-control" wire:model="endDate" id="endDate" placeholder="" />
        </div>

        <div class="col-12 col-md-2 col-lg-3 mb-3">
          <button type="button" class="btn btn-primary" wire:click="loadData">
            Terapkan
          </button>
          <button type="button" class="btn btn-secondary" wire:click="resetFilter">
            Reset
          </button>
        </div>
      </div>


    </div>
  </div>

  <div class="row">

    {{-- jumlah transaksi --}}
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
      <div class="card border-0 shadow">
        <div class="card-body">
          <div class="row d-block d-xl-flex align-items-center">
            <div
              class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
              <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                  </path>
                </svg>
              </div>
              <div class="d-sm-none">
                <h2 class="h5">Transaksi Bulan Ini</h2>
                <h3 class="fw-extrabold mb-1">{{ $totalTransaksi }}</h3>
              </div>
            </div>
            <div class="col-12 col-xl-7 px-xl-0">
              <div class="d-none d-sm-block">
                <h2 class="h6 text-gray-400 mb-0">Transaksi Bulan Ini</h2>
                <h3 class="fw-extrabold mb-2">{{ $totalTransaksi }}</h3>
              </div>
              <small class="d-flex align-items-center text-gray-500">
                {{ $this->formattedDateRange }}
              </small>
              <div class="small d-flex mt-1">
                {{-- <div>peningkatan dari bulan lalu <svg class="icon icon-xs text-success" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                      clip-rule="evenodd"></path>
                  </svg><span class="text-success fw-bolder">22%</span></div> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4">
      <div class="card border-0 shadow">
        <div class="card-body">
          <div class="row d-block d-xl-flex align-items-center">
            <div
              class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
              <div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="d-sm-none">
                <h2 class="fw-extrabold h5"> Nilai Transaksi</h2>
                <h3 class="mb-1">{{ Str::rupiah($omset) }}</h3>
              </div>
            </div>
            <div class="col-12 col-xl-7 px-xl-0">
              <div class="d-none d-sm-block">
                <h2 class="h6 text-gray-400 mb-0"> Nilai Transaksi</h2>
                <h3 class="fw-extrabold mb-2">{{ Str::rupiah($omset) }}</h3>
              </div>
              <small class="text-gray-500">
                {{ $this->formattedDateRange }}
              </small>
              <div class="small d-flex mt-1">
                {{-- <div>peningkatan dari bulan lalu <svg class="icon icon-xs text-success" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                      clip-rule="evenodd"></path>
                  </svg><span class="text-success fw-bolder">4%</span></div> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4">
      <div class="card border-0 shadow">
        <div class="card-body">
          <div class="row d-block d-xl-flex align-items-center">
            <div
              class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
              <div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                    clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="d-sm-none">
                <h2 class="fw-extrabold h5">Pengeluaran Toko</h2>
                <h3 class="mb-1">{{ Str::rupiah($pengeluaran) }}</h3>
              </div>
            </div>
            <div class="col-12 col-xl-7 px-xl-0">
              <div class="d-none d-sm-block">
                <h2 class="h6 text-gray-400 mb-0">Pengeluaran Toko</h2>
                <h3 class="fw-extrabold mb-2">{{ Str::rupiah($pengeluaran) }}</h3>
              </div>
              <small class="d-flex align-items-center text-gray-500">
                {{ $this->formattedDateRange }}
              </small>
              <div class="small d-flex mt-1">
                {{-- <div>Penurunan dari bulan lalu <svg class="icon icon-xs text-danger" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"></path>
                  </svg><span class="text-danger fw-bolder">2%</span></div> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>





</div>
