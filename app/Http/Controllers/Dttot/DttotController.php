<?php
namespace App\Http\Controllers\Dttot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DttotModel;

class DttotController extends Controller{
	
	public function __construct(){
	}

	public function index(Request $request){
		$nik = trim($request->input("nik"));
		$nama = trim($request->input("nama"));
		$tempat_lahir = trim($request->input("tempat_lahir"));
		$tanggal_lahir = trim($request->input("tanggal_lahir"));
		$alamat = trim($request->input("alamat"));
		$date = date_create($tanggal_lahir);
		$tgl_lahir = date_format($date, 'd/m/Y');
		$arrData = array();
		$now = date('d/m/Y');

		if($tgl_lahir == $now){
			$tgl_lahir = "";
		}

		/*$dttot = DB::table('dttot')
				->where('NIK', 'like', '%'.$nik.'%')
                ->where('NAMA', 'like', '%'.$nama.'%')
				->where('TEMPAT_LAHIR', 'like', '%'.$tempat_lahir.'%')
				->where('TANGGAL_LAHIR', 'like', '%'.$tgl_lahir.'%')
				->where('ALAMAT', 'like', '%'.$alamat.'%')
				->get();
				*/
		$dttot = DttotModel::where('NIK', 'like', '%'.$nik.'%')
                ->where('NAMA', 'like', '%'.$nama.'%')
				->where('TEMPAT_LAHIR', 'like', '%'.$tempat_lahir.'%')
				->where('TANGGAL_LAHIR', 'like', '%'.$tgl_lahir.'%')
				->where('ALAMAT', 'like', '%'.$alamat.'%')
				->paginate(15); //set row data in each page
				//->withQueryString();//passing parameter from sumbit form $_POST or $_GET
				//->get();
				

		/*foreach($dttot as $value){
			$arrData[] = $value;
		}*/

		//$data["data"] = $arrData;
		//$data["data"] = $dttot;
		//return view('dttot',$data);
		return view('dttot',compact('dttot'));
	}
	public function toprintpage(Request $request){
		//print_r($request->all());
		
		$nik = trim($request->input("nik"));
		$nama = trim($request->input("nama"));
		$tempat_lahir = trim($request->input("tempat_lahir"));
		$tanggal_lahir = trim($request->input("tanggal_lahir"));
		$alamat = trim($request->input("alamat"));
		$date = date_create($tanggal_lahir);
		$tgl_lahir = date_format($date, 'd/m/Y');
		$arrData = array();
		$now = date('d/m/Y');

		if($tgl_lahir == $now){
			$tgl_lahir = "";
		}
		
		$dttot = DttotModel::where('NIK', 'like', '%'.$nik.'%')
                ->where('NAMA', 'like', '%'.$nama.'%')
				->where('TEMPAT_LAHIR', 'like', '%'.$tempat_lahir.'%')
				->where('TANGGAL_LAHIR', 'like', '%'.$tgl_lahir.'%')
				->where('ALAMAT', 'like', '%'.$alamat.'%')
				->get();
		
		return view('cetak',compact('dttot'));
	}
}