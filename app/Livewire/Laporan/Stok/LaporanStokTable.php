<?php

namespace App\Livewire\Laporan\Stok;

use App\Models\Stok;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class LaporanStokTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'laporan-stok-table';
    public $startDate;
    public $endDate;

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            ['filterDate' => 'filterDate']
        );
    }

    public function filterDate($dates)
    {
        $this->startDate = $dates['startDate'];
        $this->endDate = $dates['endDate'];

        // Trigger refresh tanpa fillData
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        $sekarang = Carbon::now();
        return [
            PowerGrid::exportable(fileName: 'laporan-stok-' . $sekarang->format('d-m-Y'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = Stok::query()->with(['barang', 'cabang']);

        if ($this->startDate && $this->endDate) {
            // $this->js("alert('masuk nih')");
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }


        return $query;
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

    // public function actions(Stok $row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: ' . $row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

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
