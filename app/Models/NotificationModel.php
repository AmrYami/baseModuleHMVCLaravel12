<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationModel extends Model
{

    use SoftDeletes;

    public $table = 'notifications';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'created_by','type','related_id','related_type','body', 'title','route','icon', 'user_id', 'seen'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'created_by' => 'required',
        'related_type' => 'required',
        'related_id' => 'required',
        'body' => 'required',
        'title' => 'required',
        'route' => 'required',
        'icon' => 'max:255',
    ];
}
