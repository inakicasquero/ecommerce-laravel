<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TaxRateDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('tax_rates')
            ->addSelect(
                'id',
                'identifier',
                'state',
                'country',
                'zip_code',
                'zip_from',
                'zip_to',
                'tax_rate'
            );

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'identifier',
            'label'      => trans('admin::app.datagrid.identifier'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'state',
            'label'      => trans('admin::app.datagrid.state'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($value) {
                if (empty($value->state)) {
                    return '*';
                }

                return $value->state;
            },
        ]);

        $this->addColumn([
            'index'      => 'country',
            'label'      => trans('admin::app.datagrid.country'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'zip_code',
            'label'      => trans('admin::app.configuration.tax-rates.zip_code'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'zip_from',
            'label'      => trans('admin::app.configuration.tax-rates.zip_from'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'zip_to',
            'label'      => trans('admin::app.configuration.tax-rates.zip_to'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'tax_rate',
            'label'      => trans('admin::app.datagrid.tax-rate'),
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.tax_rates.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.datagrid.delete'),
            'method' => 'POST',
            'url'    => function ($row) {
                return route('admin.tax_rates.delete', $row->id);
            },
        ]);
    }
}
