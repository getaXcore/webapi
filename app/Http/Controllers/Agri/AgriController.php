<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 07/01/2021
 * Time: 11:23
 */

namespace App\Http\Controllers\Agri;


use App\Http\Controllers\Controller;
use App\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AgriController extends Controller
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
        return response($this->codeOK,200);
    }

	public function getBudget(){
		$budget = DB::table('budget')
            ->select('value')
            ->get();

		foreach($budget as $val){
			$valBudget[] = $val->value;
		}

		return response($valBudget,200);
	}

	public function getVehicle(){
		$vehicle = DB::table('vehicle')
            ->select('name')
            ->get();

		foreach($vehicle as $val){
			$valVehicle[] = $val->name;
		}

		return response($valVehicle,200);
	}
	
	public function getVehicleImage(Request $request){
		$param = json_decode($request->getContent(),true);
		$name = trim($param["name"]);
		$type = trim($param["type"]);
		
		$vehicle = DB::table('vehicle')
            ->select('imageproduct')
			->where('name', $name)
			->orWhere('type', $type)
            ->first();

			$valVehicle[] = $vehicle->imageproduct;

		return response($valVehicle,200);
	}


}