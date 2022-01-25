<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 14/11/2018
 * Time: 11:39
 */

namespace App\Http\Utilities;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use File;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class MyUtil
{
    public $defaultId;
    public $format;
    public $char;
    public $length;

    public function __construct()
    {
        $this->length = 10;
        $this->char = 0;
        $this->defaultId = 1;
        $this->format = "%08s";
    }

    public function customId(){
        return sprintf($this->format,$this->defaultId);
    }
	
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
	
	public static function sendToEmail($FilePath = null,$subject,$to = array(),$data = array()){
		Mail::alwaysFrom('no-reply@jto.co.id','JTO Finance');
		Mail::send('testemail',$data,function($message) use($FilePath,$subject,$to){
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

}