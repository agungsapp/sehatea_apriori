<?php

namespace App\Livewire\Jasa;

use App\Models\Jasa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class JasaTable extends PowerGridComponent
{
    use LivewireAlert;

    public string $tableName = 'jasa-table-iv9srx-table';

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
            $this->alert('info', 'Tidak ada jasa yang dipilih.', [
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
            'position' => 'center',
            'toast' => false,
            'onConfirmed' => 'bulkDeleteConfirmed',
        ]);
    }

    #[On('bulkDeleteConfirmed')]
    public function bulkDeleteConfirmed()
    {
        if ($this->checkboxValues) {
            // Delete selected users
            Jasa::destroy($this->checkboxValues);

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
        return Jasa::query()->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('kode_jasa')
            ->add('nama')
            ->add('catatan')
            ->add(
                'created_at_formatted',
                fn($dish) => Carbon::parse($dish->created_at)->format('d/m/Y H:i')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Kode Jasa', 'kode_jasa')
                ->sortable()
                ->searchable(),
            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),


            Column::make('Catatan', 'catatan')
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

    public function actions(Jasa $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-jasa', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-jasa', ['id' => $row->id])
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
