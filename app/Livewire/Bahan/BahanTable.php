<?php

namespace App\Livewire\Bahan;

use App\Models\Bahan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BahanTable extends PowerGridComponent
{
    public string $tableName = 'bahan-table-66tahd-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

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
        return Bahan::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama')
            ->add('satuan')
            ->add('harga_satuan', fn($data) => Str::rupiah($data->harga_satuan))
            ->add('stok')
            ->add('catatan')
            ->add('active')
            ->add('created_at_formatted', fn($data) => Carbon::parse($data->created_at)->format('d-m-Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),

            Column::make('Satuan', 'satuan')
                ->sortable()
                ->searchable(),
            Column::make('Harga Satuan', 'harga_satuan')
                ->sortable()
                ->searchable(),

            Column::make('Stok', 'stok')
                ->sortable()
                ->searchable(),

            Column::make('Catatan', 'catatan')
                ->sortable()
                ->searchable(),

            Column::make('Active', 'active')
                ->sortable()
                ->toggleable(
                    trueLabel: 'true',
                    falseLabel: 'false'
                ),

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

    public function actions(Bahan $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-item', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-item', ['id' => $row->id])
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        Bahan::query()->find($id)->update([
            $field => e($value),
        ]);
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
