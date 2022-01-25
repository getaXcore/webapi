<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 25/10/2021
 * Time: 13:50
 */

namespace App\Http\Controllers\Insur;


use App\Http\Controllers\Controller;
use App\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InsurController extends Controller
{
    public $statusCode = array();
    private $codeOK;
    private $codeFAIL;
	private $codeSize;
	private $codeType;

    public function __construct()
    {
        $this->codeOK = $this->statusCode[0] = array("statusCode"=>"01","description"=>"OK");
        $this->codeFAIL = $this->statusCode[1] = array("statusCode"=>"02","description"=>"FAIL");
		$this->codeSize = $this->statusCode[2] = array("statusCode"=>"03","description"=>"Ukuran file tidak boleh melebihi 200kb");
		$this->codeType = $this->statusCode[3] = array("statusCode"=>"04","description"=>"Tipe file tidak boleh selain png, jpg, pdf dan msword");
    }

    public function index(){
        //return response($this->codeOK,200);
		
		//foler utama
		$dir = public_path()."/media/";
		
		$filename = $dir."response.json";
		
		$contents = json_decode(file_get_contents($filename,true),true);
		
		/*
		//utk download filenya pdfnya
		$fileins = base64_decode($contents["PDF64"]);
		$file = $dir.'ins.pdf';
		file_put_contents($file, $fileins);

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
		//End
		*/
		
		//return response($contents,200);
		
		print_r($contents);
    }
	
}