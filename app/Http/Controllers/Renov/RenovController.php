<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 21/10/2019
 * Time: 09:23
 */

namespace App\Http\Controllers\Renov;


use App\Http\Controllers\Controller;
use App\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RenovController extends Controller
{
    public $statusCode = array();
    private $codeOK;
    private $codeFAIL;

    public function __construct()
    {
        $this->codeOK = $this->statusCode[0] = array("statusCode"=>"01","description"=>"OK");
        $this->codeFAIL = $this->statusCode[1] = array("statusCode"=>"02","description"=>"FAIL");
    }

    public function index(){
        return response($this->codeOK,200);
    }

	public function regRenov(Request $request){
		$param = json_decode($request->getContent(),true);
		
		$fullName = trim($param['fname']);
		$gender = trim($param['gender']);
		$perumahan = trim($param['perumahan']);
		$fulladdress = trim($param['faddress']);
		$maritalStatus = trim($param['mstatus']);
		$occupation = trim($param['occupate']); 
		$mobileNumber = trim($param['mPhone']);
		$typeofRenov = trim($param['typeofR']);

		//insert to table
		DB::table('regrenovasi')->insert(
            [
                'fullname' => $fullName,
                'gender' => $gender,
                'perumahan' => $perumahan,
                'fulladdress' => $fulladdress,
                'maritalstatus' => $maritalStatus,
				'occupation'=> $occupation,
				'mobilenumber'=>$mobileNumber,
				'typeof'=>$typeofRenov,
				'created'=> date('Y-m-d H:i:s')
            ]
        );

		//send email
		//Mail::send(new Mailer($fullName,$gender,$perumahan,$fulladdress,$maritalStatus,$occupation,$mobileNumber,$typeofRenov));
		//\Illuminate\Contracts\Mail\Mailer::to('getaufan@gmail.com')->send(new Mailer($fullName,$gender,$perumahan,$fulladdress,$maritalStatus,$occupation,$mobileNumber,$typeofRenov));
		
		if ($gender == 'F'){
			$descGender = "Perempuan";
		}else{
			$descGender = "Laki-Laki";
		}
		if($maritalStatus == 0){
			$descMStatus = "Belum Kawin";
		}else{
			$descMStatus = "Kawin";
		}
		
		
		$arrData = array(
		    "fname" => $fullName,
            "gender" => $descGender,
            "perumahan" => $perumahan,
            "faddress" => $fulladdress,
            "mstatus" => $descMStatus,
            "occupate" => $occupation,
            "mPhone" => $mobileNumber,
            "typeofR" => $typeofRenov
        );

		//send email
		Mail::alwaysFrom('financejto@gmail.com','JTO Finance');
		Mail::send('email',$arrData,function ($message) use($fullName){
		    $message->to('getaufan@gmail.com')
			->to('getaufan@yahoo.com')
            ->subject('Pengajuan Renovasi Rumah a/n '.$fullName);
        });
		

		//return response ok
		return response($this->codeOK,200);
	}

}