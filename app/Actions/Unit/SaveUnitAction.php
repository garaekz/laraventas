<?php

namespace App\Actions\Unit;

use App\Models\Unit;

class SaveUnitAction
{
    public function execute(Unit $model, array $data): Unit
    {
        $model->fill($data);
        $model->save();

        return $model->refresh();
    }
}
