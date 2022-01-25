<?php


namespace App\Http\Controllers\Job;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use File;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Utilities\Util;

class JobController extends Controller{
	
	public function __construct(){
		$this->ftpHost = "192.168.204.21";
		$this->ftpPort = 22;
		$this->ftpUser = "";
		$this->ftpPass = "";
		$this->ftpRootPath = "/files/";
		$this->ociUser = "";
		$this->ociPass = "";
		$this->ociHost = "//192.168.205.100:1521/jtofast";
		$this->recepient = array("taufan.septaufani@jto.co.id");
		$this->subject = "Data Payment Debitur ".date('d-m-Y');
		
		//for AR Tunggak
		$this->recepientArT = array("taufan.septaufani@jto.co.id");
		$this->subjectArT = "Laporan AR Tunggak ".date('d-m-Y');
		
		//set timezone
		date_default_timezone_set('Asia/Jakarta');
		
		//set to unlimited memory
		//ini_set('memory_limit', '-1');
		
		//set max of execution
		ini_set('max_execution_time', '1800'); //30minutes
	}
	
	public function loadTo(Request $request){ //utk data payment debitur (BJI & OJK)
		$id = $request->id;
		
		if($id == 1){
			$batch = "I";
		}else if($id == 2){
			$batch = "II";
		}else {
			$batch = $id;
		}
		
		$path1 = "PAYMENT/"; //primary path
		$filename = "DATA PAYMENT DEBITUR ".date('Ymd')."_BATCH ".$batch;
		$ext = ".xlsx"; //extension;
		
		
		$select = $this->dataOrcl($this->ociUser,$this->ociPass,$this->ociHost,$id); //get all data
		
		//save contract_no to temporary table
		foreach($select as $row){
			
			DB::table('batch_temp')->insert(
				[
						'contract_no' => $row['CONTRACT_NO'],
						'batch_no' => $id,
						'created_date' => date('Y-m-d H:i:s')
				]
			);
		}
		
		
		
		if(count($select) > 0 || !empty($select)){
			
			
			foreach ($select as $res){
				$typepay[] = $res["TYPEPAY"]; //get typepay for create categories in array
			}
			
			$au = array_unique($typepay); //remove duplicate value
			$av = array_values($au); //reset index of array
			
			for($i=0;$i<count($av);$i++){
				
				foreach($select as $data){
					if($data["TYPEPAY"] == $av[$i]){ //into typypay categories in array
						$vdata[$i][] = array_splice($data,1); //remove first element in array
					}
				}
				
			}
			
			//create excel
			$created = Excel::create($filename,function($excel)use($select,$vdata,$av){
				
				//sheet 1
				$excel->sheet('All',function($sheet)use($select){
						
						$sheet->fromArray(
							$select //data will be injected into excel file
						);
						
						
				});
				
				//next sheet with name of typepay categories
				for($i=0;$i<count($av);$i++){
					
					$excel->sheet($av[$i],function($sheet)use($vdata,$i){
						
						$sheet->fromArray(
							$vdata[$i] //data will be injected into excel file
						);    						
						
					});
				}
				
			})->store('xlsx');
			
			if($created == true){
				
				//get initial class sftpadapter
				$initSftp = Util::SetUpSFTP($this->ftpHost,$this->ftpPort,$this->ftpUser,$this->ftpPass,$this->ftpRootPath); 
				//send to ftp server
				Util::sendToFtp($filename.$ext,$path1,$initSftp);
				
				//send to email
				$FilePath = storage_path('exports/').$filename.$ext;
				$subject = $this->subject;
				$recepient = $this->recepient;			
				$dataForEmail = array("batch"=>$batch,"file"=>1);	
				$fview = "payview";
				//Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
				
				Log::info($filename.$ext." created and sent");
				
			}else{
				Log::info($filename.$ext." failed to create and send");
				
			}				
			
		}else{
			Log::info("Batch ".$batch." No records found");
			
			//send to email
			$subject = $this->subject;
			$recepient = $this->recepient;
			$dataForEmail = array("batch"=>$batch,"file"=>0);
			$fview = "payview";
			Util::sendToEmail(null,$subject,$recepient,$dataForEmail,$fview);
		}
		
		
	}
	
	public function dataOrcl($ociUser,$ociPass,$ociHost,$batch_no){ //utk data payment debitur (BJI & OJK)
		
		$timestamp = strtotime(date('y-m-d'));
		$day = strtolower(date('D', $timestamp));
		
		if($day == 'mon'){
			$cond = "orcl_pay_deb_mon"; //monday
		}else{
			$cond = "orcl_pay_deb"; //other day
		}
		
		//get query for $qry
		$param = DB::table('param')
			->select('param_value')
			->where('param_name','=',$cond)
			->first();
		
		$conn = oci_connect($ociUser,$ociPass,$ociHost);
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		
		if($batch_no == 2){
			$select = DB::table('batch_temp')
				->select('contract_no')
				->where('batch_no','=','1')
				->where('created_date','like','%'.date("Y-m-d").'%')
				->distinct()
				->get();
				
			$placeholders = array();
			$select = json_decode(json_encode($select),true);

			foreach($select as $filter) {
			  $placeholders[] = "'".$filter["contract_no"]."'";
			}
			
			$filters = implode(',',$placeholders);
			
			$paramValue = explode('ORDER',$param->param_value);
			
			$qry = $paramValue[0]." AND A.CONTRACT_NO NOT IN(".$filters.") ORDER ".$paramValue[1];
		}else{
			$qry = $param->param_value;
		}
		
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		oci_fetch_all($stid,$res,null,null,OCI_FETCHSTATEMENT_BY_ROW);
		
		return $res;
	}
	
	public function dataOrclArTunggak($ociUser,$ociPass,$ociHost){ //utk data AR Tunggak
		//get query for $qry
		$param = DB::table('param')
			->select('param_value')
			->where('param_name','=','orcl_ar_tunggak')
			->first();
		
		$conn = oci_connect($ociUser,$ociPass,$ociHost);
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		
		$qry = $param->param_value;		
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		oci_fetch_all($stid,$res,null,null,OCI_FETCHSTATEMENT_BY_ROW);
		
		return $res;
	}
	
	public function jobArTunggak(){ //utk data AR Tunggak
	
		$filename = "laporan ar yang tunggak ".date('d-m-Y');
		$ext = ".xlsx"; //extension;
		$body = "File Terlampir";
		
		$select = $this->dataOrclArTunggak($this->ociUser,$this->ociPass,$this->ociHost);
		
		//print_r("<pre>");
		//print_r($select);
		//print_r("</pre>");
		
		if(count($select) > 0 || !empty($select)){
			
			$created = Util::ExcelFileCreated($filename,$select);
			
			if($created == true){
				
				//send to email
				$FilePath = storage_path('exports/').$filename.$ext;
				$subject = $this->subjectArT;
				$recepient = $this->recepientArT;				
				$dataForEmail = array("file"=>1,"filename"=>$filename,"body"=>$body);	
				$fview = "arview";
				Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
				
				Log::info($filename.$ext." created and sent");
				
			}else{
				Log::info($filename.$ext." failed to create and send");
				
			}				
			
		}else{
			Log::info("For ".$filename." No records found");
			
			//send to email
			$subject = $this->subjectArT;
			$recepient = $this->recepientArT;	
			$dataForEmail = array("file"=>0,"filename"=>$filename,"body"=>$body);
			$fview = "arview";
			Util::sendToEmail(null,$subject,$recepient,$dataForEmail,$fview);
		}
	}
	
	public function cronjob(){
		
		//get values of param
		$param = DB::table('param')
			->select('*')
			->where('param_isactive','=','Y')
			->get();
		
		
		
		if(count($param) > 0 || !empty($param)){
		
			//to array
			$data = json_decode(json_encode($param),true);
			
			//extract
			foreach($data as $result){
				
				$name = $result["param_name"];
				$qry = $result["param_value"];
				$recepient = explode(",",$result["param_sendto"]);
				$subject = $result["param_subject"];
				$body = $result["param_body"];
				$withFTP = $result["param_withftp"];
				$withEmail = $result["param_withemail"];
				$filename = $result["param_filename"]." ".date('d-m-Y');
				$ext = ".xlsx"; //extension;
				$path1 = "PAYMENT/"; //primary path
				
				//get oracle data
				$select  = Util::GetOrclData($this->ociUser,$this->ociPass,$this->ociHost,$qry);
				
				if(count($select) > 0 || !empty($select)){
					
					//export as excel file
					$created = Util::ExcelFileCreated($filename,$select);
					
					if($created == true){
						
						Log::info($filename.$ext." created");
						
						if($withEmail == 'Y'){
						
							//send to email
							$FilePath = storage_path('exports/').$filename.$ext;		
							$dataForEmail = array("file"=>1,"filename"=>$filename,"body"=>$body);	
							$fview = "cronview";
							Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
							
							Log::info("Email ".$filename.$ext." sent");
							
						}else if($withFTP == 'Y'){
							
							//get initial class sftpadapter
							$initSftp = Util::SetUpSFTP($this->ftpHost,$this->ftpPort,$this->ftpUser,$this->ftpPass,$this->ftpRootPath); 
							//send to ftp server
							Util::sendToFtp($filename.$ext,$path1,$initSftp);
							
							Log::info("FTP ".$filename.$ext." sent");
						}
						
						
					}else{
						Log::info($filename.$ext." failed to create and send");
					}
					
				}else{
					
					if($withEmail == 'Y'){
					
						//send to email
						$FilePath = storage_path('exports/').$filename.$ext;		
						$dataForEmail = array("file"=>0,"filename"=>$filename,"body"=>$body);	
						$fview = "cronview";
						Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
					}
						
					Log::info("For ".$name." No records found");
				}
			}
		}
		
		
	}
	
	public function finjob(){
		
		$timestamp = strtotime(date('y-m-d'));
		$day = strtolower(date('D', $timestamp));
		
		if($day == 'mon'){
			$cond = "orcl_pay_fin_mon"; //monday
		}else{
			$cond = "orcl_pay_fin"; //other day
		}
		
		//get query for $qry
		$param = DB::table('param')
			->select('*')
			->where('param_name','=',$cond)
			->first();
			
		//print_r($param);
			
		if(count($param) > 0 || !empty($param)){
			
			//to array
			$data = json_decode(json_encode($param),true);
			
			//print_r("<pre>");
			//print_r($data["param_value"]);
			//print_r("</pre>");
			
			//extract
			$name = $data["param_name"];
			$qry = $data["param_value"];
			$recepient = explode(",",$data["param_sendto"]);
			$subject = $data["param_subject"]." ".date('d-m-Y')." Test IT";
			$body = $data["param_body"];
			$withFTP = $data["param_withftp"];
			$withEmail = $data["param_withemail"];
			$filename = $data["param_filename"].date('dmY')."NEW";
			$ext = ".xlsx"; //extension;
			$path1 = "PAYMENT/"; //primary path
			
			//get oracle data
			$select  = Util::GetOrclData($this->ociUser,$this->ociPass,$this->ociHost,$qry);
			
			if(count($select) > 0 || !empty($select)){
				
				//export as excel file
				/*$created = Excel::create($filename,function($excel)use($select){
					$excel->sheet('Sheet1',function($sheet)use($select){
						$sheet->fromArray(
							$select //data will be injected into excel file
						);
					});
				})->store('xlsx');
				*/
				$created = Util::ExcelFileCreated($filename,$select);
				
				if($created == true){
					
					Log::info($filename.$ext." created");
					
					if($withEmail == 'Y'){
					
						//send to email
						$FilePath = storage_path('exports/').$filename.$ext;		
						$dataForEmail = array("file"=>1,"filename"=>$filename,"body"=>$body);	
						$fview = "cronview";
						Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
						
						Log::info("Email ".$filename.$ext." sent");
						
					}else if($withFTP == 'Y'){
						
						//get initial class sftpadapter
						$initSftp = Util::SetUpSFTP($this->ftpHost,$this->ftpPort,$this->ftpUser,$this->ftpPass,$this->ftpRootPath); 
						//send to ftp server
						Util::sendToFtp($filename.$ext,$path1,$initSftp);
						
						Log::info("FTP ".$filename.$ext." sent");
					}
					
					
				}else{
					Log::info($filename.$ext." failed to create and send");
				}
				
			}else{
				
				if($withEmail == 'Y'){
				
					//send to email
					$FilePath = storage_path('exports/').$filename.$ext;		
					$dataForEmail = array("file"=>0,"filename"=>$filename,"body"=>$body);	
					$fview = "cronview";
					Util::sendToEmail($FilePath,$subject,$recepient,$dataForEmail,$fview);
				}
					
				Log::info("For ".$name." No records found");
			}
			
		}
		
	}
}
?>