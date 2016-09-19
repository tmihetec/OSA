<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests, DB, Auth;
use App\Http\Controllers\Controller;

use View, Spwarehouse, Supplier, Sparepart;
use App\Models\Spreceipt;
use Bouncer;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $primke_closed=\App\Models\Spreceipt::where('closed','>','0')->orderBy('receipt_datetime','desc')->orderBy('updated_at','desc')->get();
        $primke_open=\App\Models\Spreceipt::where('closed','0')->orderBy('receipt_datetime','desc')->orderBy('updated_at','desc')->get();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        
        $sendData=array(
            'primke_closed' => $primke_closed,
            'primke_open'   => $primke_open,
            'adminUser'     => $adminUser
            );

        return View::make('primka.index')->with($sendData);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        $warehouses=\App\Models\Spwarehouse::lists('name','id')->all();
        $suppliers=\App\Models\Supplier::lists('name','id')->all();
        $spareparts=\App\Models\Sparepart::all()->lists('name','id')->all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');

        $sendData=array(
            'primka'        => null,
            'editingID'     => null,
            'suppliers'     => $suppliers,
            'warehouses'    => $warehouses,
            'spareparts'    => $spareparts,
            'adminUser'     => $adminUser
            );

        return View::make('primka.create')->with($sendData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) //, $id=null
    {

        $rules = array(
                    'document_no'               => 'required', //|min:1',   
                    'supplier_id'               => 'required|numeric',
                    'warehouse_id'              => 'required|numeric',
                    'receipt_datetime'          => 'date_format:"d.m.Y"',
                    'document_date'             => 'sometimes|date_format:"d.m.Y"',
                    );
        $messages = array(
                    'supplier_id.required'      => 'Odaberite dobavljača.',
                    'warehouse_id.required'     => 'Odaberite prijemno skladište.',
                    'document_no.required'      => 'Ulazni dokument je obvezan.',
                    'receipt_datetime.date_format' => 'Datum primke - neispravan format datuma (npr. 28.02.2014).',
                    'document_date.date_format' => 'Datum ulaznog dokumenta - neispravan format datuma (npr. 28.02.2014) ili prazno polje.',
                    );
        $this->validate($request, $rules, $messages);




        // primka
        $primka=new \App\Models\Spreceipt();

        // polja
        $primka->spwarehouse_id   =  $request->input('warehouse_id');
        $primka->supplier_id    =  $request->input('supplier_id');
        $primka->document_no    =  $request->input('document_no');
        $primka->closed         =  $request->input('closed');
        $primka->user_opened    =   Auth::user()->id;

        // datumi
        $primka->receipt_datetime  = \Carbon\Carbon::createFromFormat('d.m.Y', $request->input('receipt_datetime'))->format('Y-m-d');
        if (!empty($request->input('document_date'))) {
            $primka->document_date  = \Carbon\Carbon::createFromFormat('d.m.Y', $request->input('document_date'))->format('Y-m-d');
        } else {
            $primka->document_date  = null;//\Carbon\Carbon::now()->format('Y-m-d');
        }
        /*
            http://stackoverflow.com/questions/205190/select-from-same-table-as-an-insert-or-update
            INSERT INTO spreceipt(supplier_id,spwarehouse_id,receipt_no) values( 1,1,COALESCE((SELECT max(x.receipt_no) FROM spreceipt x)+1,1))   
         */

        DB::transaction(function() use ($primka) {
    
            $max_receipt_no = \App\Models\Spreceipt::select(DB::raw('MAX(receipt_no) as no'))->get();
            $primka->receipt_no = is_null($max_receipt_no->first()->no) ? 1 : $max_receipt_no->first()->no+1;
            $primka->save();

        });


        // spremi spareparts u pivot tablicu
        if (is_array($parts=$request->input('sppt'))) {
            foreach ($parts as $part) {
                      // dd($part);
                if ($part['qty']>0){
                    $primka->spareparts()->attach($part['ids'], array('price'=>$part['prc'],'qty'=>$part['qty'])); 

                    // ako je primka sada zatvorena onda potrpaj u INVENTORY
                    if($primka->closed) {
                        // nađi da li ima taj key? firstOrCreate?
                        $partmodel=\App\Models\Sparepart::find($part['ids']);

                        // izračunaj cijene i upiši u tablicu dijelova
                        $staraCijena = $partmodel->price;
                        $staraKolicinaSvaSkladista = 0;
                        $inventory = $partmodel->stock()->where('sparepart_id',$part['ids'])->get();
                        foreach($inventory as $kol) { $staraKolicinaSvaSkladista+=$kol->pivot->qty; }
                        $novaCijena = ($staraCijena*$staraKolicinaSvaSkladista + $part['prc']*$part['qty']) / ($staraKolicinaSvaSkladista + $part['qty']);
                        $partmodel->price=$novaCijena;
                        $partmodel->save();

                        // treba testirati da li postoji u pivotu zapis za taj dio + to skladište?
                        $whid=$primka->spwarehouse_id;

                        //-> upis količina u inventory tablicu
                        $qtypivot=$partmodel->stock()->where('spwarehouse_id',$whid)->first();//->pivot;//->qty;
                        if (is_null($qtypivot)) {
                            $partmodel->stock()->attach($whid, array('qty'=>$part['qty']));
                        } else {
                            $qtypivot->pivot->qty += $part['qty'];
                            $qtypivot->pivot->save();
                        }

                        // spremi i user ID kod prvog zatvaranja!
                        if (!is_null($primka->user_closed)) {
                            $primka->user_closed = Auth::user()->id;
                            $primka->save();
                        }

                    }
                }
            }
        }
        

        // ispis stavki kod neuspjelog spremanja


       // dd($request->all());


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        //-edit otvorene - svi
        //-edit zatvorene - ak mijenja količine, prije spremanja, provjeriti da li su nove količine veće od prethodnih. 
        //ak su manje, onda provjeriti da li se može to postaviti da ne ode u -  * TODO napisati na kojim dokumentima se nalaze

        return $this->store($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        //-delete otvorene - sam tak i sam admin
        //-delete zatvorene -ak može a da ni jedna stavka ne ode u minus, ok, inače nemože * TODO napisati na kojim dokumentima se nalaze

    }
}
