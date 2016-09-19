<?php

namespace App\Models\Soap;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ws_tele2soap extends Model
{
    //
    //
	use SoftDeletes;

    protected $fillable = [];
	protected $table = 'ws_tele2soap';
	protected $dates = ['deleted_at'];


}
