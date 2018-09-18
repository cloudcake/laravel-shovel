<?php

namespace Shovel\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
      'name',
      'description'
    ];
}
