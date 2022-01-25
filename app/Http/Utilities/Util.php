<?php
namespace App\Http\Utilities;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use File;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class Util{
	
	public function __construct(){}
	
	public static function sendToFtp($file,$newpath,$sftp){	
		
		$is_exists = $sftp->has($newpath.$file);
		if($is_exists == 1){
			$sftp->delete($newpath.$file); //delete file first if exists in new path sftp
		}
		
		$localFile = File::get(storage_path('exports/').$file); //get local file
		$sftp->put($file,$localFile); //upload to root path of sftp		
		$sftp->copy($file,$newpath.$file); //new path : PAYMENT, copy to new path of sftp
		$sftp->delete($file); //delete file in root path sftp
	}
	
	public static function SetUpSFTP($host,$port,$username,$password,$rootpath){

		$filesystem = new Filesystem(new SftpAdapter([
			'host' => $host,
			'port' => $port,
			'username' => $username,
			'password' => $password,
			//'privateKey' => 'path/to/or/contents/of/privatekey',
			'root' => $rootpath, //as root path
			//'timeout' => 10,
		]));

		return $filesystem;
	}
	
	public static function sendToEmail($FilePath = null,$subject,$to = array(),$data = array(),$view){
		Mail::alwaysFrom('no-reply@jto.co.id','JTO Finance');
		Mail::send($view,$data,function($message) use($FilePath,$subject,$to){
			$message->to($to)
					->subject($subject);
			if($FilePath != null){
				$message->attach($FilePath);
			}
		});
	}
	
	public static function ExcelFileCreated($filename,$data = array()){
		$created = Excel::create($filename,function($excel)use($data){
			$excel->sheet('Sheet1',function($sheet)use($data){
				$sheet->fromArray(
					$data //data will be injected into excel file
				);
			});
		})->store('xlsx');
		
		return $created;
	}
	
	public static function ExcelFileCreatedMoreSheet($filename,$data = array()){
		$created = Excel::create($filename,function($excel)use($data){
			$excel->sheet('Sheet1',function($sheet)use($data){
				$sheet->fromArray(
					$data //data will be injected into excel file
				);
			});
		})->store('xlsx');
		
		return $created;
	}
	
	public static function GetOrclData($ociUser,$ociPass,$ociHost,$query){
		
		$conn = oci_connect($ociUser,$ociPass,$ociHost);
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		
		$qry = $query;		
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		oci_fetch_all($stid,$res,null,null,OCI_FETCHSTATEMENT_BY_ROW);
		
		oci_close($conn);
		
		return $res;
	}
}