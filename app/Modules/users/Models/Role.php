<?php

namespace Users\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Role
 *
 * @package Users\Models
 * @property string name
 * @property string guard_name
 */
class Role extends BaseModel
{
    use HasRoles;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string',
        'created_by' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->guard_name)) {
                $role->guard_name = config('auth.defaults.guard', 'web');
            }
        });
    }

    /**
     * A role may be given various permissions.
     */

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        $guard = $this->attributes['guard_name'] ?? config('auth.defaults.guard', 'web');
        return $this->morphedByMany(
            getModelForGuard($guard),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }

}
