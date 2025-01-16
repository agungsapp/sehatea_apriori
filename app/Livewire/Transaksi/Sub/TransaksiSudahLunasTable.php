<?php

namespace App\Livewire\Transaksi\Sub;

use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TransaksiSudahLunasTable extends PowerGridComponent
{
    public string $tableName = 'transaksi-sudah-lunas-table';


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

        return Penjualan::query()->where('status_pembayaran', 'lunas')
            ->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_invoice', function ($iv) {
                return "<button wire:click=\"\$dispatch('handle-detail', { id: {$iv->id} })\" class=\"btn btn-primary\">" . $iv->no_invoice . "</button>";
            })
            ->add('nama_pelanggan', function ($dish) {
                return $dish->nama_pelanggan;
                // return $dish->nama_pelanggan ?? 'INSTANSI';
            })
            ->add('kendaraan')
            ->add('nopol')
            ->add('telepon')
            ->add('tanggal_masuk_formatted', fn(Penjualan $model) => Carbon::parse($model->tanggal_masuk)->format('d/m/Y'))
            ->add('tanggal_keluar_formatted', fn(Penjualan $model) => Carbon::parse($model->tanggal_keluar)->format('d/m/Y'))
            ->add('ppn')
            ->add('pph')
            ->add('diskon')
            ->add('total_harga', function ($dish) {
                return Str::rupiah($dish->total_harga);
            })
            ->add('total_bayar')
            ->add('sisa_tagihan')
            ->add('status_pembayaran', function ($dish) {
                return $dish->status_pembayaran == 'lunas' ? "<span class='badge bg-success'>Lunas</span>" : "<span class='badge bg-danger'>Belum Lunas</span>";
            })
            ->add('catatan')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('No invoice', 'no_invoice')
                ->sortable()
                ->searchable(),

            Column::make('Nama pelanggan', 'nama_pelanggan')
                ->sortable()
                ->searchable(),

            Column::make('Kendaraan', 'kendaraan')
                ->sortable()
                ->searchable(),

            Column::make('Nopol', 'nopol')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal masuk', 'tanggal_masuk_formatted', 'tanggal_masuk')
                ->sortable(),

            Column::make('Tanggal keluar', 'tanggal_keluar_formatted', 'tanggal_keluar')
                ->sortable(),

            Column::make('Total harga', 'total_harga')
                ->sortable()
                ->searchable(),

            Column::make('Status pembayaran', 'status_pembayaran')
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

    public function actions(Penjualan $row): array
    {
        return [
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-barang-masuk', ['id' => $row->id]),
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
