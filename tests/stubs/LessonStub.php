<?php

use RobotsInside\Tags\Taggable;
use Illuminate\Database\Eloquent\Model;

class LessonStub extends Model
{
    use Taggable;

    protected $connection = 'testbench';

    public $table = 'lessons';
}
