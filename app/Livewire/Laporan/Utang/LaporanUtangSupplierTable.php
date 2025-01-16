<?php

namespace App\Livewire\Laporan\Utang;

use App\Models\BarangMasuk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class LaporanUtangSupplierTable extends PowerGridComponent
{
    use WithExport;


    public string $tableName = 'laporan-utang-supplier-table';


    public $startDate;
    public $endDate;
    public $statusPembayaran = 'semua';



    protected function getListeners()
    {
        return array_merge(parent::getListeners(), ['filterData' => 'filterData']);
    }

    public function filterData($filters)
    {
        $this->startDate = $filters['startDate'];
        $this->endDate = $filters['endDate'];
        $this->statusPembayaran = $filters['statusPembayaran'];

        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }


    public function setUp(): array
    {
        $this->showCheckBox();


        $sekarang = Carbon::now();
        return [
            PowerGrid::exportable(fileName: 'laporan-utang-supplier-' . $sekarang->format('d-m-Y'))
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
        $query = BarangMasuk::query()->with(['supplier', 'cabang', 'user']);

        if ($this->statusPembayaran !== 'semua') {
            $query->where('status_pembayaran', $this->statusPembayaran);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $query->orderBy('id', 'desc');


        return $query;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('no_invoice')
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
            ->add('sisa_tagihan', function ($dish) {
                return Str::rupiah($dish->sisa_tagihan);
            })
            ->add('status_pembayaran')
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
            Column::make('Sisa Tagihan', 'sisa_tagihan'),
            Column::make('Status Pembayaran', 'status_pembayaran'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal'),
            Filter::datepicker('jatuh_tempo'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions(BarangMasuk $row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
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
