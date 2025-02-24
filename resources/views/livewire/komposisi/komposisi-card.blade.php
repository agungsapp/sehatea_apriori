<div class="row p-5">
  @forelse ($produks as $produk)
    {{-- @dd($produk->hpp) --}}
    <div class="col-4 mb-5">
      <div class="card h-100 w-100">
        <div class="card-body">
          <h5 class="card-title">{{ $produk->nama }}</h5>
          <h6 class="card-subtitle text-muted mb-2">{{ Str::rupiah($produk->harga) }}</h6>
          {{-- list bahan --}}
          <ul class="list-group list-group-flush">
            @forelse ($produk->komposisi as $k)
              <li class="list-group-item d-flex justify-content-between">
                <span class="fw-bold">{{ $k->bahan->nama }}</span>
                <span>{{ Str::rupiah($k->takaran * $k->bahan->harga_satuan) }}</span>
              </li>
            @empty
              <li class="list-group-item">masih kosong !</li>
            @endforelse
          </ul>

        </div>
        <div class="card-footer">
          HPP :
          {{ Str::rupiah(
              $produk->komposisi->sum(function ($k) {
                  return $k->takaran * $k->bahan->harga_satuan;
              }),
          ) }}
        </div>
      </div>
    </div>
  @empty
  @endforelse

</div>
