<?php


namespace App\Http\Controllers\Exceltest;


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
use App\Http\Utilities\MyUtil;

class ExcelController extends Controller{
	
	public function __construct(){
		$this->ftpHost = "192.168.204.21";
		$this->ftpPort = 22;
		$this->ftpUser = "";
		$this->ftpPass = "";
		$this->ftpRootPath = "/files/";
		$this->ociUser = "";
		$this->ociPass = "";
		$this->ociHost = "//192.168.205.100:1521/jtofast";
		$this->recepient = array("edward.saragih@jto.co.id","abdul.basir@jto.co.id");
		//$this->recepient = array("taufan.septaufani@jto.co.id");
		$this->subject = "DATA PAYMENT DEBITUR";
		
		//set timezone
		date_default_timezone_set('Asia/Jakarta');
	}
	
	public function loadTo(Request $request){
		$id = $request->id;
		$batch = $id;
		/*if($id == 1){
			$batch = "I";
		}else if($id == 2){
			$batch = "II";
		}else {
			$batch = $id;
		}*/
		
		$path1 = "PAYMENT/"; //primary path
		$filename = "DATA PAYMENT DEBITUR ".date('Ymd')."_BATCH ".$batch;
		$ext = ".xlsx"; //extension;
		$data = array();
		
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
			
			//Inject number to select data
			for($i=0;$i<count($select);$i++){
				$data[] = array_merge(
					array("NO"=>($i+1)),
					$select[$i]
				);
			}
		
			$created = MyUtil::ExcelFileCreated($filename,$data);
			
			if($created == true){
				//get initial class sftpadapter
				$initSftp = MyUtil::SetUpSFTP($this->ftpHost,$this->ftpPort,$this->ftpUser,$this->ftpPass,$this->ftpRootPath); 
				//send to ftp server
				MyUtil::sendToFtp($filename.$ext,$path1,$initSftp);
				
				//send to email
				$FilePath = storage_path('exports/').$filename.$ext;
				$subject = $this->subject." ".date('Ymd')." BATCH ".$batch;
				$recepient = $this->recepient;			
				$dataForEmail = array("batch"=>$batch,"file"=>1);				
				MyUtil::sendToEmail($FilePath,$subject,$recepient,$dataForEmail);
				
				//truncate table batch_temp
				//DB::table('batch_temp')->truncate();
				
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
			MyUtil::sendToEmail(null,$subject,$recepient,$dataForEmail);
		}
		
		
	}
	
	public function dataOrcl($ociUser,$ociPass,$ociHost,$batch_no){
		
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
				
			
				
			$placeholders1 = array();
			$placeholders2 = array();
			$select = json_decode(json_encode($select),true);
			
			//split the select into 2 array
			$length = count($select)/2;
			$start = $length;
			$select1 = array_slice($select,0,$length,true);
			$select2 = array_slice($select,$start,count($select),true);
			
			//create two placeholder
			foreach($select1 as $filter) {
			  $placeholders1[] = "'".$filter["contract_no"]."'";
			}
			
			foreach($select2 as $filter) {
			  $placeholders2[] = "'".$filter["contract_no"]."'";
			}
			
			$filters1 = implode(',',$placeholders1);
			$filters2 = implode(',',$placeholders2);
			
			$paramValue = explode('ORDER',$param->param_value);
			
			$qry = $paramValue[0]." AND A.CONTRACT_NO NOT IN(".$filters1.")";
			$qry.= " UNION ALL ".$paramValue[0]." AND A.CONTRACT_NO NOT IN(".$filters2.")";
			$qry.= " ORDER BY 12";
		}else{
			$qry = $param->param_value;
		}
				
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		oci_fetch_all($stid,$res,null,null,OCI_FETCHSTATEMENT_BY_ROW);
		
		return $res;
	}
}
?>