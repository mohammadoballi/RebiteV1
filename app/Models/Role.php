<?php

namespace App\Models;

use Laratrust\Models\Role as LaratrustRole;

class Role extends LaratrustRole
{
    const ADMIN = 'admin';
    const DONOR = 'donor';
    const CHARITY = 'charity';
    const VOLUNTEER = 'volunteer';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}
