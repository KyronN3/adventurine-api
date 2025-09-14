<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;

class CustomORM
{

    protected Model $model;
    protected string $rightTable;
    protected string $leftTable;

    public function __construct(Model $model, string $rightTable)
    {
        $this->model = $model;
        $this->rightTable = $rightTable;
        $this->leftTable = $this->model->getTable();
    }

    /* ❗❗❗ Only Use This for custom Column ID, not foreign ID Otherwise use built-in "Eloquent query" in laravel❗❗❗ */
    public function leftJoinCustomColumn(array $select, string $leftIdColumn, string $rightIdColumn)
    {
        return $this->model::query()->leftJoin($this->rightTable, function ($join) use ($leftIdColumn, $rightIdColumn) {
            $join->on($this->leftTable . '.' . $leftIdColumn, '=', $this->rightTable . '.' . $rightIdColumn);
        })->select($select);
    }


}
