<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 *
 * Represents a user role/permission level in the VANTAGE system.
 *
 * The system defines three default roles via DatabaseSeeder:
 * - Admin (ID 1): Full system access
 * - Manager (ID 2): Limited administrative access
 * - User (ID 3): Standard basic access
 *
 * Roles are referenced by users through the role_id foreign key.
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Fields that can be mass-assigned.
     *
     * @var array<string>
     */
    protected $fillable = ['name'];
}

