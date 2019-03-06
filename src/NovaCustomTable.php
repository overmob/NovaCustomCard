<?php

namespace Overmob\NovaCustomTable;

use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Card;

class NovaCustomTable extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'nova-custom-table';
    }

    public function actions($actions){
        $this->withMeta(['actions' => $actions]);
        return $this;
    }

    public function filters($filters){
        $this->withMeta(['filters' => $filters]);
        return $this;
    }

    public function tableData($tableData)
    {
        $this->withMeta(['tableData' => $tableData]);
        return $this;
    }
}
