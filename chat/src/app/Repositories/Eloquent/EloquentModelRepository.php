<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use Exception;

abstract class EloquentModelRepository
{
    protected $eloquent;

    /**
     * @return BaseModel
     * @throws Exception
     */
    protected function model(): BaseModel
    {
        if (!$this->eloquent) {
            throw new Exception('No eloquent set for ' . get_class($this));
        }

        return $this->isSubclassBaseModel(new $this->eloquent);
    }

    /**
     * @throws Exception
     */
    private function isSubclassBaseModel($eloquent): BaseModel
    {
        if (!is_subclass_of($eloquent, BaseModel::class)) {
            throw new Exception(get_class($eloquent). ' don\'t extend for '. BaseModel::class);
        }

        return $eloquent;
    }
}
