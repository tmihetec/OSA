<?php
namespace App\Models;

/*

ro -> devicelog
ro -> lockrecive
 */


class Osalog 
{
    //
    //
	protected $action;
	protected $data;


	 /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct($action, $data)
    {
    	
        $this->action= $action;
        $this->data= $data;
        
    }


	// sprema log data u repairorders 
    public function store()
    {

    }


    // dohvaća id 
    private function ro_id()
    {

    }


	public function getJsonData(){
        $var = get_object_vars($this);
        foreach($var as &$value){
           if(is_object($value) && method_exists($value,'getJsonData')){
              $value = $value->getJsonData();
           }
        }
        return $var;
     }


	public function toJson(){
		return json_encode($this->getJsonData());
	}

}
