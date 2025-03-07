<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent'
    ];
}
