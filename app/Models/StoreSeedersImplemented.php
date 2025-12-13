<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel as Model;

class StoreSeedersImplemented extends Model
{
    use HasFactory;
    public $table = 'seeders';
    protected $fillable = [
        'seeder','batch'
    ];
}
