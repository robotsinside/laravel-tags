<?php

use Illuminate\Support\Str;
use RobotsInside\Tags\Models\Taggable;

class TagsDateUsageTest extends TestCase
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
    public function can_filter_taggables_by_days()
    {
        $this->lesson->tag(TagStub::whereIn('slug', ['science', 'mathematics'])->get());

        // Make the mathematics tag created_at 7 days 1 hour ago.
        $taggable = Taggable::latest()->first();
        $taggable->created_at = now()->subDays('7')->subHours('1');
        $taggable->save();

        $newTags = $taggable->taggedWithin('7 days')->get();

        $this->assertCount(1, $newTags);

        $this->assertNotContains('mathematics', $newTags->pluck('slug'));
    }

    /** @test */
    public function can_filter_taggables_by_months()
    {
        $this->lesson->tag(TagStub::whereIn('slug', ['science', 'mathematics'])->get());

        // Make the mathematics tag created_at 2 months 1 hour ago.
        $taggable = Taggable::latest()->first();
        $taggable->created_at = now()->subMonths(2)->subHours(1);
        $taggable->save();

        $newTags = $taggable->taggedWithin('2 months')->get();

        $this->assertCount(1, $newTags);

        $this->assertNotContains('mathematics', $newTags->pluck('slug'));
    }

    /** @test */
    public function can_filter_taggables_by_years()
    {
        $this->lesson->tag(TagStub::whereIn('slug', ['science', 'mathematics'])->get());

        // Make the mathematics tag created_at 1 year 1 hour ago.
        $taggable = Taggable::latest()->first();
        $taggable->created_at = now()->subYears(1)->subHours(1);
        $taggable->save();

        $newTags = $taggable->taggedWithin('1 year')->get();

        $this->assertCount(1, $newTags);

        $this->assertNotContains('mathematics', $newTags->pluck('slug'));
    }

    /** @test */
    public function filtering_expects_valid_string()
    {
        $this->lesson->tag(TagStub::where('slug', 'science')->first());

        // Make the mathematics tag created_at 7 days 1 hour ago.
        $taggable = Taggable::latest()->first();
        $taggable->created_at = now()->subDays(7)->subHours(1);
        $taggable->save();

        $this->expectException(InvalidArgumentException::class);

        $taggable->taggedWithin('1 yearasdf')->get();
    }
}
