<?php

namespace App\Actions\Category;

use App\Models\Category;

class SaveCategoryAction
{
    public function execute(Category $model, array $data): Category
    {
        $model->fill($data);
        $model->save();

        return $model->refresh();
    }
}
