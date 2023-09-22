<?php

namespace App\Actions\Product;

use App\Models\Product;

class SaveProductAction
{
    public function execute(Product $model, array $data): Product
    {
        $data['brand_id'] = $data['brand'] ?? null;
        $data['unit_id'] = $data['unit'];
        $data['min_stock'] = $data['min_stock'] ?? 0;
        unset($data['brand'], $data['unit']);

        $model->fill($data);
        $model->save();

        return $model->refresh();
    }
}
