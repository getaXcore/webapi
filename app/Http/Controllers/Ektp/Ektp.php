<?php


namespace App\Http\Controllers\Ektp;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Ektp extends Controller
{
    public function __construct()
    {
        $this->OK = array("code"=>1,"description"=>"success");
        $this->notOK = array("code"=>0,"description"=>"no data");
        $this->user_id ="";
		//$this->user_id = "10673171202101272JTO";
        $this->password = "";
		//$this->password = "123";
        $this->auth = "";
        $this->urls = "http://172.16.160.128:8000/dukcapil/get_json/jtrust_olym/nik_verifby_elemen";
		//$this->urls = "http://172.16.160.84:8000/dukcapil/get_json/jtrust_olym/call_verify";
        $this->ips = "192.168.38.1"; //default ip
		$this->ociUser = "";
		$this->ociPass = "";
		$this->ociHost = "//192.168.205.100:1521/jtofast";
		
    }

    public function index(){
        return view("mainektp");
    }
    public function checkTemplateShort(){
        return view("pagektpold");
    }
	
	public function checkOnTable(Request $request){
		$param = json_decode($request->getContent(),true);
		$dataid = trim($param["dataid"]);
        $nik = trim($param["nik"]);
        $type = trim($param["type"]);
		$typedeb = trim($param["optDeb"]);
		
		$ociData = $this->getFromOci($dataid,$typedeb,$nik);
		
        $select = DB::table('nikdata')
			->where([
				['nik', '=', $nik],
				['dataid', '=', $dataid]
			])
			->count();
			
			//print_r(count($ociData));
			//print_r($ociData);
			//print_r($select);
			

        if($select > 0 && count($ociData) > 1){
            if ($type == "1"){
                return response($this->OK,200);
            }elseif ($type == "2"){
                $data = array();
				
				$getOciData = array_change_key_case($ociData,CASE_LOWER);
				
				$select = DB::table('nikdata')
					->select('nik','nama','tglahir','tempatlahir','jeniskelamin','status','jenispekerjaan','alamat','rt','rw','kelurahan','kecamatan','kotakabupaten','propinsi')
					->where([
						['nik', '=', $getOciData["nik"]],
						['dataid', '=', $dataid]
					])
					->get();
					
				if(count($select) > 0){
					$mainData = json_decode(json_encode($select),true);
					$cleanData = $mainData[0];
					
					$data["data"]["nik"] = trim($cleanData["nik"]);
					$data["data"]["nama"] = trim($cleanData["nama"]);
					$data["data"]["tglahir"] = trim($cleanData["tglahir"]);
					$data["data"]["tempatlahir"] = trim($cleanData["tempatlahir"]);
					$data["data"]["jeniskelamin"] = trim($cleanData["jeniskelamin"]);
					$data["data"]["status"] = trim($cleanData["status"]);
					$data["data"]["jenispekerjaan"] = trim($cleanData["jenispekerjaan"]);
					$data["data"]["alamat"] = trim($cleanData["alamat"]);
					$data["data"]["rt"] = trim($cleanData["rt"]);
					$data["data"]["rw"] = trim($cleanData["rw"]);
					$data["data"]["kelurahan"] = trim($cleanData["kelurahan"]);
					$data["data"]["kecamatan"] = trim($cleanData["kecamatan"]);
					$data["data"]["kotakabupaten"] = trim($cleanData["kotakabupaten"]);
					$data["data"]["propinsi"] = trim($cleanData["propinsi"]);
					
					
					$data["sysdata"]["nik"] = trim($getOciData["nik"]);
					$data["sysdata"]["nama"] = trim($getOciData["nama"]);
					$data["sysdata"]["tglahir"] = trim($getOciData["tglahir"]);
					$data["sysdata"]["tempatlahir"] = trim($getOciData["tempatlahir"]);
					$data["sysdata"]["jeniskelamin"] = trim($getOciData["jeniskelamin"]);
					$data["sysdata"]["status"] = trim($getOciData["status"]);
					$data["sysdata"]["jenispekerjaan"] = trim($getOciData["jenispekerjaan"]);
					$data["sysdata"]["alamat"] = trim($getOciData["alamat"]);
					$data["sysdata"]["rt"] = trim($getOciData["rt"]);
					$data["sysdata"]["rw"] = trim($getOciData["rw"]);
					$data["sysdata"]["kelurahan"] = trim($getOciData["kelurahan"]);
					$data["sysdata"]["kecamatan"] = trim($getOciData["kecamatan"]);
					$data["sysdata"]["kotakabupaten"] = trim($getOciData["kotakabupaten"]);
					$data["sysdata"]["propinsi"] = trim($getOciData["propinsi"]);
					
					return response(array_merge($this->OK,$data),200);
				}else{
					return response($this->notOK,200);
				}
			}else{
				return response($this->notOK,200);
			}
		}else{
            return response($this->notOK,200);
        }
		
	}
    public function fetching(Request $request){
        $param = json_decode($request->getContent(),true);
        $nik = trim($param["nik"]);
        $dataid = trim($param["dataid"]);
		$typedeb = trim($param["optDeb"]);

        $select = DB::table('nik')
            ->select('ip')
            ->where('nik',$nik)
            ->first();

        if (!empty($select)){
            $this->ips = $select->ip;
        }
		
		$getOciData = $this->getFromOci($dataid,$typedeb,$nik);
		
		//print_r($getOciData);
		
		if(count($getOciData) > 1){
			$ociData = array_change_key_case($getOciData,CASE_LOWER);
			
			$body = array(
				"NIK" =>  $nik,
				"Nama_lgkp" => $ociData["nama"],
				"JENIS_KLMIN" => $ociData["jeniskelamin"],
				"Tmpt_lhr" => $ociData["tempatlahir"],
				"Tgl_lhr" => $ociData["tglahir"],
				"STATUS_KAWIN"=> $ociData["status"],
				"JENIS_PKRJN"=> $ociData["jenispekerjaan"],
				"PROP_NAME"=> $ociData["propinsi"],
				"KAB_NAME"=> $ociData["kotakabupaten"],
				"KEC_NAME"=> $ociData["kecamatan"],
				"KEL_NAME"=> $ociData["kelurahan"],
				"ALAMAT"=> $ociData["alamat"],
				"NO_RT"=> $ociData["rt"],
				"NO_RW"=> $ociData["rw"],
				"treshold"=> 90,
				"user_id"=> $this->user_id,
				"password"=> $this->password,
				"ip_user"=> $this->ips
			);

			$opts = array(
				'http'=>array(
					'method'=>"POST",
					'header'=>
						"Content-Type: application/json\r\n"
						."Authorization: ".$this->auth."\r\n",
					'content' => json_encode($body),
					'timeout' => 60
				)
			);

			$context = stream_context_create($opts);

			// Open the file using the HTTP headers set above
			$file = file_get_contents($this->urls, false, $context);

			$res = json_decode($file);

			$total = count((array)$res->content[0]);
			
			//print_r((array)$res->content[0]);

			if ($total < 2){
				return response($this->notOK,200);
			}else{
				$NIK = $nik;
				$NAMA_LGKP = $res->content[0]->NAMA_LGKP;
				$JENIS_KLMIN = $res->content[0]->JENIS_KLMIN;
				$TMPT_LHR = $res->content[0]->TMPT_LHR;
				$TGL_LHR = $res->content[0]->TGL_LHR;
				$STATUS_KAWIN = $res->content[0]->STATUS_KAWIN;
				$JENIS_PKRJN = $res->content[0]->JENIS_PKRJN;
				$PROP_NAME = $res->content[0]->PROP_NAME;
				$KAB_NAME = $res->content[0]->KAB_NAME;
				$KEC_NAME = $res->content[0]->KEC_NAME;
				$KEL_NAME = $res->content[0]->KEL_NAME;
				$ALAMAT = $res->content[0]->ALAMAT;
				$RT = $res->content[0]->NO_RT;
				$RW = $res->content[0]->NO_RW;
				$NAMA_LGKP_IBU = "";

				//insert to table
				DB::table('nikdata')->insert(
					[
						'nik' => $NIK,
						'dataid' => $dataid,
						'nama' => $NAMA_LGKP,
						'jeniskelamin' => $JENIS_KLMIN,
						'tempatlahir' => $TMPT_LHR,
						'tglahir' => $TGL_LHR,
						'status'=> $STATUS_KAWIN,
						'jenispekerjaan'=>$JENIS_PKRJN,
						'propinsi'=>$PROP_NAME,
						'kotakabupaten'=>$KAB_NAME,
						'kecamatan'=>$KEC_NAME,
						'kelurahan'=>$KEL_NAME,
						'alamat'=> $ALAMAT,
						'rt'=>$RT,
						'rw'=>$RW,
						'namalengkapibu'=>$NAMA_LGKP_IBU
					]
				);
				
				DB::table('nik')->insert(
					[
						'nik' => $NIK,
						'nktr' => $dataid
					]
				);

				return response($this->OK,200);
			}
		}else{
			return response($this->notOK,200);
		}
    }
    public function gmd5(){
        print_r(base64_encode("10673174202008281Meilyana_Bintoro:JyTf2820"));
    }
	public function getFromOci($aplno,$typedeb,$nik){
		$conn = oci_connect($this->ociUser,$this->ociPass,$this->ociHost);
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		if($typedeb == '0'){
			$qry = "
			SELECT A.APPL_NO,B.IDENTITY_NO NIK,A.CUST_NAME NAMA,TO_CHAR(A.BIRTH_DATE,'DD/MM/YYYY') AS TGLAHIR,
			UPPER(TRIM(A.BIRTH_PLACE )) AS TEMPATLAHIR,
			CASE
				WHEN A.CUST_SEX='M' THEN 'Laki-Laki'
				WHEN A.CUST_SEX='F' THEN 'Perempuan'
			END AS JENISKELAMIN,
			CASE
				WHEN A.MARITAL_STAT='M' THEN 'KAWIN'
				WHEN A.MARITAL_STAT='S' THEN 'BELUM KAWIN'
				WHEN A.MARITAL_STAT='W' THEN 'CERAI'
			END AS STATUS,
			UPPER(TRIM(C.DESCRIPTION)) AS JENISPEKERJAAN,
			UPPER(TRIM(A.CUST_ADDR )) AS ALAMAT,
			NVL(TRIM(LTRIM(A.CUST_RT,0)),0) AS RT,
			NVL(TRIM(LTRIM(A.CUST_RW,0)),0) AS RW,
			UPPER(TRIM(SUBSTR(A.CUST_KEL,4))) AS KELURAHAN,
			UPPER(TRIM(SUBSTR(A.CUST_KEC,4))) AS KECAMATAN,

			CASE 
				WHEN SUBSTR(A.CUST_CITY,0,3) = 'Kab' THEN UPPER(TRIM(LTRIM(A.CUST_CITY,'Kab')))
				WHEN SUBSTR(A.CUST_CITY,0,4) = 'Kota' THEN 
					 CASE
						 WHEN SUBSTR(A.CUST_CITY,0,12) = 'Kota Jakarta' THEN UPPER(TRIM(REPLACE(A.CUST_CITY,'Kota','Kota Adm.')))
						 ELSE UPPER(TRIM(REPLACE(A.CUST_CITY,'Kota','Kota ')))
					 END
				ELSE UPPER(TRIM(A.CUST_CITY))
			END AS KOTAKABUPATEN,
			CASE
				WHEN SUBSTR(A.CUST_PROV,0,2) = 'DI' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(A.CUST_PROV,4))))
				WHEN SUBSTR(A.CUST_PROV,0,2) = 'Di' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(A.CUST_PROV,4))))
				WHEN SUBSTR(A.CUST_PROV,0,2) = 'di' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(A.CUST_PROV,4))))
				WHEN SUBSTR(A.CUST_PROV,0,2) = 'dI' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(A.CUST_PROV,4))))
				ELSE UPPER(TRIM(A.CUST_PROV))  
			END AS PROPINSI
			FROM OM_TRN_APPL_HDR A,
			OM_TRN_APPL_ADDR B,
			FS_MST_OCCUPATIONS C 
			WHERE A.APPL_NO=B.APPL_NO
			AND C.OCPT_CODE=A.OCPT_CODE
			AND A.APPL_NO='".$aplno."'
			AND B.IDENTITY_NO='".$nik."'
			";
		}else{
			$qry = "
			SELECT A.APPL_NO,A.SPOUSE_ID_NO NIK,A.SPOUSE_NAME NAMA,TO_CHAR(A.SPOUSE_BIRTHDAY ,'DD/MM/YYYY') AS TGLAHIR,
			UPPER(TRIM(A.SPOUSE_BIRTHPLACE )) AS TEMPATLAHIR,
			CASE
				WHEN A.CUST_SEX ='M' THEN 'Perempuan'
				WHEN A.CUST_SEX='F' THEN 'Laki-Laki'
			END AS JENISKELAMIN,
			'KAWIN' STATUS,
			NVL(UPPER(TRIM(C.DESCRIPTION)),'') AS JENISPEKERJAAN,
			UPPER(TRIM(A.SPOUSE_ADDR )) AS ALAMAT,
			NVL(TRIM(LTRIM(A.CUST_RT ,0)),0) AS RT,
			NVL(TRIM(LTRIM(A.CUST_RW,0)),0) AS RW,
			UPPER(TRIM(SUBSTR(E.KELURAHAN ,4))) AS KELURAHAN,
			UPPER(TRIM(SUBSTR(F.KECAMATAN ,4))) AS KECAMATAN,

			CASE 
				WHEN SUBSTR(D.CITY ,0,3) = 'Kab' THEN UPPER(TRIM(LTRIM(D.CITY,'Kab')))
				WHEN SUBSTR(D.CITY,0,4) = 'Kota' THEN 
					 CASE
						 WHEN SUBSTR(D.CITY,0,12) = 'Kota Jakarta' THEN UPPER(TRIM(REPLACE(D.CITY,'Kota','Kota Adm.')))
						 ELSE UPPER(TRIM(REPLACE(D.CITY,'Kota','Kota ')))
					 END
				ELSE UPPER(TRIM(D.CITY))
			END AS KOTAKABUPATEN,
			CASE
				WHEN SUBSTR(G.PROVINSI ,0,2) = 'DI' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(G.PROVINSI,4))))
				WHEN SUBSTR(G.PROVINSI,0,2) = 'Di' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(G.PROVINSI,4))))
				WHEN SUBSTR(G.PROVINSI,0,2) = 'di' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(G.PROVINSI,4))))
				WHEN SUBSTR(G.PROVINSI,0,2) = 'dI' THEN CONCAT('DAERAH ISTIMEWA ',UPPER(TRIM(SUBSTR(G.PROVINSI,4))))
				ELSE UPPER(TRIM(G.PROVINSI))  
			END AS PROPINSI
			FROM OM_TRN_APPL_HDR A
			LEFT JOIN FS_MST_OCCUPATIONS C ON C.OCPT_CODE=A.SPOUSE_OCPT,
			FS_MST_CITIES D,
			FS_MST_KELURAHAN E,
			FS_MST_KECAMATAN F,
			FS_MST_PROVINSI G
			WHERE D.CITY_CODE=A.SPOUSE_CITY
			AND E.KEL_CODE=A.SPOUSE_KEL
			AND F.KEC_CODE=A.SPOUSE_KEC
			AND G.PROV_CODE=A.SPOUSE_PROV
			AND A.APPL_NO='".$aplno."'
			AND A.SPOUSE_ID_NO='".$nik."'
			";
		}
		
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		
		return $row;
	}

}