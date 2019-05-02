<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_tasks';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['project_id', 'absolute_day', 'name', 'story_id'];
}
