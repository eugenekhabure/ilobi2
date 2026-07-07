<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table    = 'designations';
    protected $guarded  = ['id'];
    protected $fillable = [
        'name','status','added_by'
    ];
    protected $fakeColumns = [];

    public $timestamps = false;

    protected $casts = [ 
        
    ];
}
