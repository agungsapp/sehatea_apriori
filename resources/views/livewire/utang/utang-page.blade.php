<div class="row gap-3">



  <div class="row">
    <h1>Data Hutang By Supplier</h1>
  </div>

  <div class="row">
    <div class="card">
      <x-modal modalTitle="Data utang" eventName="show-utang" modalSize="modal-xl">
        @livewire('utang.show-utang')
      </x-modal>
      <div class="card-body">
        <livewire:utang.utang-table />
      </div>
    </div>
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
  </div>
