<?php

namespace App\Livewire\PembelianBahan;

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

final class PembelianBahanTable extends PowerGridComponent
{
    public string $tableName = 'pembelian-bahan-table';

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
        return Pengeluaran::query()->with(['bahan', 'jenisPengeluaran', 'sumberDana'])->orderBy('created_at', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('bahan_id', fn(Pengeluaran $model) => $model->bahan->nama)
            ->add('jenis_pengeluaran_id', fn(Pengeluaran $model) => $model->jenisPengeluaran->nama)
            ->add('sumber_dana_id', fn(Pengeluaran $model) => $model->sumberDana->nama)
            ->add('satuan')
            ->add('qty')
            ->add('harga_satuan', fn(Pengeluaran $model) => Str::rupiah($model->harga_satuan))
            ->add('subtotal', fn(Pengeluaran $model) => Str::rupiah($model->subtotal))
            ->add('tanggal_formatted', fn(Pengeluaran $model) => Carbon::parse($model->tanggal)->format('d/m/Y'))
            ->add('catatan')
            ->add('created_at_formatted', fn(Pengeluaran $model) => Carbon::parse($model->created_at)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Bahan id', 'bahan_id'),
            Column::make('Jenis pengeluaran id', 'jenis_pengeluaran_id'),
            Column::make('Sumber dana id', 'sumber_dana_id'),
            Column::make('Satuan', 'satuan')
                ->sortable()
                ->searchable(),

            Column::make('Qty', 'qty')
                ->sortable()
                ->searchable(),

            Column::make('Harga satuan', 'harga_satuan')
                ->sortable()
                ->searchable(),

            Column::make('Subtotal', 'subtotal')
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
