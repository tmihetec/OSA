<?php
/**
 * Created by PhpStorm.
 * User: Tomek
 * Date: 15.05.2016.
 * Time: 9:29
 *
 * https://github.com/JosephSilber/bouncer/releases/tag/v0.0.22
 *
 * You can now extend the built-in Ability & Role model classes.
 * After creating your own models, register them with the bouncer.
 *
 * Bouncer::useAbilityModel(MyAbility::class);
 * Bouncer::useRoleModel(MyRole::class);
 */

namespace App\Models;

use Silber\Bouncer\Database\Role as BaseRole;

class Role extends BaseRole
{
/*
 * //https://github.com/JosephSilber/bouncer/issues/13
 *
 *     public function scopeOrderedList($query)
    {
        return $query->orderBy('name')->lists('name', 'id');
    }*/
}