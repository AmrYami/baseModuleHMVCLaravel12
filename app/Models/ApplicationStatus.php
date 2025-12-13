<?php

namespace App\Models;

use App\Models\BaseModel as Model;

class ApplicationStatus extends Model
{
    protected $table = 'application_statuses';
    protected $fillable = ['user_id', 'status', 'reason', 'changed_by'];

//status must be one of these 'pending','in_review','approved','rejected','on_hold'

    public function user(){
        return $this->belongsTo(\Users\Models\User::class, 'user_id');
    }


    public function changedBy(){
        return $this->belongsTo(\Users\Models\User::class, 'changed_by');
    }
}
