<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class UploadController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function upload(Request $request)
    {
        $re_param = $request->all();
        $data = $this->getAccessToken();
        $trimID       = explode('/',$data['id']);
        $accID        = trim(end($trimID));
        foreach ($re_param['file'] as $item) {
            $contenttype  = 'image/'.$item->extension();
            $name         = $item->hashName();
            $param = [
                'accId' => $accID,
                'contenttype' => $contenttype,
                'name' => $name,
            ];
            $body = base64_encode(file_get_contents($item->path()));
            $uri = $data['instance_url'] . '/services/apexrest/DocumentUploadBase64?';
            foreach ($param as $key => $value)
            {
                $uri .= $key.'='.$value.'&';
            }
            $request_param = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $data['access_token']
                ],
                'body' => $body
            ];
            $client = new Client();
            $response = $client->request('POST',$uri,$request_param);
            $data_res = json_decode((string)$response->getBody(), true);
        }
        $ContentDocumentId = $data_res['ContentID'];
        $img_url = $this->getUrlImage($data['instance_url'],$data['access_token'],$ContentDocumentId);
        return view('viewImg',['img_url' => $img_url]);


//        $this->pushImage('POST',$data['instance_url'] . '/services/apexrest/DocumentUploadBase64',$request_param);
    }

    public function view(Request $request)
    {
        $accessToken = '00D5g00000A4cc5!AQgAQJ_FC6cegs8Z49MU6ma3vQxr_n.US52lQUsIr4T7QQOIzpn9.PlB519w1lwqiIFPA1TSoBowYuLRxpgHCBNxL5QdpxWl';
        $url = $this->getUrlImage('https://vti7-dev-ed.my.salesforce.com',$accessToken,'0695g00000062Q6AAI');
        return view('viewImg',['img_url' => $url]);
    }
    public function getAccessToken()
    {
        $client = new Client(['base_uri' => env('LOGIN_URL')]);
        try {
            $response = $client->post('services/oauth2/token',[
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'password',
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_SECRET'),
                    'username' => 'hoang.nguyenngoc-test@force.com',
                    'password' => env('PASSWORD') . env('SECURITY_TOKEN'),
                ]
            ]);
            $data = json_decode($response->getBody(),true);
        }catch (\Exception $e){
            dd($e);
        }
        return $data;
    }

    public function pushImage($method,$instance_url,$request_param)
    {
        $client = new Client();
        $response = $client->request($method,$instance_url,$request_param);
        dd($response);
    }

    public function getUrlImage($instance_url,$accessToken,$ContentDocumentId)
    {
        try {
            $uri = $instance_url . '/services/data/v52.0/query/?q=SELECT+Id,ContentDocumentId,DistributionPublicUrl,ContentDownloadUrl+FROM+ContentDistribution+WHERE+ContentDocumentId+=+\''                   .$ContentDocumentId.'\'';
            $request_param = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
//            'q' => "SELECT+Id,ContentDocumentId,DistributionPublicUrl,ContentDownloadUrl+FROM+ContentDistribution+WHERE+ContentDocumentId+=+\'".$ContentDocumentId."\'"
            ];
            $client = new Client();
            $response = $client->request('GET',$uri,$request_param);
            $data_res = json_decode((string)$response->getBody(), true);
            $imgUrl = '';
            foreach ($data_res['records'] as $val){
                $imgUrl = $val['ContentDownloadUrl'];
            }
            return $imgUrl;
        }catch (\Exception $e){
            dd($e);
        }

    }
}
