<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table = 'roles';
    protected $fillable = ['name', 'guard_name'];

}
