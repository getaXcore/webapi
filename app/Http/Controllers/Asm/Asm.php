<?php


namespace App\Http\Controllers\Asm;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Asm extends Controller
{
    public function __construct()
    {
        $this->OK = array("code"=>1,"description"=>"success");
        $this->notOK = array("code"=>0,"description"=>"no data");
        $this->user_id ="SERVICEJTO";
        $this->password = "JTO%^&";
        $this->auth = "Basic U0VSVklDRUpUTzpKVE8lXiY=";
        $this->urls = "https://pegadev.sinarmas.co.id/prweb/PRRestService/ASMFWSFAGISFWWork/Data-Portal/ASMRequestServiceAutoAccept";
		$this->ociUser = "TAUFAN";
		$this->ociPass = "mastaufanjto6";
		$this->ociHost = "//192.168.205.100:1521/jtofast";
		$this->connect = oci_connect($this->ociUser,$this->ociPass,$this->ociHost);
		
    }

    public function index(){
        return view("pageasm");
    }
	
	public function check(Request $request){
		$param = json_decode($request->getContent(),true);
		$contractNo = trim($param["contractNo"]);
		$userid = trim($param["uid"]);
		$password = trim($param["pwd"]);
		
		/*$isAuth = $this->checkUPass($userid);
		
		if(!empty($isAuth)){
			
			print_r($isAuth);
			
		}*/
		$conn = $this->connect;
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$qry = "
			SELECT COUNT(*) X
			FROM AR_TRN_SUM_CONTRACTS A,
			OM_TRN_APPL_HDR B,
			OM_TRN_APPL_OBJECT C,
			INSC_MST_TYPE D
			WHERE A.CONTRACT_NO='".$contractNo."'
			AND A.CONTRACT_NO=B.CONTRACT_NO
			AND B.APPL_NO=C.APPL_NO
			AND C.INSC_TYPE_ID=D.TYPE_ID
		";
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		
		if(!empty($row)){
			$data = $row["X"];
			
			$qryx = "
				SELECT TO_CHAR(A.CONTRACT_DATE,'YYYYMMDD')||'T050000.000 GMT' StartDateTime,TO_CHAR(A.MATURITY_DATE,'YYYYMMDD')||'T050000.000 GMT' EndDateTime,'PT JTO QQ '|| B.CUST_NAME QQName,
				CASE
					WHEN B.CUST_TYPE='I' THEN '1'
					WHEN B.CUST_TYPE='C' THEN '2'
				END CustomerType,
				B.CUST_NAME TheInsured,
				A.CONTRACT_NO RefNo,
				'JTO_'||A.CONTRACT_NO||'_'||TO_CHAR(SYSDATE,'YYMMDDHHMISS') IdTransaction,
				'1' StatusPenerbitan,
				D.DESCRIPTION TypeOfPacket,
				B.CUST_NAME pyFirstName,
				B.BIRTH_PLACE pyCity,
				TO_CHAR(B.BIRTH_DATE,'YYYYMMDD') ASMDateOfBirth,
				CASE
					WHEN B.CUST_SEX = 'M' THEN '1'
					ELSE '2'
				END ASMGender,
				CASE
					WHEN B.CUST_TYPE='I' THEN (SELECT X.IDENTITY_NO FROM OM_TRN_APPL_ADDR X WHERE X.APPL_NO=A.APPL_NO )
					WHEN B.CUST_TYPE='C' THEN B.NPWP_NO
				END ASMIDCard_ASMNPWP,
				B.CUST_ADDR ||' RT '||B.CUST_RT ||' RW '||B.CUST_RW||' '||B.CUST_KEL||' '||B.CUST_KEC||' '||B.CUST_CITY||' '||B.CUST_PROV ASMAddress,
				CASE
					WHEN B.CUST_TYPE='I' THEN '1'
					WHEN B.CUST_TYPE='C' THEN '2'
				END ASMAddressType,
				NVL(B.CUST_FIXPH_AREA||B.CUST_FIXPHONE,B.CUST_MOBPHONE) TelfaxNumber,
				CASE
					WHEN B.CUST_TYPE='I' THEN '3'
					WHEN B.CUST_TYPE='C' THEN '1'
				END TelfaxType,
				C.OBJ_BRAND BrandName,
				C.NOKA ChassisNumber,
				C.NOSIN EngineNumber,
				C.WARNA ColorName,
				NVL(C.NO_POLISI,C.CERT_NO) LicensePlate,
				C.OBJ_TAHUN ManufactureYear,
				C.OBJ_PRICE TSI,
				C.OBJ_TYPE TypeName,
				CASE
					WHEN C.PENGGUNAAN = 'PERSONAL' THEN 'PRIBADI'
					ELSE C.PENGGUNAAN
				END OccupationName,
				C.TYPE_ID CoverageNote,
				(SELECT COUNT(Y.TAHUN_KE) FROM INSC_TRN_APPL_PREMI Y WHERE Y.APPL_NO=A.APPL_NO ) CoverageYear
				FROM AR_TRN_SUM_CONTRACTS A,
				OM_TRN_APPL_HDR B,
				OM_TRN_APPL_OBJECT C,
				INSC_MST_TYPE D
				WHERE A.CONTRACT_NO='".$contractNo."'
				AND A.CONTRACT_NO=B.CONTRACT_NO
				AND B.APPL_NO=C.APPL_NO
				AND C.INSC_TYPE_ID=D.TYPE_ID
			";
			
			$stidx = oci_parse($conn, $qryx);
			oci_execute($stidx);
			
			$rows = oci_fetch_array($stidx, OCI_ASSOC+OCI_RETURN_NULLS);
			
			//print_r($rows);
				
			$bodyJsonPolicy = array(
				"CustomerType" => $rows["CUSTOMERTYPE"],
				"EndDateTime" => $rows["ENDDATETIME"],
				"IdTransaction" => $rows["IDTRANSACTION"],
				"QQName" => $rows["QQNAME"],
				"RefNo" => $rows["REFNO"],
				"StartDateTime" => $rows["STARTDATETIME"],
				"StatusPenerbitan" => $rows["STATUSPENERBITAN"],
				"TheInsured" => $rows["THEINSURED"],
				"TypeOfPacket" => $rows["TYPEOFPACKET"]
			);
			
			if($rows["CUSTOMERTYPE"] == 1){
				$bodyJsonCustH = "Customer_P";
				$bodyJsonCustB = array(
					"ASMDateOfBirth" => $rows["ASMDATEOFBIRTH"],
					"ASMGender" => $rows["ASMGENDER"],
					"ASMIDCard" => $rows["ASMIDCARD_ASMNPWP"],
					"pyCity" => $rows["PYCITY"],
					"pyFirstName" => $rows["PYFIRSTNAME"]
				);
			}else{
				$bodyJsonCustH = "Customer_C";
				$bodyJsonCustB = array(
					"pyCompany" => $rows["PYFIRSTNAME"],
					"ASMNPWP" => $rows["ASMIDCARD_ASMNPWP"]
				);
			}
			
			$bodyJsonTlpFx[] = array(
				"TelfaxNumber" => $rows["TELFAXNUMBER"],
				"TelfaxType" => $rows["TELFAXTYPE"]
			);
			
			$bodyJsonAdrs[] = array(
				"ASMAddress" => $rows["ASMADDRESS"],
				"ASMAddressType" => $rows["ASMADDRESSTYPE"],
				"ASMTelfax" => $bodyJsonTlpFx
			);
			
			for($i=1;$i<=$rows["COVERAGEYEAR"];$i++){
				$arrCovList[] = array(
					"CoverageNote" => "TLO",
					"Year" => $i
				);
			}
			
			
			$bodyJsonVhcl[] = array(
				"BrandName" => $rows["BRANDNAME"],
				"ChassisNumber" => $rows["CHASSISNUMBER"],
				"EngineNumber" => $rows["ENGINENUMBER"],
				"LicensePlate" => $rows["LICENSEPLATE"],
				"ColorName" => $rows["COLORNAME"],
				"ManufactureYear" => $rows["MANUFACTUREYEAR"],
				"TSI" => $rows["TSI"],
				"TypeName" => $rows["TYPENAME"],
				"CoverageList" => $arrCovList,
				"Occupation" => array(
					"OccupationName" => $rows["OCCUPATIONNAME"]
				)
			);
			
			$bodyJsonNB = array(
				"NBWorkPage" => array(
					"Quotation" => array(
						"AccessCode" => "100000000829",
						"BusinessCode" => "10138",
						"BusinessName" => "MBU",
						"GroupPanel" => "007"
					),
					"Policy" => $bodyJsonPolicy,
					$bodyJsonCustH => $bodyJsonCustB,
					"AddressList" => $bodyJsonAdrs,
					"VehicleList" => $bodyJsonVhcl
				)
			);
			
			$bodyJson = $bodyJsonNB;
			
			return response($bodyJson,200);
			
			//return response(array_merge($this->OK,array("data"=>$data)),200);
		}else{
			return response($this->notOK,200);
		}
		
		
	}
	
	public function gmd5(){
        print_r(base64_encode($this->user_id.":".$this->password));
    }
	
	public function checkUPass($userid){
		$conn = $this->connect;
		
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$qry = "
			SELECT enkripsi.decak(A.USER_PWD) pass FROM FIFAPPS.FS_SEC_USERS A
			WHERE A.USER_ID='".$userid."'
		";
		
		$stid = oci_parse($conn, $qry);
		oci_execute($stid);
		
		$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		
		return $row;
	}
	
}