<?php

namespace App\Livewire\Laporan\Piutang;

use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class LaporanPiutangTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'laporan-piutang-table-kwukk6-table';

    public $startDate;
    public $endDate;
    public $statusPembayaran = 'semua';

    protected function getListeners()
    {
        return array_merge(parent::getListeners(), ['filterData' => 'filterData']);
    }

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
        return Penjualan::query();
        $query = Penjualan::query()
            ->with([
                'pelangganKendaraan.pelanggan',
                'pelangganKendaraan.kendaraan'
            ]);


        if ($this->statusPembayaran != 'semua') {
            $query->where('status_pembayaran', $this->statusPembayaran);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_keluar', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        return $query->orderBy('id', 'desc');
    }

    public function relationSearch(): array
    {
        return [
            'pelangganKendaraan.pelanggan' => ['nama'],
            'pelangganKendaraan.kendaraan' => ['nopol', 'nama_kendaraan'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('no_invoice', function ($iv) {
                return "<button wire:click=\"\$dispatch('handle-detail', { id: {$iv->id} })\" class=\"btn btn-primary\">" . $iv->no_invoice . "</button>";
            })
            ->add('nama_pelanggan', function ($dish) {
                return $dish->pelangganKendaraan->pelanggan->nama;
                // return $dish->nama_pelanggan ?? 'INSTANSI';
            })
            ->add('kendaraan', fn($data) => $data->pelangganKendaraan->kendaraan->nama_kendaraan)
            ->add('nopol', fn($data) => $data->pelangganKendaraan->kendaraan->nopol)
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
            Column::make('No', 'no'),
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
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
