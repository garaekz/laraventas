<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SearchFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $fields)
    {
        $fields = explode(',', $fields);

        $value = is_array($value) ? implode(',', $value) : $value;

        $fields = collect($fields)->toArray();
        $value = trim($value);

        $query->where(function ($q) use ($value, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', mb_strtolower("%{$value}%"));
            }
        });
    }

}
