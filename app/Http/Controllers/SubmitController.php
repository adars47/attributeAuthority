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

    /**
     *
     * Request body:
     * {
     *      "email":"adars.nepal@gmail.com",
     *      "password":"password"
     * }
     * Response:
     * 200
     * {}
     *
     */

    //create a endpoint to login
    // harcoded way to check credentials (password)
    //credentials do not matter or hardcode it (
    //create dummy db (text/json)

    /**
     * {
     *     "email": "loggedineamil@gmail.com",
     *      "attribute": [
     *          "isDoctor",
     *          "isOncologyDepartment"
     *      ],
     *      "uid":"asasasasasas"
     * },
     *  {
     *      "email": "loggedineamil2@gmail.com",
     *       "attribute": [
     *           "isDoctor",
     *           "isOncologyDepartment"
     *       ],
     *       "uid":"asasasasasas"
     *  }
     */

    public function login(Request $request)
    {
        $email = $request->input('email');
        $response = new Response();
        $file = file_get_contents(base_path() . "/db.json");
        $file = json_decode($file, true);
        if ($file === null) {
            $response->setStatusCode(400);
            $response->setContent("user not found");
        }

        foreach ($file['users'] as $value) {
            if ($value['email'] === $email) {
                $key = $this->generateKeys($value['uid'],$value['attribute']);
                $content = json_encode($key);
                return response()->streamDownload(function () use ($content){
                    echo $content;
                },$_SERVER['HTTP_HOST']."key");
            }
        }
        $response->setStatusCode(400);
        $response->setContent("Invalid credentials");
        return $response;
    }

    /**
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function sign(Request $request)
    {
        $fileName=time().".key";
        $key = $this->generateKeys("aasasasasasasas",[
            'isDoctor',
            'isOncologist'
        ]);
        $content = json_encode($key);
        return response()->streamDownload(function () use ($content){
            echo $content;
        },$fileName);
    }


    private function generateKeys($user_id,$attributes)
    {
        $message = [
            "attributes" => $attributes,
            "issuedTo" => $user_id,
            "validUntil"=> "2024-01-11" //system wide variable
        ];
        $stringMessage = json_encode($message);
        $signature = "";
        $key = file_get_contents(base_path() . "/sharingService.pem");
        $success = openssl_sign($stringMessage,$signature,$key,OPENSSL_ALGO_SHA256);
        $response = new Response();
        if($success === false)
        {
            $response->setStatusCode(500);
            return $response;
        }
        return [
            "payload" => $stringMessage,
            "signature" => base64_encode($signature)
        ];
    }

}
