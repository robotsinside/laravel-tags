<?php

use Illuminate\Support\Str;

class TagsStringUsageTest extends TestCase
{
    protected $lesson;

    public function setUp() :void
    {
        parent::setUp();

        foreach (['Laravel', 'PHP', 'Testing', 'Redis', 'MySQL', 'Fun stuff'] as $tag) {
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
        $this->lesson->tag(['laravel', 'php']);

        $this->assertCount(2, $this->lesson->tags);

        foreach (['Laravel', 'PHP'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function can_untag_a_lesson()
    {
        $this->lesson->tag(['laravel', 'php', 'testing']);
        $this->lesson->untag(['laravel']);

        $this->assertCount(2, $this->lesson->tags);

        foreach (['Testing', 'PHP'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function can_untag_all_lesson_tags()
    {
        $this->lesson->tag(['laravel', 'php', 'testing']);
        $this->lesson->untag();

        $this->lesson->load('tags');

        $this->assertCount(0, $this->lesson->tags);
        $this->assertEquals(0, $this->lesson->tags->count());
    }

    /** @test */
    public function can_retag_lesson_tags()
    {
        $this->lesson->tag(['laravel', 'php', 'testing']);

        $tags = ['laravel', 'redis', 'testing'];
        $this->lesson->retag($tags);

        $this->lesson->load('tags');

        $this->assertCount(3, $this->lesson->tags);

        foreach ($tags as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }
    }

    /** @test */
    public function non_existing_tags_are_ignored_on_tagging()
    {
        $this->lesson->tag(['laravel', 'php', 'python']);

        $this->assertCount(2, $this->lesson->tags);

        foreach (['laravel', 'php'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }
    }

    /** @test */
    public function inconstistent_tag_cases_are_normalized()
    {
        $this->lesson->tag(['Laravel', 'PHP', 'ReDiS', 'Fun stuff']);

        $this->assertCount(4, $this->lesson->tags);

        foreach (['laravel', 'php', 'redis', 'fun-stuff'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('slug'));
        }
    }
}
