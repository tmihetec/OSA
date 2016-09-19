<?php

namespace App\Http\Controllers;

//use App\Models\User;
use Bouncer;


class MwrptController extends Controller {


	/**
	 * initail screen
	 *
	 * @return Response
	 */
	public function startscreen() {


		//echo "start screen";
		//$dates['start']="2015-04-01";
		//$dates['end']="2015-04-10";
		//return View::make('rpt')->with('dates', $dates);
		return View::make('rpt');

	}



	/**
	 * generate report
	 *
	 * @return json
	 */
	public function getreport() {



		/* HARD CODE */
		$rpt_version="RRV300";
		$serviceprovider_id=1; // STS


		/* DYNAMIC */
		$serviceprovider=\App\Models\Serviceprovider::where('id','=',$serviceprovider_id)->first(); // prvi serviceprovider (STS)

		$rpt_country=$serviceprovider->loccountry->name; //"CROATIA";
		$rpt_ARCname=$serviceprovider->name; //"ST Servis";
		$rpt_ARCcode=$serviceprovider->arccode; //"103301";


		/* CALCULATE */
		$rpt_editionDate="10/04/2015"; // DD/MM/YYYY
		$rpt_firstRepairDate="10/03/2015"; // DD/MM/YYYY
		$rpt_lastRepairDate="30/03/2015"; // DD/MM/YYYY

		$rpt_fileName="103301_2015-03.rpt";




		// direkt u browser
		/*
		$out = fopen('php://output', 'w');
		fputcsv($out, array('this','is some', 'csv "stuff", you know.'));
		fclose($out);
		*/

		// snima u fajl
		/*
		$list = array (
		    array('aaa', 'bbb', 'ccc', 'dddd'),
		    array('123', '456', '789'),
		    array('"aaa"', '"bbb"')
		);

		$fp = fopen('file.csv', 'w');

		foreach ($list as $fields) {
		    fputcsv($fp, $fields);
		}

		fclose($fp);
		*/




		$filename="test.rpt";



		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		//header("Content-type: text/plain");
		header("Content-Type: application/octet-stream; "); 
		header("Content-Transfer-Encoding: binary");
		//header("Content-Length: ". filesize("$filename").";");




		/* REPORT */
		$rpt="";


		//HEADER--
		//L1:
		$rpt.= $rpt_country.";".$rpt_ARCname.";".$rpt_ARCcode;
		for ($i=0;$i<43;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L2:
		$rpt.= $rpt_editionDate.";".$rpt_firstRepairDate.";".$rpt_lastRepairDate;
		for ($i=0;$i<43;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L3:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L4:
		$rpt.=$rpt_version;
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L5:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L6:
		$rpt.=$rpt_fileName;
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L7:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L8:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L9:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L10:
		for ($i=0;$i<45;$i++) $rpt.=";";
		$rpt.="\r\n";

		//L11: A0;..A45
		for ($i=0;$i<45;$i++) $rpt.="A".$i.";";
		$rpt.="A45";
		$rpt.="\r\n";



// DATA ROWS

		$radninalozi = \App\Models\Repairorder::where('serviceprovider_id','=',$serviceprovider_id)->get();


		$i = 0;
		foreach ($radninalozi as $rn) {

			$rpt_data[$i][0] = $rpt_ARCcode; // ARC code
			// 25 znakova
			$rpt_data[$i][1] = $rn->stsrepairorderno; //jobNumber
			$rpt_data[$i][2] = (trim($rn->customername." ".$rn->customerlastname)!=="") ? $rn->customername." ".$rn->customerlastname : $rn->pos->distributer->name; //customerName
			// 0/1
			$rpt_data[$i][3] = $rn->devicewarranty; //warranty? 0/1
			$rpt_data[$i][9] = $rn->deviceaccbattery; // ACC: Battery
			$rpt_data[$i][10] = $rn->deviceacccharger; // ACC: CHarger
			$rpt_data[$i][11] = $rn->deviceaccantenna; // ACC: Antenna
			// 15 znakova:
			$rpt_data[$i][4] = $rn->deviceincomingimei; //incoming IMEI
			$rpt_data[$i][6] = $rn->devicemanufactureddate;
			$rpt_data[$i][7] = $rn->deviceoutgoingimei; // ili incoming ili novi
			// 9 znakova:
			$rpt_data[$i][5] = $rn->deviceincomingsasref; //Incoming MobiWire SAS Reference
			$rpt_data[$i][8] = $rn->deviceoutgoingsasref; // swap SAS ref ili incoming sas
			//DD/MM/YYYY
			$rpt_data[$i][39] = $rn->posclaimdate;
			$rpt_data[$i][40] = $rn->possenddate;

			$rpt_data[$i][12] = $rn->devicebuydate;

			$rpt_data[$i][14] = ""; // datum ponude kupcu
			$rpt_data[$i][15] = ""; // datum prihvačanja ponude

			$rpt_data[$i][13] = $rn->stsroopendate;
			$rpt_data[$i][16] = $rn->stsclosingdate;

			// posebno?
			$rpt_data[$i][17] = $rn->customersymptom_id; // CUStOMER SYMPTOM CODE (2 char)
			$rpt_data[$i][18] = $rn->techniciansymptom_id; // SERVISER SYMPROM CODE (3 char)

			$rpt_data[$i][19] = $rn->deviceincomingswversion; // 3 ili 6 znakova
			$rpt_data[$i][20] = $rn->deviceoutgoingswversion; // 3 ili 6 znakova
			//$rpt_data[$i][21] = $rn->stsservicelevel->name(); // REPAIR LEVEL - 3 char maximum
			$rpt_data[$i][21] = $rn->stsservicelevel_id;//->name(); // REPAIR LEVEL - 3 char maximum

			// 9 znakova
			$rpt_data[$i][22] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][23] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][24] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][25] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][26] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][27] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][28] = ""; // SAS REF DIO U JAMSTVU
			$rpt_data[$i][29] = ""; // SAS REF DIO U JAMSTVU
			// 9 znakova
			$rpt_data[$i][30] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][31] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][32] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][33] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][34] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][35] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][36] = ""; // SAS REF DIO VAN JAMSTVA
			$rpt_data[$i][37] = ""; // SAS REF DIO VAN JAMSTVA

			// niš za sad
			$rpt_data[$i][38] = ""; // SPECIFIC PROCESS
			$rpt_data[$i][41] = ""; // datum preuzimanja u servisu
			$rpt_data[$i][42] = ""; // datum preuzimanja - krajnji kupac collect - AK JE KUPAC SAM DOSTAVIO PA SAD PREUZEO
			$rpt_data[$i][43] = ""; // Pack reference
			$rpt_data[$i][44] = ""; // Collect Point Specificity
			$rpt_data[$i][45] = ""; // FAULTY ELEMENT

			$i++;
		}





		//za test
		//echo str_replace("\n", "<br>", $rpt);

		// header
		echo $rpt;

		//data
		foreach($rpt_data as $rd) {
			foreach ($rd as $cell)	echo $cell.";";
			echo "\r\n";
		}

	}




}

