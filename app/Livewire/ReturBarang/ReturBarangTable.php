<?php

namespace App\Livewire\ReturBarang;

use App\Models\ReturBarang;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ReturBarangTable extends PowerGridComponent
{
    public string $tableName = 'retur-barang-table-ajillm-table';

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

    public function datasource(): Builder
    {
        return ReturBarang::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('id_barang')
            ->add('id_supplier')
            ->add('id_user')
            ->add('harga_satuan')
            ->add('jumlah')
            ->add('total_harga')
            ->add('metode')
            ->add('catatan')
            ->add('tanggal_retur_formatted', fn (ReturBarang $model) => Carbon::parse($model->tanggal_retur)->format('d/m/Y'))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Id barang', 'id_barang'),
            Column::make('Id supplier', 'id_supplier'),
            Column::make('Id user', 'id_user'),
            Column::make('Harga satuan', 'harga_satuan')
                ->sortable()
                ->searchable(),

            Column::make('Jumlah', 'jumlah')
                ->sortable()
                ->searchable(),

            Column::make('Total harga', 'total_harga')
                ->sortable()
                ->searchable(),

            Column::make('Metode', 'metode')
                ->sortable()
                ->searchable(),

            Column::make('Catatan', 'catatan')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal retur', 'tanggal_retur_formatted', 'tanggal_retur')
                ->sortable(),

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
        return [
            Filter::datepicker('tanggal_retur'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(ReturBarang $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
