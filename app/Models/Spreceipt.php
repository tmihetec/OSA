<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Spreceipt extends \Eloquent {

	use SoftDeletes;
	protected $fillable = [];
	protected $dates = ['deleted_at', 'document_date','receipt_datetime'];
	protected $table = 'spreceipts';

	
	public function warehouse(){
		return $this->belongsTo('App\Models\Spwarehouse','spwarehouse_id');
	}

	public function supplier(){
		return $this->belongsTo('App\Models\Supplier');
	}

	public function spareparts(){
		return $this->belongsToMany('App\Models\Sparepart')->withPivot('qty','price'); 
	}
	
}