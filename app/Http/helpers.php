<?php

/*

funkcija na našem serveru

 */


function DeviceRepairOrder($DeviceRepairOrderRequest){

    // LOG SVE   
    Log::info("Logging an object: " . print_r($DeviceRepairOrderRequest, true));

    // ako ih ima više, onda je polje, ak je samo jedan napravi da je polje, radi foreach
    $DeviceRepairOrderRequest->repairOrder = is_array($DeviceRepairOrderRequest->repairOrder)
        ? $DeviceRepairOrderRequest->repairOrder
        : array($DeviceRepairOrderRequest->repairOrder)
    ;

    // moram vratiti koleciju statusa...    
    $statuses=array();

    foreach ($DeviceRepairOrderRequest->repairOrder as $repairOrder) {
        # code...

        // provjeri jel taj caseId već postoji, ak da vrati neki error osim 0
        if (!(\App\Models\Soap\ws_tele2soap::where('caseId', '=', $repairOrder->caseId)->exists())) {


                // NOVI OBJEKT
                $newcase = new \App\Models\Soap\ws_tele2soap();


                // RASPAKIRAJ UPIT I SLOŽI MODEL
                // - common
                $newcase->caseId                    = $repairOrder->caseId;
                $newcase->repairorder_priority      = $repairOrder->priority;        // case u int
                $newcase->repairorder_receiveddate  = $repairOrder->receivedDate;    // cast u DATE

                // Base63 encoded, json encoded K=>V (id=>description) - OPTIONAL
                $newcase->repairorder_equipment     = $repairOrder->equipment;
                // Base63 encoded, json encoded K=>V (id=>description)
                $newcase->repairorder_damage        = $repairOrder->damage;

                // comment je spoj "NOTE" (detaljan opis kvara) i "DAMAGENOTE" (uočenja oštećenja prilikom preuzimanja)
                $newcase->repairorder_comment       = $repairOrder->comment;

                // - device
                $newcase->device_imei               = $repairOrder->device->imei;     
                $newcase->device_brand              = $repairOrder->device->brand;     
                $newcase->device_model              = $repairOrder->device->model;     
                $newcase->device_type               = $repairOrder->device->type;     
                $newcase->device_code               = $repairOrder->device->code;     
                $newcase->device_buydate            = $repairOrder->device->buyDate;     
                // - contact
                $newcase->contact_phone1            = $repairOrder->contact->phone1;
                $newcase->contact_phone2            = $repairOrder->contact->phone2;
                $newcase->contact_firstname         = $repairOrder->contact->firstName;
                $newcase->contact_lastname          = $repairOrder->contact->lastName;
                // - contact address
                $newcase->contact_address_street    = $repairOrder->contact->address->street;
                $newcase->contact_address_place     = $repairOrder->contact->address->place;
                $newcase->contact_address_postcode  = $repairOrder->contact->address->postcode;
                $newcase->contact_address_country   = $repairOrder->contact->address->country;
                // - pos
                $newcase->pos_id                    = $repairOrder->pos->id;
                $newcase->pos_name                  = $repairOrder->pos->name; 
                $newcase->pos_phone                 = $repairOrder->pos->phone;
                $newcase->pos_address_street        = $repairOrder->pos->address->street;
                $newcase->pos_address_place         = $repairOrder->pos->address->place;
                $newcase->pos_address_postcode      = $repairOrder->pos->address->postcode;
                $newcase->pos_address_country       = $repairOrder->pos->address->country;
                // - type
                
                $newcase->claimtype                 = (is_null($repairOrder->type)) ? 1 : $repairOrder->type;
                /*
                    1 = Novi popravak
                    2 = DOA (isporučeni neispravni uređaj)
                    3 = DAP (neispravni uređaj koji su vračeni unutar 8 dana)
                    4 = Mehaničko oštećenje
                    5 = Zamjena
                    6 = Kontrola kvalitete

                 */

                // SPREMI U BAZU
                $newcase->save();

                $hasError=0;
                $message="OK (".$repairOrder->caseId.")";
                //$message[]="DA (".$repairOrder->caseId.")";

        } else {

                $hasError=1;
                //$code=1;
                $message="NE (".$repairOrder->caseId.") [ caseId postoji] ";
        }


        $caseId=$repairOrder->caseId;

        // odgovor za taj caseId
        $status = new \App\Models\Soap\SOAP_Status();
        $status->caseId=  $caseId;
        $status->error= ($hasError==0) ? false : true;
        $status->message= $message;


        // stavi ga u polje responseova
        $statuses[]=$status;

    }


    // ZAPAKIRAJ ODGOVOR
    $deviceRepairOrderResponse = new \App\Models\Soap\SOAP_DeviceRepairOrderResponse();

    // VRATI ODGOVOR
    return $deviceRepairOrderResponse->status=$statuses;

}

?>