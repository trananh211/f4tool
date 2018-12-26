<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Excel extends Model
{
    public $timestamps = true;

    protected $table='tableName';

    public function users()
    {
        return $this->belongsTo('User');
    }

    public function getFileTracking()
    {
        $files = Excel::where('status',0)->get();
        return $files;
    }

}
