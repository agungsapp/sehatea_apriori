<?php

namespace App\Livewire\Tables;

use App\Models\DaftarHarga;
use App\Models\Kategori;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DaftarHargaTable extends PowerGridComponent
{
    public string $tableName = 'daftar-harga-table-fqnp7w-table';
    public bool $showFilters = true;

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


    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Hapus (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('btn btn-danger')
                ->attributes([
                    'wire:click' => 'confirmBulkDelete',
                    'style' => 'position: relative;'
                ]),
        ];
    }

    public function confirmBulkDelete()
    {
        // Check if any rows are selected
        if (empty($this->checkboxValues)) {
            $this->alert('info', 'Tidak ada barang yang dipilih.', [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);
            return;
        }

        // Show confirmation alert
        $this->alert('warning', 'Apakah anda yakin ingin menghapus semua data yang terpilih ?', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'bulkDeleteConfirmed',
        ]);
    }

    #[On('bulkDeleteConfirmed')]
    public function bulkDeleteConfirmed()
    {
        if ($this->checkboxValues) {
            // Delete selected users
            DaftarHarga::destroy($this->checkboxValues);

            // Clear checkbox selections
            $this->js('window.pgBulkActions.clearAll()');

            // Show success alert
            $this->alert('success', 'Berhasil menghapus semua data terpilih !', [
                'showConfirmButton' => false,
                'timer' => 2000,
            ]);

            // Refresh the table data
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        }
    }


    public function datasource(): Builder
    {
        return DaftarHarga::query()->with('kategori');
    }

    public function relationSearch(): array
    {
        return [
            'kategori' => ['nama'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no', fn($row, $index) => $index + 1)
            ->add('kode_barang', fn($row) => $row->kode_barang ?? 'NO_DATA')
            ->add('nama_barang')
            ->add('id_barang')
            ->add('kategori.nama')
            ->add('harga_beli', fn($row) => $row->harga_beli ? Str::rupiah($row->harga_beli) : 'NO_DATA')
            ->add('harga_jual', fn($row) => $row->harga_jual ? Str::rupiah($row->harga_jual) : 'NO_DATA')
            ->add('catatan', fn($row) => $row->catatan ?? '-')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->created_at)->format('d/m/Y H:i'); //20/01/2024 10:05
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'no'),
            Column::make('Kode barang', 'kode_barang')
                ->sortable()
                ->searchable(),

            Column::make('Nama barang', 'nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('kategori', 'kategori.nama')
                ->sortable()
                ->searchable(),
            Column::make('Harga beli', 'harga_beli')
                ->sortable()
                ->searchable(),

            Column::make('Harga jual', 'harga_jual')
                ->sortable()
                ->searchable(),

            Column::make('Catatan', 'catatan')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nama_barang', 'nama_barang')
                ->operators(['contains', 'is', 'is_not']),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        DaftarHarga::query()->find($id)->update([
            $field => e($value),
        ]);
    }

    public function actions(DaftarHarga $row): array
    {
        return [
            Button::add('edit')
                ->slot("<i class='bx bxs-pencil'></i> ")
                ->id()
                ->class('btn btn-warning')
                ->dispatch('edit-data', ['id' => $row->id]),
            Button::add('delete')
                ->slot("<i class='bx bxs-trash'></i>")
                ->id()
                ->class('btn btn-danger')
                ->dispatch('delete-data', ['id' => $row->id])
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
