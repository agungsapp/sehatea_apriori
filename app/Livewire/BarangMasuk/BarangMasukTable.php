<?php

namespace App\Livewire\BarangMasuk;

use App\Models\BarangMasuk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BarangMasukTable extends PowerGridComponent
{
    public string $tableName = 'barang-masuk-table-raogwp-table';

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
        return BarangMasuk::query()->with(['supplier', 'cabang', 'user'])->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('no_invoice', function ($iv) {
                return "<button wire:click=\"\$dispatch('handle-detail', { id: {$iv->id} })\" class=\"btn btn-primary\">" . $iv->no_invoice . "</button>";
            })
            ->add('id_suplier', function ($dish) {
                return ucwords($dish->supplier ? $dish->supplier->nama : 'N/A');
            })
            // ->add('tanggal')
            ->add('total_harga', function ($dish) {
                return Str::rupiah($dish->total_harga);
            })
            ->add('total_bayar', function ($dish) {
                return Str::rupiah($dish->total_bayar);
            })
            ->add('status_pembayaran', function ($dish) {

                return $dish->status_pembayaran == 'lunas' ? "<span class='badge bg-success'>Lunas</span>" : "<span class='badge bg-danger'>Belum Lunas</span>";

                // $status = '';
                // if ($dish->status_pembayaran == 'lunas') {
                //     return  $status = '<span class="badge badge-success">Lunas</span>';
                // } else {
                //     return $status = '<span class="badge badge-warning">Belum Lunas</span>';
                // }
            })
            // ->add('no_invoice', function ($stok) {
            //     return ucwords($stok->barang ? $stok->barang->nama_barang : 'N/A'); // Menampilkan nama barang
            // })
            // ->add('cabang_nama', function ($stok) {
            //     return ucwords($stok->cabang ? $stok->cabang->nama : 'N/A'); // Menampilkan nama cabang
            // })
            ->add('stok')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Invoice', 'no_invoice')
                ->sortable()
                ->searchable(),
            Column::make('Supplier', 'id_suplier'),
            // Column::make('Tanggal', 'tanggal'),
            Column::make('Total Harga', 'total_harga'),
            Column::make('Jumlah Bayar', 'total_bayar'),
            Column::make('Status Pembayaran', 'status_pembayaran'),
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

    public function actions(BarangMasuk $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->route('edit-barang-masuk', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->route('edit-barang-masuk', ['id' => $row->id])
            // Button::add('detail')
            //     ->slot("<i class='bx bx-info-circle' ></i>")
            //     ->id()
            //     ->class('btn btn-info')
            //     ->dispatch('detail-barang-masuk', ['id' => $row->id])
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
