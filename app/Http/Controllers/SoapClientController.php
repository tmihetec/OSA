<?php
//http://www.soapclient.com/soaptest.html
//
//
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Log;

use App\Models\Soap\SOAP_Address;
use App\Models\Soap\SOAP_Contact;
use App\Models\Soap\SOAP_Device;
use App\Models\Soap\SOAP_DeviceRepairOrderRequest;
use App\Models\Soap\SOAP_ChangeDeviceRepairStatusRequest;
use App\Models\Soap\SOAP_POS;
use App\Models\Soap\SOAP_Service;
use App\Models\Soap\SOAP_RepairOrder;

use Illuminate\Http\Request;
use App\Http\Requests;



class SoapClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */


    // samo za test
     public function newOrder(){

        //http://stackoverflow.com/questions/13874521/php-soapclient-timeout-error-handler
        //http://stackoverflow.com/questions/17860488/php-soapclient-soap-error-fatal-error-couldnt-load-from-external-entity
        ini_set("default_socket_timeout","300");

        //ini_set('display_errors', true);
        ini_set("soap.wsdl_cache_enabled", "0");
        ini_set('soap.wsdl_cache_ttl', '0'); 

        $client = new \SoapClient(asset("wsdl/rs.wsdl"), array("trace"=>1));

 


        $repairOrder = new SOAP_RepairOrder();

        $repairOrder->caseId = "1231456eee";
        $repairOrder->priority = 1;
        $repairOrder->receivedDate = "17.07.2015.";
        $repairOrder->type = 4;

        $device = new SOAP_Device();
        $device->imei = "123456789";
        $device->brand = "Samsung";
        $device->model = "S4";
        $device->type = "Super";
        $device->code = "987654321";
        $device->buyDate = "17.06.2015.";

        $repairOrder->device = $device;

        $contact = new SOAP_Contact();
        $contact->phone1 = "0917367245";
        $contact->phone2 = "654";
        $contact->firstName = "Antonio";
        $contact->lastName = "Grabic";

        $address = new SOAP_Address();
        $address->street = "Vranovina";
        $address->place = "Zagreb";
        $address->postcode = "10000";
        $address->country = "Croatia";

        $contact->address = $address;

        $repairOrder->contact = $contact;

        $repairOrder->equipment = "Headphones";
        $repairOrder->damage = "Broken microphone";
        $repairOrder->comment = "Microphone is not working";

        $pos = new SOAP_POS();
        $pos->id = "12345678910";
        $pos->name = 111;
        $pos->phone = "09112345678";

        $address = new SOAP_Address();
        $address->street = "Drziceva";
        $address->place = "Slunj";
        $address->postcode = "10000";
        $address->country = "Croatia";

        $pos->address = $address;

        $repairOrder->pos = $pos;

// DRUGI 
    
        $ros=array();
        $ros[]= $repairOrder;

        $repairOrder = new SOAP_RepairOrder();

        $repairOrder->caseId = "abcdaaa";
        $repairOrder->priority = 4;
        $repairOrder->receivedDate = "07.07.2015.";
        $repairOrder->type = 2;

        $device = new SOAP_Device();
        $device->imei = "123";
        $device->brand = "a";
        $device->model = "v";
        $device->type = "g";
        $device->code = "o";
        $device->buyDate = "11.01.2015.";

        $repairOrder->device = $device;

        $contact = new SOAP_Contact();
        $contact->phone1 = "0917367245";
        $contact->phone2 = "654";
        $contact->firstName = "Antonio";
        $contact->lastName = "Grabic";

        $address = new SOAP_Address();
        $address->street = "Vranovina";
        $address->place = "Zagreb";
        $address->postcode = "10000";
        $address->country = "Croatia";

        $contact->address = $address;

        $repairOrder->contact = $contact;

        $repairOrder->equipment = "Headphones";
        $repairOrder->damage = "Broken microphone";
        $repairOrder->comment = "Microphone is not working";

        $pos = new SOAP_POS();
        $pos->id = "12345678910";
        $pos->name = 111;
        $pos->phone = "09112345678";

        $address = new SOAP_Address();
        $address->street = "Drziceva";
        $address->place = "Slunj";
        $address->postcode = "10000";
        $address->country = "Croatia";

        $pos->address = $address;

        $repairOrder->pos = $pos;



        // dodaj i taj...
        $ros[]= $repairOrder;



        $deviceRepairOrderRequest = new SOAP_DeviceRepairOrderRequest();
        $deviceRepairOrderRequest->repairOrder = $ros;//$repairOrder;

        $params = array(
            "deviceRepairOrderRequest" => $deviceRepairOrderRequest,
        );
        //$params = new \SoapParam($deviceRepairOrderRequest, 'DeviceRepairOrderRequest');


        //dd($deviceRepairOrderRequest);
        //var_dump($client->__getFunctions());

        $result = $client->__soapCall('DeviceRepairOrder', $params);

        //echo "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
        //echo("\nDumping client object functions:\n");
        //var_dump($client->__getFunctions());
        //echo("\nDumping request headers:\n".$client->__getLastRequestHeaders());
        //echo("\nDumping request:\n".$client->__getLastRequest());
        //echo("\nDumping response headers:\n".$client->__getLastResponseHeaders());
        //echo("\nDumping response:\n".$client->__getLastResponse());

        //print_r($result);


    }














    public function index()
    {
        //
    }

    public function callOrderUpdate($caseId, $repairStatus, $remark="", $comment="", $imei="", $reason="", $serviceStatus="", $serviceType=""){


        // case mora biti string
        // status mora biti integer
        $caseId=(string)$caseId;
        $repairStatus=(int)$repairStatus;

        $comment=(string)$comment;
        $remark=(string)$remark; // dodatno...
        
        $imei=(string)$imei;
        $reason=(string)$reason;

        $serviceStatus=(string)$serviceStatus;
        $serviceType=(string)$serviceType;     


//dd("mijenjam: ".$caseId." na ".$repairStatus);

        // naš klijent šalje njihovom serveru, kod promjene na radnom nalogu
        ini_set("default_socket_timeout","300");
        ini_set("soap.wsdl_cache_enabled", "0");
        ini_set('soap.wsdl_cache_ttl', '0'); 

        $result=false;

        try {

                
                // NAPRAVI Request
                // mandatory:
                $ChangeDeviceRepairStatusRequest = new SOAP_ChangeDeviceRepairStatusRequest();
                $ChangeDeviceRepairStatusRequest->caseId = $caseId;
                $ChangeDeviceRepairStatusRequest->status = $repairStatus;

                // optional:
                $ChangeDeviceRepairStatusRequest->remark = $remark;
                $ChangeDeviceRepairStatusRequest->comment = $comment;
                
                // optional device:
                $device = new SOAP_Device();
                $device->imei = $imei;
                $device->reason = $reason;
                $ChangeDeviceRepairStatusRequest->device=$device;

                // optional service:
                $service = new SOAP_Service();
                $service->status = $serviceStatus;
                $service->type = $serviceType;
                $ChangeDeviceRepairStatusRequest->service=$service;



                $params = array(
                    "ChangeDeviceRepairStatusRequest" => $ChangeDeviceRepairStatusRequest,
                );



                // IDEM PROBATI NAPRAVITI / POSLATI SOAP PAKET

                $dontcall=env('DISABLE_T2SOAP',false);
                if (!$dontcall) {

                    // production, T2
                    // http://213.101.144.135/ASP/production/service/
                    // http://213.101.144.135/ASP/production/service/?wsdl
                    // testing
                    // http://213.101.144.135/ASP/testing/service/
                    // http://213.101.144.135/ASP/testing/service/?wsdl
                    $client = new \SoapClient("http://213.101.144.135/ASP/production/service/?wsdl", array("trace"=>1));

                    //local_:
                    //$client = new \SoapClient(asset("wsdl/asp.wsdl"), array("trace"=>1));

                    // POZOVI
                    $result = $client->__soapCall('ChangeDeviceRepairStatus', $params);

                } else {

                    // fake result - za test
                    $result = app()->make('stdClass');
                    $result->status = app()->make('stdClass');
                    $result->status->errorCode = 1;
                    $result->status->message= "TEST SOAP ODGOVOR";//implode(PHP_EOL, "TU SAM");

                    return $result;

                }


				Log::info("Logging an SOAP result (caseid=".$caseId." ): " . print_r($result, true)); //(caseid=".$caseId." )
				

        } catch (\SoapFault $e) {

                Log::error("Soap client exception (caseid=".$caseId." ): " . print_r($e, true)); //(caseid=".$caseId." )
                exit;

        } 

        return $result;


    }




    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
    }
}
