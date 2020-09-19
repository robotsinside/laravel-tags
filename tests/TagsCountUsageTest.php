<?php

class TagsCountUsageTest extends TestCase
{
    protected $lesson;

    public function setUp(): void
    {
        parent::setUp();

        $this->lesson = LessonStub::create([
            'title' => 'Lesson count'
        ]);
    }

    /** @test */
    public function tag_count_is_incremented_when_tagged()
    {
        $tag = TagStub::create([
            'name' => 'Laravel',
            'slug' => 'slug',
            'count' => 0
        ]);

        $this->lesson->tag($tag);

        $tag = $tag->fresh();

        $this->assertEquals(1, $tag->count);
    }

    /** @test */
    public function tag_count_is_decremented_when_untagged()
    {
        $tag = TagStub::create([
            'name' => 'Laravel',
            'slug' => 'slug',
            'count' => 20
        ]);

        $this->lesson->tag($tag);
        $this->lesson->untag($tag);

        $tag = $tag->fresh();

        $this->assertEquals(20, $tag->count);
    }

    /** @test */
    public function tag_count_does_not_go_below_zero()
    {
        $tag = TagStub::create([
            'name' => 'Laravel',
            'slug' => 'slug',
            'count' => 0
        ]);

        $this->lesson->untag($tag);

        $tag = $tag->fresh();

        $this->assertEquals(0, $tag->count);
    }

    /** @test */
    public function tag_count_is_not_incremented_if_already_exists()
    {
        $tag = TagStub::create([
            'name' => 'Laravel',
            'slug' => 'slug',
            'count' => 0
        ]);

        $this->lesson->tag($tag);
        $this->lesson->tag($tag);
        $this->lesson->tag($tag);

        $tag = $tag->fresh();

        $this->assertEquals(1, $tag->count);
    }
}
