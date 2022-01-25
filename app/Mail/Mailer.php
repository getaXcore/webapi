<?php
/**
 * Created by PhpStorm.
 * User: Taufan
 * Date: 01/04/2019
 * Time: 14:56
 */

namespace App\Mail;



use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class Mailer extends \Illuminate\Mail\Mailer
{
    use Queueable, SerializesModels;

    protected $fullName;
    protected $gender;
    protected $perumahan;
    protected $fullAddress;
    protected $maritalStatus;
    protected $occupation;
    protected $phoneNumber;
    protected $typeOfRenovation;

    public function __construct($fullName,$gender,$perumahan,$fullAddress,$maritalStatus,$occupation,$phoneNumber,$typeOfRenovation)
    {
        $this->fullName = $fullName;
        $this->gender = $gender;
        $this->perumahan = $perumahan;
        $this->fullAddress = $fullAddress;
        $this->maritalStatus = $maritalStatus;
        $this->occupation = $occupation;
        $this->phoneNumber = $phoneNumber;
        $this->typeOfRenovation = $typeOfRenovation;
    }
    public function build(){
        return $this->from('jtrustolympindo@gmail.com')
            ->view('email')
            ->with(
                [
                    'fname' =>  $this->fullName,
                    'gender' => $this->gender,
                    'perumahan' => $this->perumahan,
                    'faddress' => $this->fullAddress,
                    'mstatus' => $this->maritalStatus,
                    'occupate' => $this->occupation,
                    'mPhone' => $this->phoneNumber,
                    'typeofR' => $this->typeOfRenovation
                ]
            );
    }

}