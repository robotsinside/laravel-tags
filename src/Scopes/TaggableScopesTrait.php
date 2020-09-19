<?php

namespace RobotsInside\Tags\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait TaggableScopesTrait
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $tags
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTag(Builder $builder, array $tags)
    {
        return $builder->hasTags($tags);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $tags
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags(Builder $builder, array $tags)
    {
        foreach ($tags as $tag) {
            $builder->hasTags([$tag]);
        }

        return $builder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $tags
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTags(Builder $builder, array $tags)
    {
        return $builder->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('slug', $tags);
        });
    }
}
