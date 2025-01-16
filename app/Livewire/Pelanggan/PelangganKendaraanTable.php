<?php

namespace App\Livewire\Pelanggan;

use App\Models\PelangganKendaraan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PelangganKendaraanTable extends PowerGridComponent
{
    public string $tableName = 'pelanggan-kendaraan-table-oj2u3n-table';

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
        return PelangganKendaraan::query()->with([
            'pelanggan',
            'pelanggan.instansi:id,nama_instansi',
            'kendaraan:id,nama_kendaraan,nopol',
        ]);
    }

    public function relationSearch(): array
    {
        return [
            'pelanggan' => [
                'nama',
                'telepon'
            ],
            'pelanggan.instansi' => [
                'nama_instansi'
            ],
            'kendaraan' => [
                'nama_kendaraan',
                'nopol'
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama', function ($row) {
                return $row->pelanggan->tipe === 'reguler'
                    ? $row->pelanggan->nama
                    : $row->pelanggan->instansi->nama_instansi;
            })
            ->add('tipe', function ($row) {
                return $row->pelanggan->tipe === 'reguler'
                    ? '<span class="badge rounded-pill bg-primary">Reguler</span>'
                    : '<span class="badge rounded-pill bg-success">Instansi</span>';
            })
            ->add('pelanggan.telepon')
            ->add('kendaraan.nama_kendaraan')
            ->add('kendaraan.nopol')
            ->add('created_at_formatted', function ($row) {
                return Carbon::parse($row->created_at)->format('d/m/Y H:i');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),
            Column::make('Tipe', 'tipe')
                ->sortable()
                ->searchable(),
            Column::make('Nama Kendaraan', 'kendaraan.nama_kendaraan')
                ->sortable()
                ->searchable(),
            Column::make('Nomor Polisi', 'kendaraan.nopol')
                ->sortable()
                ->searchable(),
            Column::make('Telepon', 'pelanggan.telepon')
                ->sortable()
                ->searchable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            // Column::action('Action')
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

    // public function actions(PelangganKendaraan $row): array
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
