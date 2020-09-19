<?php

namespace RobotsInside\Tags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RobotsInside\Tags\Scopes\TagUsedScopesTrait;

class Tag extends Model
{
    use TagUsedScopesTrait;

    public $fillable = ['name', 'slug'];

    /**
     * Resolve a single tag.
     *
     * @param string $name
     * @return RobotsInside\Tags\Models\Tag
     */
    public function resolve($name)
    {
        return $this->firstOrCreate(['name' => $name, 'slug' => Str::slug($name)]);
    }

    /**
     * Resolve on ore more tags.
     *
     * @param string|array|Illuminate\Support\Collection $tags
     * @return Illuminate\Support\Collection
     */
    public function resolveAll($tags)
    {
        if (is_array($tags)) {
            $tags = collect($tags);
        } elseif (is_string($tags)) {
            $tags = collect([$tags]);
        }

        return $tags->map(function ($category) {
            return $this->resolve($category);
        });
    }

    /**
     * Make a new tag or get the existing one.
     *
     * @param string $name
     * @return RobotsInside\Tags\Models\Tag
     */
    public function make($name)
    {
        return $this->firstOrCreate(['name' => $name, 'slug' => Str::slug($name)]);
    }
}
