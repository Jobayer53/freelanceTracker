<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
     public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
