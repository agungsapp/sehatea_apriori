<?php

namespace App\Livewire\Pengguna;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Livewire\Attributes\On;


final class PenggunaTable extends PowerGridComponent
{
    use LivewireAlert;

    public string $tableName = 'pengguna-table-08tser-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Bulk Delete (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('btn btn-danger')
                ->attributes([
                    'wire:click' => 'confirmBulkDelete',
                    // Tambahkan style agar tombol terlihat menarik
                    'style' => 'position: relative;'
                ]),
        ];
    }

    public function confirmBulkDelete()
    {
        // Check if any rows are selected
        if (empty($this->checkboxValues)) {
            $this->alert('info', 'Tidak ada pengguna yang dipilih.', [
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
            User::destroy($this->checkboxValues);

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


    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        $this->js('alert(window.pgBulkActions.get(\'' . $this->tableName . '\'))');
        if ($this->checkboxValues) {
            User::destroy($this->checkboxValues);
            $this->js('window.pgBulkActions.clearAll()'); // clear the count on the interface.
        }
    }

    public function datasource(): Builder
    {
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),



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

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-user', ['id' => $row->id])
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
