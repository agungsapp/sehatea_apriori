<?php

namespace App\Livewire\Antrian;

use App\Models\Antrian;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class AntrianTable extends PowerGridComponent
{
    public string $tableName = 'antrian-table-bnjt75-table';

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
        return Antrian::query()
            ->with(['pelangganKendaraan.pelanggan', 'pelangganKendaraan.kendaraan'])
            ->whereIn('status', ['antri', 'proses'])
            ->orderBy('id', 'desc');
    }


    public function relationSearch(): array
    {
        return [
            'pelangganKendaraan.pelanggan' => ['nama'],
            'pelangganKendaraan.pelanggan.instansi' => ['nama_instansi'],
            'pelangganKendaraan.kendaraan' => ['nama_kendaraan', 'nopol'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            // ->add('nama_pelanggan', fn(Antrian $model) => $model->pelangganKendaraan->pelanggan->tipe == 'instansi' ? 'ya' : 'bukan')
            ->add('nama_pelanggan', fn(Antrian $model) => $model->pelangganKendaraan->pelanggan->tipe == 'instansi' ? $model->pelangganKendaraan->pelanggan->instansi->nama_instansi : $model->pelangganKendaraan->pelanggan->nama)
            ->add('tipe', fn(Antrian $model) => $model->pelangganKendaraan->pelanggan->tipe)
            ->add('kendaraan', fn(Antrian $model) => $model->pelangganKendaraan->kendaraan->nama_kendaraan)
            ->add('nopol', fn(Antrian $model) => $model->pelangganKendaraan->kendaraan->nopol)
            ->add('tanggal_masuk_formatted', fn(Antrian $model) => Carbon::parse($model->tanggal_masuk)->format('d/m/Y'))
            ->add('status')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('no', 'no'),
            Column::make('Nama Pelanggan', 'nama_pelanggan')->searchable(),
            Column::make('tipe pelanggan', 'tipe'),
            Column::make('kendaraan', 'kendaraan')
                ->sortable()
                ->searchable(),
            Column::make('nomor polisi', 'nopol'),
            Column::make('Tanggal masuk', 'tanggal_masuk_formatted', 'tanggal_masuk')
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal_masuk'),
            Filter::datepicker('tanggal_keluar'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Antrian $row): array
    {
        $button = [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->route('edit-antrian', ['id' => $row->id]),
        ];

        if ($row->status == 'proses') {
            $button[] = Button::add('chekout')
                ->slot("<i class='bx fs-5 bxs-right-arrow-circle'></i>")
                ->id()
                ->class('btn btn-info')
                ->dispatch('chekout', ['id' => $row->id]);
        }

        return $button;
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
