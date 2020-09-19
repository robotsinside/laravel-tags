<?php

use Illuminate\Database\Eloquent\Model;
use RobotsInside\Tags\Scopes\TagUsedScopesTrait;

class TagStub extends Model
{
    use TagUsedScopesTrait;

    protected $connection = 'testbench';

    public $table = 'tags';
}
