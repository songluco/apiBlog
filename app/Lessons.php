<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lessons extends Model
{
    //隐藏表中的字段
    protected $hidden = ['created_at', 'updated_at'];
}
