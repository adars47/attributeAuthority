<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubmitController
{
    public function publicKey(Request $request)
    {
        $response = new Response();
        $file = file_get_contents(base_path() . "/publickey.crt");
        if ($file === null) {
            $response->setStatusCode(500);
        }
        $response->setStatusCode(200);
        $response->setContent($file);
        return $response;
    }

    public function sign(Request $request)
    {
        $message = [
            "attributes" => [
                "IsDoctor",
                "IsMedicalStaff"
            ],
            "issuedTo" => "0x7D378c0c7D5E046Fc1e9b95d5d4411FC4E6424f4",
            "validUntil"=> "2024-10-18"
        ];
        $stringMessage = json_encode($message);
        $signature = "";
        $key = file_get_contents(base_path() . "/sharingService.pem");
        $success = openssl_sign($stringMessage,$signature,$key,OPENSSL_ALGO_SHA256);
        $response = new Response();
        if($success === false)
        {
            $response->setStatusCode(500);
        }
        $response->setStatusCode(200);
        $response->setContent([
            "payload" => $stringMessage,
            "signature" => base64_encode($signature)
        ]);
        return $response;
    }

}
