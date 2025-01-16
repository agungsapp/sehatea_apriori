@props(['modalTitle', 'eventName', 'modalSize' => '', 'showSubmit' => true])
<div>
  <!-- Modal -->
  <div class="modal fade" id="{{ $eventName }}" tabindex="-1" aria-labelledby="{{ $eventName }}Label"
    aria-hidden="true">
    <div class="modal-dialog {{ $modalSize }}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="{{ $eventName }}Label">{{ $modalTitle }}</h5>
          <button @click="$dispatch('{{ $eventName }}-close')" type="button" class="btn-close"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {{ $slot }}
        </div>
        <div class="modal-footer">
          <button @click="$dispatch('{{ $eventName }}-close')" type="button" class="btn btn-secondary"
            data-bs-dismiss="modal">Tutup</button>
          @if ($showSubmit)
            <button @click="$dispatch('{{ $eventName }}')" type="button" class="btn btn-primary">Simpan</button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
