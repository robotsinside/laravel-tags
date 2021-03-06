<?php

namespace RobotsInside\Tags\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait TagUsedScopesTrait
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGte(Builder $builder, $value)
    {
        return $builder->where('count', '>=', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGt(Builder $builder, $value)
    {
        return $builder->where('count', '>', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLte(Builder $builder, $value)
    {
        return $builder->where('count', '<=', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLt(Builder $builder, $value)
    {
        return $builder->where('count', '<', $value);
    }
}
