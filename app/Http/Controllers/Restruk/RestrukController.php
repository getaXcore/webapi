<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 23/04/2020
 * Time: 14:23
 */

namespace App\Http\Controllers\Restruk;


use App\Http\Controllers\Controller;
use App\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RestrukController extends Controller
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
	public function regRestruk(Request $request){
		
		$nama = $request->input('nama');
		$cabang = $request->input('cabang');
		$alamat = $request->input('alamat');
		$nmibukdg = $request->input('nmibukdg');
		$nktr = $request->input('nktr');
		$noktp = $request->input('noktp');
		$mphone = $request->input('mphone');
		$msg = $request->input('message');

		$fotoKtp = $request->file('fktp');
		$sizeFotoKtp = $fotoKtp->getClientSize(); //byte
		$typeFotoKtp = $fotoKtp->getClientMimeType(); //image/png, image/jpeg, application/pdf

		//print_r($typeFotoKtp);

		$fotoStnkBagDepan = $request->file('fstnkd');
		$sizefotoStnkBagDepan = $fotoStnkBagDepan->getClientSize(); //byte
		$typefotoStnkBagDepan = $fotoStnkBagDepan->getClientMimeType();

		$fotoStnkBagBelakang = $request->file('fstnkb');
		$sizefotoStnkBagBelakang = $fotoStnkBagBelakang->getClientSize(); //byte
		$typefotoStnkBagBelakang = $fotoStnkBagBelakang->getClientMimeType();

		$fotoSelfieWithKtpWform = $request->file('fselfiewktpwform');
		$sizefotoSelfieWithKtpWform = $fotoSelfieWithKtpWform->getClientSize(); //byte
		$typefotoSelfieWithKtpWform = $fotoSelfieWithKtpWform->getClientMimeType();

		$fotoSelfieWithCar = $request->file('fselfiewcar');
		$sizefotoSelfieWithCar = $fotoSelfieWithCar->getClientSize(); //byte
		$typefotoSelfieWithCar = $fotoSelfieWithCar->getClientMimeType();

		$suratKeterangan = $request->file('fket');
		$sizesuratKeterangan = $suratKeterangan->getClientSize(); //byte
		$typesuratKeterangan =  $suratKeterangan->getClientMimeType();

		$formulir = $request->file('fform');
		$sizeformulir = $formulir->getClientSize(); //byte
		$typeformulir = $formulir->getClientMimeType();
		
		$typeArray = array(1 => "image/png", 2 => "image/jpeg", 3 => "application/pdf",4 => "application/vnd.openxmlformats-officedocument.wordprocessingml.document", 5 => "application/msword");
		$keyFotoKtp = array_search($typeFotoKtp,$typeArray); 
		//echo $keyFotoKtp;
		$keyfotoStnkBagDepan = array_search($typefotoStnkBagDepan,$typeArray);
		$keyfotoStnkBagBelakang = array_search($typefotoStnkBagBelakang,$typeArray);
		$keyfotoSelfieWithKtpWform = array_search($typefotoSelfieWithKtpWform,$typeArray);
		$keyfotoSelfieWithCar = array_search($typefotoSelfieWithCar,$typeArray);
		$keysuratKeterangan = array_search($typesuratKeterangan,$typeArray);
		$keyformulir = array_search($typeformulir,$typeArray);

		if($sizeFotoKtp > 200000 || $sizefotoStnkBagDepan > 200000 || $sizefotoStnkBagBelakang > 200000 || $sizefotoSelfieWithKtpWform > 200000 || $sizefotoSelfieWithCar > 200000 || $sizesuratKeterangan > 200000 || $sizeformulir > 200000){ //jika lebih dari 200kb
			return response($this->codeSize,200);
		}elseif($keyFotoKtp == false || $keyfotoStnkBagDepan == false  || $keyfotoStnkBagDepan == false  || $keyfotoStnkBagBelakang == false  || $keyfotoSelfieWithKtpWform == false  || $keyfotoSelfieWithCar == false  || $keysuratKeterangan == false  || $keyformulir == false){ // jika bukan image/png, image/jpeg, application/pdf
			return response($this->codeType,200);
		}else{
			//nama path
			$pathName = str_replace(" ","",trim($nama));

			//foler utama
			$dir = public_path()."/media/".$pathName."/";
			
			//create folder
			if (!file_exists($dir)) {
				mkdir($dir,0777,true);
			}
			
			//create file index
			$content = "";
			$fp = fopen($dir. "/index.php","c");
			fwrite($fp,$content);
			fclose($fp);

			//if ($request->hasFile('fktp')) {
				//$fotoKtp->move($dir, $fotoKtp->getClientOriginalName());
			//}
			
			//memindahkan file ke folder masing2 debitur
			$fotoKtp->move($dir, $fotoKtp->getClientOriginalName());
			$fotoStnkBagDepan->move($dir, $fotoStnkBagDepan->getClientOriginalName());
			$fotoStnkBagBelakang->move($dir, $fotoStnkBagBelakang->getClientOriginalName());
			$fotoSelfieWithKtpWform->move($dir, $fotoSelfieWithKtpWform->getClientOriginalName());
			$fotoSelfieWithCar->move($dir, $fotoSelfieWithCar->getClientOriginalName());
			$suratKeterangan->move($dir, $suratKeterangan->getClientOriginalName());
			$formulir->move($dir, $formulir->getClientOriginalName());
			
			$arrData = array(
				"nama" => $nama,
				"cabang" => $cabang,
				"alamat" =>$alamat,
				"nmibukdg" =>$nmibukdg,
				"nktr" =>$nktr,
				"noktp" =>$noktp,
				"mphone" =>$mphone,
				"msg" =>$msg
			);

			//send email
			Mail::alwaysFrom('financejto@gmail.com','JTO Finance');
			Mail::send('emailrestruk',$arrData,function ($message) use($nama,$fotoKtp,$fotoStnkBagDepan,$fotoStnkBagBelakang,$fotoSelfieWithKtpWform,$fotoSelfieWithCar,$suratKeterangan,$formulir,$dir){
				$message->to('getaufan@gmail.com')
					/*->to('edward.saragih@jto.co.id')
					->to('edward.saragih@gmail.com')
					->to('taufan.septaufani@jto.co.id')*/
				->subject('Pengajuan Restrukturisasi a/n '.$nama);
				/*->attach($dir,array(
                    'as' => $fotoKtp->getClientOriginalName(),
                    'mime' => $fotoKtp->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $fotoStnkBagDepan->getClientOriginalName(),
                    'mime' => $fotoStnkBagDepan->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $fotoStnkBagBelakang->getClientOriginalName(),
                    'mime' => $fotoStnkBagBelakang->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $fotoSelfieWithKtpWform->getClientOriginalName(),
                    'mime' => $fotoSelfieWithKtpWform->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $fotoSelfieWithCar->getClientOriginalName(),
                    'mime' => $fotoSelfieWithCar->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $suratKeterangan->getClientOriginalName(),
                    'mime' => $suratKeterangan->getClientMimeType()
						)
					)
				->attach($dir,array(
                    'as' => $formulir->getClientOriginalName(),
                    'mime' => $formulir->getClientMimeType()
						)
					);*/
				$message->embed($dir.$fotoKtp->getClientOriginalName());
				$message->embed($dir.$fotoStnkBagDepan->getClientOriginalName());
				$message->embed($dir.$fotoStnkBagBelakang->getClientOriginalName());
				$message->embed($dir.$fotoSelfieWithKtpWform->getClientOriginalName());
				$message->embed($dir.$fotoSelfieWithCar->getClientOriginalName());
				$message->embed($dir.$suratKeterangan->getClientOriginalName());
				$message->embed($dir.$formulir->getClientOriginalName());
			});

			return response($this->codeOK,200);

		}

		
		
		
		
		
	}
}