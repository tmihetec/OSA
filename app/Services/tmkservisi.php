<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Bouncer;

class tmkservisi extends Controller
{

    public function t2ordercount()
    {
        $t2nalozi=\App\Models\Soap\ws_tele2soap::whereNull('rejected')
                ->where(function($q){
                        $q->where('sts_repairorder_number','<','1')
                        ->orWhereNull('sts_repairorder_number');
                })->count();
        return $t2nalozi;
    }

    public function SPPordercount()
    {
        $sql='
            SELECT t1.*
            FROM repairorder_repairstatus t1 
            LEFT JOIN repairorder_repairstatus t2
                ON (t1.repairorder_id = t2.repairorder_id 
                    AND t1.created_at < t2.created_at
                    )
            WHERE t2.created_at IS NULL 
            AND t1.repairstatus_id = 2 
        ';

        $SPPnalozi=\DB::select($sql);
        return count($SPPnalozi);
    }

    public function userrole()
    {
		//dd(\App\Models\User::find(1)->roles->first()->name );
		//složiti role po važnosti (ono, step 1000)
    }


    public function restrictedSppRole()
    {
        return (Bouncer::is(\Auth::user())->a('spp') && Bouncer::is(\Auth::user())->notAn('admin'));
    }

}
