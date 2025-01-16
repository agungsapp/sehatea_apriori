<?php

namespace App\Livewire\PengeluaranLain;

use App\Models\PengeluaranLain;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PengeluaranLainTable extends PowerGridComponent
{
    public string $tableName = 'pengeluaran-lain-table';

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
        return PengeluaranLain::query()->with(['jenisPengeluaran', 'sumberDana']);
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
            ->add('jenis_pengeluaran', fn($data) => $data->jenisPengeluaran->nama)
            ->add('sumber_dana', fn($data) => $data->sumberDana->nama)
            ->add('harga')
            ->add('keterangan', fn($data) => substr($data->keterangan, 0, 30) . (strlen($data->keterangan) > 30 ? '...' : ''))
            ->add('tanggal_pengeluaran_formatted', fn(PengeluaranLain $model) => Carbon::parse($model->tanggal_pengeluaran)->format('d/m/Y H:i:s'))
            ->add('created_at_formatted', fn($data) => Carbon::parse($data->created_at)->format('d-m-Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),

            Column::make('Jenis pengeluaran id', 'jenis_pengeluaran'),
            Column::make('Sumber dana id', 'sumber_dana'),
            Column::make('Harga', 'harga')
                ->sortable()
                ->searchable(),

            Column::make('Keterangan', 'keterangan')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal pengeluaran', 'tanggal_pengeluaran_formatted', 'tanggal_pengeluaran')
                ->sortable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datetimepicker('tanggal_pengeluaran'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(PengeluaranLain $row): array
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
