<div>
  <div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <h3 class="fw-bold">Analisis Pola Pembelian</h3>
        <p class="text-muted">
          Analisis menggunakan Algoritma Apriori untuk menemukan pola pembelian produk yang sering terjadi bersamaan
        </p>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" wire:model="startDate" class="form-control">
            @error('startDate')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" wire:model="endDate" class="form-control">
            @error('endDate')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Minimum Support (0.1-1)</label>
            <input type="number" step="0.1" wire:model="minSupport" class="form-control">
            @error('minSupport')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Minimum Confidence (0.1-1)</label>
            <input type="number" step="0.1" wire:model="minConfidence" class="form-control">
            @error('minConfidence')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="mt-3">
          <button wire:click="analyze" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading wire:target="analyze">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Menganalisis...
            </span>
            <span wire:loading.remove wire:target="analyze">
              Analisis
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session()->has('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Results -->
    @if (!empty($results))
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Hasil Analisis</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
                <tr>
                  <th class="text-nowrap">Jika Membeli</th>
                  <th class="text-nowrap">Maka Membeli</th>
                  <th class="text-nowrap">Support</th>
                  <th class="text-nowrap">Confidence</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($results as $rule)
                  <tr>
                    <td>{{ implode(', ', $rule['antecedent']) }}</td>
                    <td>{{ implode(', ', $rule['consequent']) }}</td>
                    <td>{{ number_format($rule['support'] * 100, 2) }}%</td>
                    <td>{{ number_format($rule['confidence'] * 100, 2) }}%</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Interpretasi -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Interpretasi Hasil</h5>
        </div>
        <div class="card-body">
          <p>Dari hasil analisis di atas dapat diinterpretasikan:</p>
          <ul>
            @foreach ($results as $rule)
              <li class="mb-2">
                Jika pelanggan membeli <strong>{{ implode(', ', $rule['antecedent']) }}</strong>,
                maka {{ number_format($rule['confidence'] * 100, 2) }}% kemungkinan juga akan membeli
                <strong>{{ implode(', ', $rule['consequent']) }}</strong>.
                Pola ini muncul dalam {{ number_format($rule['support'] * 100, 2) }}% dari total transaksi.
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif
  </div>
</div>
