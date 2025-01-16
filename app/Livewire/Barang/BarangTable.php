<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Merk;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Support\Str;

final class BarangTable extends PowerGridComponent
{
    public string $tableName = 'barang-table-lp0z94-table';


    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

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
        return Barang::query()->with(['kategori', 'merk'])->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('kode_barang')
            ->add('no_seri')
            ->add('nama_barang')
            ->add('id_kategori', function ($barang) {
                return $barang->kategori->nama;
            })
            ->add('id_brand', function ($barang) {
                return $barang->brand->nama;
            })
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Kode barang', 'kode_barang')
                ->sortable()
                ->searchable(),

            Column::make('No seri', 'no_seri')
                ->sortable()
                ->searchable(),

            Column::make('Nama Barang', 'nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Kategori', 'id_kategori'),
            Column::make('Brand', 'id_brand'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('category_name')
                ->filterRelation('category', 'name')
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Barang $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-barang', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-barang', ['id' => $row->id])
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
