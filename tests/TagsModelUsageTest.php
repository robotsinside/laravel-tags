<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RobotsInside\Tags\Models\Tag;
use RobotsInside\Tags\Models\Taggable;

class TagsModelUsageTest extends TestCase
{
    protected $lesson;

    public function setUp(): void
    {
        parent::setUp();

        foreach (['Science', 'Technology', 'Engineering', 'Mathematics'] as $tag) {
            TagStub::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
                'count' => 0
            ]);
        }

        $this->lesson = LessonStub::create([
            'title' => 'Lesson 1'
        ]);
    }

    /** @test */
    public function can_tag_a_lesson()
    {
        $this->lesson->tag(TagStub::where('slug', 'science')->first());

        $this->assertCount(1, $this->lesson->tags);

        $this->assertContains('science', $this->lesson->tags->pluck('slug'));
    }

    /** @test */
    public function can_tag_lesson_with_a_collection_of_tags()
    {
        $tagArray = ['science', 'technology', 'engineering'];
        $tags = TagStub::whereIn('slug', $tagArray)->get();

        $this->lesson->tag($tags);

        $this->assertCount(3, $this->lesson->tags);

        foreach ($tagArray as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }
    }

    /** @test */
    public function can_untag_lesson_tags()
    {
        $tagArray = ['science', 'technology', 'engineering'];
        $tags = TagStub::whereIn('slug', $tagArray)->get();

        $this->lesson->tag($tags);

        $this->lesson->untag($tags->first());

        $this->assertCount(2, $this->lesson->tags);

        array_pop($tagArray);

        foreach ($tagArray as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }

        $this->assertNotContains('engineering', $this->lesson->tags->pluck('slug'));
    }

    /** @test */
    public function can_untag_all_lesson_tags()
    {
        $tagArray = ['science', 'technology', 'engineering'];
        $tags = TagStub::whereIn('slug', $tagArray)->get();

        $this->lesson->tag($tags);

        $this->lesson->untag();

        $this->lesson->load('tags');

        $this->assertCount(0, $this->lesson->tags);
    }

    /** @test */
    public function can_retag_lesson_tags()
    {
        $tagArray = ['science', 'technology', 'engineering'];
        $retagArray = ['science', 'mathematics'];

        $tags = TagStub::whereIn('slug', $tagArray)->get();
        $retags = TagStub::whereIn('slug', $retagArray)->get();

        $this->lesson->tag($tags);

        $this->lesson->retag($retags);

        $this->lesson->load('tags');

        $this->assertCount(2, $this->lesson->tags);

        foreach ($retagArray as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }
    }

    /** @test */
    public function non_models_are_filtered_when_using_collection()
    {
        $tagArray = ['science', 'technology', 'engineering'];

        $tags = TagStub::whereIn('slug', $tagArray)->get();

        $tags->push('something weird here'); // ¯\_(ツ)_/¯

        $this->lesson->tag($tags);

        $this->assertCount(3, $this->lesson->tags);
    }

    /** @test */
    public function can_create_a_tag_using_the_make_function()
    {
        $tag = (new Tag())->setConnection('testbench')->make('My tag');

        $this->assertInstanceOf(Tag::class, $tag);
    }

    /** @test */
    public function cannot_create_duplicate_tags_using_the_make_function()
    {
        $tag = (new Tag())->setConnection('testbench')->make('My tag');

        $this->assertInstanceOf(Tag::class, $tag);

        $tag = (new Tag())->setConnection('testbench')->make('My tag');

        $this->assertInstanceOf(Tag::class, $tag);

        $saved = Tag::where('slug', 'my-tag')->get();

        $this->assertCount(1, $saved);
    }

    /** @test */
    public function can_resolve_a_collection_of_tags()
    {
        $resolvable = collect(['Tag 1', 'Tag 2']);

        $tags = (new Tag())
                ->setConnection('testbench')
                ->resolveAll($resolvable);

        $this->assertCount(2, $tags);

        $this->assertInstanceOf(Collection::class, $tags);
    }

    /** @test */
    public function can_resolve_an_array_of_tags()
    {
        // Passing an array
        $tags = (new Tag())
                ->setConnection('testbench')
                ->resolveAll(['Tag 1', 'Tag 2']);

        $this->assertCount(2, $tags);

        $this->assertInstanceOf(Collection::class, $tags);
    }

    /** @test */
    public function can_resolve_string_of_tags()
    {
        $tags = (new Tag())
                ->setConnection('testbench')
                ->resolveAll('Tag 3');

        $this->assertCount(1, $tags);

        $this->assertInstanceOf(Collection::class, $tags);
    }

    /** @test */
    public function cannot_duplicate_resolve_all_tags()
    {
        $collectionA = (new Tag())
                ->setConnection('testbench')
                ->resolveAll(['Tag 1', 'Tag 2']);

        $collectionB = (new Tag())
                ->setConnection('testbench')
                ->resolveAll(['Tag 1', 'Tag 2']);

        // Including STEM tags.
        $this->assertCount(6, Tag::get());
    }

    /** @test */
    public function can_filter_taggables()
    {
        $this->lesson->tag(TagStub::where('slug', 'science')->first());

        $taggables = Taggable::type('LessonStub')->get();

        $this->assertInstanceOf(Taggable::class, $taggables->first());

        $this->assertCount(1, $taggables);

        $this->assertInstanceOf(LessonStub::class, $taggables->first()->taggable);
    }
}
