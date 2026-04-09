<?php

namespace Webkul\Brochure\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class BrochureDataGrid extends DataGrid
{
    /**
     * Build the primary query for the datagrid.
     */
    public function prepareQueryBuilder(): mixed
    {
        $queryBuilder = DB::table('brochures')
            ->select(
                'id',
                'title',
                'slug',
                'type',
                'status',
                'sort_order',
                'created_at'
            );

        $this->addFilter('id', 'brochures.id');
        $this->addFilter('title', 'brochures.title');
        $this->addFilter('status', 'brochures.status');
        $this->addFilter('type', 'brochures.type');
        $this->addFilter('created_at', 'brochures.created_at');

        return $queryBuilder;
    }

    /**
     * Define columns for the datagrid.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('brochure::app.admin.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('brochure::app.admin.datagrid.title'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'slug',
            'label'      => trans('brochure::app.admin.datagrid.slug'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('brochure::app.admin.datagrid.type'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return ucfirst($row->type);
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('brochure::app.admin.datagrid.status'),
            'type'       => 'boolean',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->status) {
                    return '<span class="label-active">' . trans('brochure::app.admin.datagrid.active') . '</span>';
                }

                return '<span class="label-info">' . trans('brochure::app.admin.datagrid.inactive') . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'sort_order',
            'label'      => trans('brochure::app.admin.datagrid.sort-order'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('brochure::app.admin.datagrid.created-at'),
            'type'       => 'datetime',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Define row-level actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('brochure.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('brochure::app.admin.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.brochure.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('brochure.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('brochure::app.admin.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.brochure.destroy', $row->id);
                },
            ]);
        }
    }

    /**
     * Define mass actions for bulk operations.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('brochure.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('brochure::app.admin.datagrid.mass-delete'),
                'method' => 'POST',
                'url'    => route('admin.brochure.mass_destroy'),
            ]);
        }
    }
}
