<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RepairorderRelations extends Pivot {


	public function repairorder() {
        return $this->belongsTo('App\Models\Repairorder');
    }

    public function repairstatus() {
        return $this->belongsTo('App\Models\Repairstatus');
    }

    public function reluser(){
        return $this->belongsTo('App\Models\Users');
    }


}