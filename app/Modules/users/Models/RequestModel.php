<?php

namespace Users\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Users\Models\User;


class RequestModel extends BaseModel
{
    use HasFactory;

    protected $table = 'requests';
    protected $fillable = ['title', 'description', 'user_id', 'request_from', 'request_to'];

    /**
     * Get the user who owns the cycle request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who made the request.
     */
    public function requestFrom(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_from');
    }
    /**
     * Get the user who get the request.
     */
    public function requestTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_to');
    }
}
