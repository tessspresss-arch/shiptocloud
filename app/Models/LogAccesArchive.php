<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAccesArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'type_objet',
        'objet_id',
        'description',
        'ip_address',
        'user_agent',
    ];
}
