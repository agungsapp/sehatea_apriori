<?php

namespace App\Livewire\Transaksi\Pengeluaran;

use App\Models\Pengeluaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PengeluaranTable extends PowerGridComponent
{
    public string $tableName = 'pengeluaran-table-lgqvq9-table';

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
        return Pengeluaran::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_invoice')
            ->add('nama')
            ->add('harga', fn(Pengeluaran $model) => Str::rupiah($model->harga))
            ->add('tanggal_formatted', fn(Pengeluaran $model) => Carbon::parse($model->tanggal)->format('d/m/Y'))
            ->add('catatan')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('No invoice', 'no_invoice')
                ->sortable()
                ->searchable(),

            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),

            Column::make('Harga', 'harga')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal', 'tanggal_formatted', 'tanggal')
                ->sortable(),

            Column::make('Catatan', 'catatan')
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
        return [
            Filter::datepicker('tanggal'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Pengeluaran $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('btn btn-warning')
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
