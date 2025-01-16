<?php

namespace App\Livewire\Examples;

use App\Models\Merk;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use App\Enums\Diet;
use App\Models\Category;
use App\Models\Dish;
use Illuminate\Support\Number;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class FiltersInlineTable extends PowerGridComponent
{
    public string $tableName = 'filters-inline-table';

    use WithExport;

    public int $categoryId = 0;

    protected function queryString()
    {
        return $this->powerGridQueryString();
    }

    public function setUp(): array
    {
        $this->showCheckBox('id');

        return [
            PowerGrid::header()
                ->showToggleColumns()
                ->withoutLoading()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Merk::query();
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

            ->add('created_at_formatted', fn($dish) => Carbon::parse($dish->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'nama')
                ->searchable()
                ->sortable(),

            Column::make('Created At', 'created_at_formatted', 'created_at')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nama')->placeholder('Dish Name'),


        ];
    }
}
