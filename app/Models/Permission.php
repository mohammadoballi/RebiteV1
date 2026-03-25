<?php

namespace App\Models;

use Laratrust\Models\Permission as LaratrustPermission;

class Permission extends LaratrustPermission
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}
