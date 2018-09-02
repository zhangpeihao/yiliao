<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/20
 * Time: 11:01
 */

namespace app\test\controller;

use think\Controller;

class Ser extends Controller
{
    public function get_data()
    {
        $header = array(
            'postman-token' => '73a4261a-0089-523c-c9bc-09eeddfdda1e',
            'cache-control' => 'no-cache',
            'cookie' => '_gscp=7342642',
            'accept-language' => 'zh-CN,zh;q=0.8,en-us;q=0.6,en;q=0.5;q=0.4',
            'accept-encoding' => 'gzip, deflate',
            'referer' => 'https://service.txslicai.com/html/my/trans-history.html',
            'userid' => 'YvUzAFTvAUXu8c7IJMRFyCeCZZEJDYiE3v9Ld9WspsQ=',
            'stalkerid' => 'did_2210c9e509f77c11bff86ee901ef0d354f2864f7',
            'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.691.400 QQBrowser/9.0.2524.400',
            'platform' => 'H5',
            'origin' => 'https://service.txslicai.com',
            'content-type' => 'application/json',
            'access-token' => '459F973992C3BB8ADF91EEB9147414A0CEC5263BFAAA950D6198D98BA5AC010EDED7E126311E681D40CD7D91C3BB5A07D1FC659AABB8B0E489F00D76D8652A074787086E5A42B28A4310558CEF8BB5CA900112BEF3EA6E2102B48E32880A4936B1D5A706FA429287F7FDD0F954431A7CBC832CBE837A1AD3195F780B191E2BB8C227DBF05757AFFD628F33CE745244C042755FABB1D1061A734C0270DD24D02247DB16D6E73E9329F7C3175F772C476D84D1090B73C9405339AC79E2806A14075174F7A1',
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'content-length' => '92',
            'connection' => 'keep-alive',
            'host' => 'javaapi.txslicai.com'
        );
        $post_data = json_decode('{"platform":1,"transType":0,"lastId":"1003313976165552128","pageSize":10,"lastTransDate":""}', true);
        $data = curl_data('https://javaapi.txslicai.com/account/transaction/list', $post_data, 'POST', $header);
        dump($data);
        dump(json_decode($data));
    }

    public function get_data2()
    {
        set_time_limit(0);
//            $post_data="{\"platform\":1,\"transType\":0,\"lastId\":\"1003313976165552128\",\"pageSize\":10,\"lastTransDate\":\"\"}";
//        {"code":200,"message":"成功","isinglobalmaintenance":false,"data":[
//          {"platform":{"name":"唐小僧","value":1},"typeList":[
//              {"name":"全部","value":0},
//              {"name":"转入","value":1},
//              {"name":"转出","value":2},
//              {"name":"投资","value":3},
//              {"name":"赎回","value":4}]},
        //{"platform":1,"transType":0,"lastId":0,"pageSize":10,"lastTransDate":""}
        $lastId=0;
        $writer = new \XLSXWriter();
        $title=['id','tranamount',	'created',	'status','afterbalance','direction','typetext',	'statustext','productname',	'memo','paymentbinding','type','detailtext','accountdetailtype','datasource','transDate','transferid'];
        $writer->writeSheetRow('Sheet1',$title);
            for ($i=1;$i<10;$i++){
                $post_data=json_encode([
                    'platform'=>1,
                    'transType'=>4,
                    'lastId'=>$lastId,
                    'pageSize'=>100,
                    'lastTransDate'=>''
                ]);
                $res=$this->curl_post($post_data);
                if (empty($res['data'])){
                    continue;
                }
                $lastId=end($res['data'])['id'];
                foreach ($res['data'] as $datum) {
                    $writer->writeSheetRow('Sheet1',[
                     strval("'".$datum[$title[0]]),
                     $datum[$title[1]],
                     $datum[$title[2]],
                     $datum[$title[3]],
                     $datum[$title[4]],
                     $datum[$title[6]],
                     $datum[$title[7]],
                     $datum[$title[8]],
                     $datum[$title[9]],
                     $datum[$title[10]],
                     $datum[$title[11]],
                     $datum[$title[12]],
                     $datum[$title[13]],
                     $datum[$title[14]],
                     $datum[$title[15]],
                     $datum[$title[16]],
                    ]);
                    ob_flush();
                    flush();
                }
            }
        $file_name='赎回.xlsx';
        $writer->writeToFile($file_name);

    }

    public function curl_post($post_data=[])
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://javaapi.txslicai.com/account/transaction/list",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, text/javascript, */*; q=0.01",
                "accept-encoding: gzip, deflate",
                "accept-language: zh-CN,zh;q=0.8,en-us;q=0.6,en;q=0.5;q=0.4",
                "access-token: 459F973992C3BB8ADF91EEB9147414A0CEC5263BFAAA950D6198D98BA5AC010EDED7E126311E681D40CD7D91C3BB5A07D1FC659AABB8B0E489F00D76D8652A074787086E5A42B28A4310558CEF8BB5CA900112BEF3EA6E2102B48E32880A4936B1D5A706FA429287F7FDD0F954431A7CBC832CBE837A1AD3195F780B191E2BB8C227DBF05757AFFD628F33CE745244C042755FABB1D1061A734C0270DD24D02247DB16D6E73E9329F7C3175F772C476D84D1090B73C9405339AC79E2806A14075174F7A1",
                "cache-control: no-cache",
                "connection: keep-alive",
//                "content-length: 92",
                "content-type: application/json",
                "cookie: _gscp=7342642",
                "host: javaapi.txslicai.com",
                "origin: https://service.txslicai.com",
                "platform: H5",
                "postman-token: c0595117-bb9a-fb4f-4d0b-d9027a2e1435",
                "referer: https://service.txslicai.com/html/my/trans-history.html",
                "stalkerid: did_2210c9e509f77c11bff86ee901ef0d354f2864f7",
                "user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.691.400 QQBrowser/9.0.2524.400",
                "userid: YvUzAFTvAUXu8c7IJMRFyCeCZZEJDYiE3v9Ld9WspsQ="
            ),
            CURLOPT_SSL_VERIFYPEER=>FALSE,
            CURLOPT_SSL_VERIFYHOST=>FALSE
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        /*if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }*/
        return json_decode($response,true);
    }
}