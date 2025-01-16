<div>
  @if ($supId != null)
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">No Invoice</th>
            <th scope="col">Total Harga</th>
            <th scope="col">Total Bayar</th>
            <th scope="col">Sisa Tagihan</th>
            <th scope="col">Jatuh Tempo</th>
            <th scope="col">Status Pembayaran</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($barangMasuks as $item)
            <tr>
              <th scope="row">{{ $loop->iteration }}</th>
              <td>{{ $item->no_invoice }}</td>
              <td>{{ Str::rupiah($item->total_harga) }}</td>
              <td>{{ Str::rupiah($item->total_bayar) }}</td>
              <td>{{ Str::rupiah($item->sisa_tagihan) }}</td>
              <td>{{ $item->jatuh_tempo }}</td>
              <td>{{ $item->status_pembayaran }}</td>
              <td>
                <a href="{{ route('bayar-utang', $item->id) }}" wire:navigate class="btn btn-info">detail</a>
              </td>
            </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  @else
    <div>
      <p class="text-center">NO_DATA</p>
    </div>
  @endif
</div>
