<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\AbsGrid;
use DB;

/**
 * Product Data Grid class
 *
 * @author Prashant Singh <prashant.singh852@webkul.com> @prashant-webkul
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class InventorySourcesDataGrid extends AbsGrid
{
    public $allColumns = [];

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('inventory_sources')->select('id')->addSelect($this->columns);

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'column' => 'id',
            'alias' => 'invId',
            'label' => 'ID',
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'width' => '40px'
        ]);

        $this->addColumn([
            'column' => 'code',
            'alias' => 'invCode',
            'label' => 'Code',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'width' => '100px'
        ]);

        $this->addColumn([
            'column' => 'name',
            'alias' => 'invName',
            'label' => 'Name',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'width' => '100px'
        ]);

        $this->addColumn([
            'column' => 'priority',
            'alias' => 'invPriority',
            'label' => 'Priority',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'width' => '100px'
        ]);

        $this->addColumn([
            'column' => 'status',
            'alias' => 'invStatus',
            'label' => 'Status',
            'type' => 'boolean',
            'searchable' => true,
            'sortable' => true,
            'width' => '100px'
        ]);
    }

    public function prepareActions() {
        $this->prepareAction([
            'type' => 'Edit',
            'route' => 'admin.inventory_sources.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->prepareAction([
            'type' => 'Delete',
            'route' => 'admin.inventory_sources.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'Exchange Rate']),
            'icon' => 'icon trash-icon'
        ]);
    }

    public function prepareMassActions() {
        // $this->prepareMassAction([
        //     'type' => 'delete',
        //     'action' => route('admin.catalog.products.massdelete'),
        //     'method' => 'DELETE'
        // ]);

        // $this->prepareMassAction([
        //     'type' => 'update',
        //     'action' => route('admin.catalog.products.massupdate'),
        //     'method' => 'PUT',
        //     'options' => [
        //         0 => true,
        //         1 => false,
        //     ]
        // ]);
    }

    public function render()
    {
        $this->addColumns();

        $this->prepareActions();

        $this->prepareMassActions();

        $this->prepareQueryBuilder();

        return view('ui::testgrid.table')->with('results', ['records' => $this->getCollection(), 'columns' => $this->allColumns, 'actions' => $this->actions, 'massactions' => $this->massActions]);
    }
}