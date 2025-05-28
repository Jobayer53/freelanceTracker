<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
      public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function timeLogs()
{
    return $this->hasMany(TimeLog::class);
}
}
