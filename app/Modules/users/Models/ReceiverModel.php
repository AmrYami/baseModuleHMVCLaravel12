<?php

namespace Users\Models;

use App\Models\BaseAuthModel;

class ReceiverModel extends BaseAuthModel
{
    protected $table = 'receivers';

    protected $fillable = ['name', 'fields'];

    // Optionally cast the fields attribute to array automatically
    protected $casts = [
        'fields' => 'array',
    ];
}
