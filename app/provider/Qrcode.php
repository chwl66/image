<?php


namespace app\provider;


class Qrcode
{

    public function __construct()
    {

    }

    public function writeString($data){
        $qrCode = new \Endroid\QrCode\QrCode($data);
        $qrCode->setSize(300);
        return $qrCode->writeString();
    }

    public function writeDataUri($data){
        $qrCode = new \Endroid\QrCode\QrCode($data);
        $qrCode->setSize(300);
        return $qrCode->writeDataUri();
    }
}