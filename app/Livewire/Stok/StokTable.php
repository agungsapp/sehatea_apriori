<?php

namespace App\Livewire\Stok;

use App\Models\Cabang;
use App\Models\Stok;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class StokTable extends PowerGridComponent
{
    public string $tableName = 'stok-table-zuwran-table';
    public bool $showFilters = true;

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

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
        return Stok::query()->with(['barang', 'cabang']);
    }

    public function relationSearch(): array
    {
        return [
            'barang' => [
                'kode_barang',
                'nama_barang'
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('kode_barang', function ($stok) {
                return ucwords($stok->barang->kode_barang ?? 'NO_DATA'); // Menampilkan nama barang
            })
            ->add('barang_nama', function ($stok) {
                return ucwords($stok->barang ? $stok->barang->nama_barang : 'NO_DATA'); // Menampilkan nama barang
            })
            ->add('kategori', function ($stok) {
                return ucwords($stok->kategori->nama ?? 'NO_DATA'); // Menampilkan nama barang
            })
            ->add('cabang_nama', function ($stok) {
                return ucwords($stok->cabang ? $stok->cabang->nama : 'NO_DATA'); // Menampilkan nama cabang
            })
            ->add('stok')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Kode Barang', 'kode_barang') // Menggunakan nama field yang baru ditambahkan
                ->sortable()
                ->searchable(),
            Column::make('Nama Barang', 'barang_nama') // Menggunakan nama field yang baru ditambahkan
                ->sortable()
                ->searchable(),
            Column::make('Kategori', 'kategori') // Menggunakan nama field yang baru ditambahkan
                ->sortable()
                ->searchable(),

            Column::make('Cabang', 'cabang_nama') // Menggunakan nama field yang baru ditambahkan
                ->sortable()
                ->searchable(),
            Column::make('Stok', 'stok'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('kode_barang')->placeholder('Dish Name'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Stok $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i>")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-stok', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-stok', ['id' => $row->id])
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

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Stok::query()->find($id)->update([
            $field => e($value),
        ]);
    }
}
