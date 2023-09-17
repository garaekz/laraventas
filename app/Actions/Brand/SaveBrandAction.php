<?php

namespace App\Actions\Brand;

use App\Models\Brand;

class SaveBrandAction
{
    public function execute(Brand $model, array $data): Brand
    {
        $model->fill($data);
        $model->save();

        return $model->refresh();
    }
}
