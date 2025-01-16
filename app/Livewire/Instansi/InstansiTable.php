<?php

namespace App\Livewire\Instansi;

use App\Models\Instansi;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class InstansiTable extends PowerGridComponent
{
    use LivewireAlert;
    public string $tableName = 'instansi-table-pil3wm-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            // PowerGrid::show_source(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Hapus (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('btn btn-danger')
                ->attributes([
                    'wire:click' => 'confirmBulkDelete',
                    'style' => 'position: relative;'
                ]),
        ];
    }

    public function confirmBulkDelete()
    {
        // Check if any rows are selected
        if (empty($this->checkboxValues)) {
            $this->alert('info', 'Tidak ada supplier yang dipilih.', [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);
            return;
        }

        // Show confirmation alert
        $this->alert('warning', 'Apakah anda yakin ingin menghapus semua data yang terpilih ?', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'bulkDeleteConfirmed',
        ]);
    }

    #[On('bulkDeleteConfirmed')]
    public function bulkDeleteConfirmed()
    {
        if ($this->checkboxValues) {
            // Delete selected users
            Instansi::destroy($this->checkboxValues);

            // Clear checkbox selections
            $this->js('window.pgBulkActions.clearAll()');

            // Show success alert
            $this->alert('success', 'Berhasil menghapus semua data terpilih !', [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);

            // Refresh the table data
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        }
    }

    public function datasource(): Builder
    {
        return Instansi::query()->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('nama_instansi')
            ->add('telepon')
            ->add('alamat')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }


    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Nama instansi', 'nama_instansi')
                ->sortable()
                ->searchable(),

            Column::make('Telepon', 'telepon')
                ->sortable()
                ->searchable(),

            Column::make('Alamat', 'alamat')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Instansi $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-supplier', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-supplier', ['id' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
