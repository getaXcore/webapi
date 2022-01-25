<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstController extends Controller
{
	public function __construct()
    {
        $this->codeOK = $this->statusCode[0] = array("statusCode"=>"01","description"=>"OK");
        $this->codeFAIL = $this->statusCode[1] = array("statusCode"=>"02","description"=>"FAIL");
    }
	
	public function index(){
		
		return view('a');
	}
	public function hit(Request $request){
		
		$input1 = $request->input("input1");
		$input2 = $request->input("input2");
		
		
		
		for($i=$input1;$i<=$input2;$i++){ 
			$a = 0;
			for($j=$input1;$j<=$i;$j++){ 
				if($i % $j == 0){ 
					$a++;
				}
			}
			if($a == 2){
			 echo $i." ";
			}
		}
	}
	
	public function add(Request $request){
		$param = json_decode($request->getContent(),true);
		
		//print_r($param);
		
		$nama = trim($param["nama"]);
		$alamat = trim($param["alamat"]);
		$tglhr = trim($param["tglhr"]);
		$tglMsKerja = trim($param["tglMasuKerja"]);
		
		$rawidKry = DB::table('karyawan')
			->select('*')
			->count();
		$rawidRwy = DB::table('riwayatakademis')
			->select('*')
			->count();
		
		$idKry = "K".($rawidKry+1).date('dmY');
		
		
		DB::table('karyawan')->insert(
            [
				'id_karyawan' => $idKry,
                'nama' => $nama,
                'alamat' => $alamat,
                'tglahir' => $tglhr,
                'tglmasukerja' => $tglMsKerja
            ]
        );
		
		//print_r($param["riwayatAkademis"]);
		$x = 1;
		for($i=0;$i<count($param["riwayatAkademis"]);$i++){
			$x++;
			DB::table('riwayatakademis')->insert(
				[
					'id_riwayat' => "AK".($rawidRwy+$x).$i.date('dmY'),
					'id_karyawan' => $idKry,
					'deskripsi' => $param["riwayatAkademis"][$i]
				]
			);
			
		}
		
		
		return response($this->codeOK,200);
		
	}
	
	public function getAll(){
		
		$Kry = DB::table('karyawan')
			->select('id_karyawan','nama','alamat','tglahir','tglmasukerja')
			->get();
			
		
		
		foreach($Kry as $dKry){
			
			$Ak = DB::table('riwayatakademis')
				->select('*')
				->where('id_karyawan','=',$dKry->id_karyawan)
				//->orderBy('id_karyawan')
				->get();
				
			$data = json_decode(json_encode($Ak),true);
			
				
				
			
			for($i=0;$i<count($data);$i++){
				
				if($data[$i]["id_karyawan"] == $dKry->id_karyawan){
					
					$rwyAk[$dKry->id_karyawan][] = $data[$i]["deskripsi"];
				}else{
					$rwyAk[$dKry->id_karyawan] = array();
				}
			}
			
			
			
			
			$kryData["data"][] = array(
				"idKaryawan" => $dKry->id_karyawan,
				"nama" => $dKry->nama,
				"alamat" => $dKry->alamat,
				"tglhr" => $dKry->tglahir,
				"tglMasuKerja" => $dKry->tglmasukerja,
				"riwayatAkademis" => $rwyAk[$dKry->id_karyawan]
				
			);
			
			
			
		}
		
		return response(array_merge($this->codeOK,$kryData));
	}
	
	public function update(Request $request){
		$param = json_decode($request->getContent(),true);
		
		$idKaryawan = trim($param["idKaryawan"]);
		$nama = trim($param["nama"]);
		$alamat = trim($param["alamat"]);
		$tglhr = trim($param["tglhr"]);
		$tglMsKerja = trim($param["tglMasuKerja"]);
		
		
		if(!empty($param["riwayatAkademis"]) || $param["riwayatAkademis"] == null || count($param["riwayatAkademis"]) !== 0){
			
			
			DB::table('riwayatakademis')->where('id_karyawan','=',$idKaryawan)->delete();
			
			$rawidRwy = DB::table('riwayatakademis')
				->select('*')
				->count();
			
			
				$x = 1;
				for($i=0;$i<count($param["riwayatAkademis"]);$i++){
					$x++;
					DB::table('riwayatakademis')->insert(
						[
							'id_riwayat' => "AK".($rawidRwy+$x).$i.date('dmY'),
							'id_karyawan' => $idKaryawan,
							'deskripsi' => $param["riwayatAkademis"][$i]
						]
					);
					
				}
			
			
		}
		
		DB::table('karyawan')
            ->where('id_karyawan', $idKaryawan)
            ->update(['nama' => $nama,'alamat' => $alamat,'tglahir' => $tglhr,'tglmasukerja' =>$tglMsKerja]);
			
		return response($this->codeOK);
	}
	
	public function del(Request $request){
		$idKaryawan = $request->id;
		
		DB::table('riwayatakademis')->where('id_karyawan','=',$idKaryawan)->delete();
		DB::table('karyawan')->where('id_karyawan','=',$idKaryawan)->delete();
		
		return response($this->codeOK);
	}
}