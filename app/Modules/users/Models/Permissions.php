<?php

namespace Users\Models;

use App\Models\BaseModel;

/**
 * Class Permissions
 *
 * @version December 11, 2019, 11:23 am UTC
 *
 * @property string name
 * @property string guard_name
 */
class Permissions extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public $table = 'permissions';

    public $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        // Permission uniqueness lives in the `permissions` table and is
        // scoped by `(name, guard_name)` per the migration's composite
        // unique index. Mirror that here so the validator matches the DB.
        'name' => 'required|max:255|unique:permissions,name,NULL,id,guard_name,web',
        'guard_name' => 'required|string',
    ];
}
