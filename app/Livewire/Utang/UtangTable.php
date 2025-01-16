<?php

namespace App\Livewire\Utang;

use App\Models\Supplier;
use App\Models\PembayaranUtang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UtangTable extends PowerGridComponent
{
    public string $tableName = 'utang-table-xm6uyp-table';

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
        return Supplier::query()->whereHas('barangMasuk', function ($query) {
            $query->where('status_pembayaran', 'belum lunas');
        })->with(['barangMasuk' => function ($query) {
            $query->where('status_pembayaran', 'belum lunas')
                ->with('pembayaranBarangMasuk');
        }]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('nama')
            ->add('telepon')
            ->add('total', function ($dish) {
                // Menghitung jumlah barangMasuk yang status_pembayarannya 'belum lunas'
                $total = $dish->barangMasuk->where('status_pembayaran', 'belum lunas')->count();
                return "<span class='badge  rounded-pill bg-primary'>{$total}</span> <span class='ms-2 badge rounded-pill bg-danger text-white'>belum lunas</span>";
            })
            ->add('sisa_tagihan', function ($dish) {
                // Menghitung total sisa_tagihan dari barangMasuk yang status_pembayarannya 'belum lunas'
                $totalSisaTagihan = $dish->barangMasuk->where('status_pembayaran', 'belum lunas')->sum('sisa_tagihan');
                return Str::rupiah($totalSisaTagihan); // Menampilkan total sisa_tagihan dalam format yang lebih rapi

            })
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); // 20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Nama Supplier', 'nama'),
            Column::make('Telepon', 'telepon'),
            Column::make('Total Transaksi', 'total'),
            Column::make('Sisa Tagihan', 'sisa_tagihan'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action('Action')
        ];
    }


    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal_pembayaran'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Supplier $row): array
    {
        return [
            Button::add('pelunasan')
                ->slot("<i class='bx bxs-info-circle'></i>")
                ->id()
                ->class('btn btn-info')
                ->dispatch('show-utang', ['id' => $row->id])
        ];
    }

    // sampai sini , belum buat modal informasi transaksi apa saja . 

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
