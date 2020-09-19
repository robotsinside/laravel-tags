<?php

namespace RobotsInside\Tags;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RobotsInside\Tags\Models\Tag;
use RobotsInside\Tags\Scopes\TaggableScopesTrait;

trait Taggable
{
    use TaggableScopesTrait;

    /**
     * Get the taggable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    /**
     * Add the specified tag(s).
     *
     * @param integer|array|RobotsInside\Tags\Models\Tag $tags
     * @return void
     */
    public function tag($tags)
    {
        $this->addTags($this->getNormalizedTags($tags));
    }

    /**
     * Removes all tags if no argument is passed in, otherwise remove the specified tags.
     *
     * @param integer|array|RobotsInside\Tags\Models\Tag $tags
     * @return void
     */
    public function untag($tags = null)
    {
        if ($tags === null) {
            $this->removeAllTags();
        } else {
            $this->removeTags($this->getNormalizedTags($tags));
        }
    }

    /**
     * Retag a model with the specified tags.
     *
     * @param integer|array|RobotsInside\Tags\Models\Tag $tags
     * @return void
     */
    public function retag($tags)
    {
        $this->removeAllTags();

        $this->tag($tags);
    }

    /**
     * Remove all tags based on the tags relationship.
     *
     * @return void
     */
    private function removeAllTags()
    {
        $this->removeTags($this->tags);
    }

    /**
     * Remove tags and decrement the tag count accordingly.
     *
     * @param Collection $tags
     * @return void
     */
    private function removeTags(Collection $tags)
    {
        $this->tags()->detach($tags);

        $tags->each(function ($tag) {
            $tag->count <= 0 ?: $tag->decrement('count');
        });
    }

    /**
     * Add tags and increment the tag count.
     *
     * @param Collection $tags
     * @return void
     */
    private function addTags(Collection $tags)
    {
        $sync = $this->tags()->syncWithoutDetaching($tags->pluck('id'));

        foreach (Arr::get($sync, 'attached') as $tagId) {
            $tag = $tags->where('id', $tagId)->first()->increment('count');
        }
    }

    /**
     * Get the workable tags
     *
     * @param mixed $tags
     * @return Collection
     */
    private function getNormalizedTags($tags)
    {
        if (is_array($tags)) {
            return $this->getTagModels($tags);
        }

        if ($tags instanceof Model) {
            return $this->getTagModels([$tags->slug]);
        }

        return $this->filterTagsCollection($tags);
    }

    /**
     * A fallback to resolve only instances of Tag.
     *
     * @param Collection $tags
     * @return Collection
     */
    private function filterTagsCollection(Collection $tags)
    {
        return $tags->filter(function ($tag) {
            return $tag instanceof Model;
        });
    }

    /**
     * Perform the DB query.
     *
     * @param array $tags
     * @return Collection
     */
    private function getTagModels(array $tags)
    {
        return Tag::whereIn('slug', $this->normalizeTagNames($tags))->get();
    }

    /**
     * Normalise values to slugified strings.
     *
     * @param array $tags
     * @return array
     */
    private function normalizeTagNames($tags)
    {
        return array_map(function ($tag) {
            return Str::slug($tag);
        }, $tags);
    }
}
