<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/31
 * Time: 10:14
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 订单物流信息查询
 * Class MallWuliu
 * @package app\api\controller
 */
class MallWuliu extends Api
{
    protected $noNeedRight='*';
    protected $noNeedLogin='*';
    //快递鸟及时查询api
    private $ReqURL='http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';

    private $EBusinessID='1315573';

    private $AppKey='0cb270c7-9cb3-4135-8613-f05792172c93';
//http://api.kuaidi100.com/api?id=[]&com=[]&nu=[]&valicode=[]&show=[0|1|2|3]&muti=[0|1]&order=[desc|asc]
//    private $kuaidi100='http://api.kuaidi100.com/api?id=5d0f73bd3f647a63&com=shentong&nu=3364845147143&show=0&muti=1&order=desc';
    private $kuaidi100='http://api.kuaidi100.com/api?id=5d0f73bd3f647a63&com=%s&nu=%s&show=0&muti=1&order=desc';

    /**
     * 快递单号查询物流信息
     * @ApiMethod   (GET)
     * @ApiParams   (name="post_num", type="string", required=true, description="快递单号")
     * @ApiParams   (name="post_code", type="string", required=false, description="快递公司编号")
     * @ApiReturnParams   (name="AcceptTime", type="string", required=true, description="时间")
     * @ApiReturnParams   (name="AcceptStation", type="string", required=true, description="描述")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1527734904','data':[{'AcceptStation':'北京市【北京顺义区一分部】，【华红卫浴\/01052860136】已揽收','AcceptTime':'2018-05-27 15:49:31'},{'AcceptStation':'到北京市【北京京顺转运中心】','AcceptTime':'2018-05-27 19:52:41'},{'AcceptStation':'北京市【北京京顺转运中心】，正发往【天通苑】','AcceptTime':'2018-05-27 22:36:35'},{'AcceptStation':'到北京市【天通苑】','AcceptTime':'2018-05-27 22:38:48'},{'AcceptStation':'北京市【天通苑】，【庞艳军\/18001175944】正在派件','AcceptTime':'2018-05-28 07:55:19'},{'AcceptStation':'北京市【天通苑】，家里 已签收','AcceptTime':'2018-05-28 17:37:38'}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/31 10:44
     */
    public function check_wuliu()
    {
        $post_num=$this->request->get('post_num','71220046842324','strval');
        $post_code=$this->request->get('post_code','','strval');
        $res=file_get_contents(sprintf($this->kuaidi100,$post_code,$post_num));
        $res=json_decode($res,true);
        if ($res['status']==1){
            db('mall_wuliu')->where('post_num',$post_num)->update(['wuliu_data'=>json_encode($res)]);
            $this->success('查询成功',$res['data']);
        }else{
            $res=db('mall_wuliu')->where('post_num',$post_num)->value('wuliu_data');
            $res=json_decode($res,true);
            $this->success('查询成功',$res['data']);
//            $this->error($res['message']);
        }
        exit();
        if (empty($post_num)){$this->error('请指定快递单号');}
        if (empty($post_code)){
            $check=$this->getOrderInfo($post_num);
            $check=json_decode($check,true);
            $order_type=$check['Shippers'][0];
            $post_code=$order_type['ShipperCode'];
        }
        $data=$this->getOrderTracesByJson($post_code,$post_num);
        dump($data);exit();
        $data=json_decode($data,true);
        $return_data=[];
        if (!empty($data['Traces'])){
            $return_data=$data['Traces'];
        }
        $this->success('查询成功',$return_data);
    }



    /**
     * Json方式 单号识别
     */
    private function getOrderInfo($LogisticCode=""){
//        $requestData= "{'LogisticCode':'1000745320654'}";
        $requestData=json_encode([
            'LogisticCode'=>$LogisticCode
        ]);
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '2002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
        $result=$this->sendPost('http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx', $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }

    /**
     * Json方式 查询订单物流轨迹
     */
    private function getOrderTracesByJson($ShipperCode='',$LogisticCode=""){
//        $requestData= "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}";
        $requestData=json_encode([
           'OrderCode'=>'',
           'ShipperCode'=>$ShipperCode,
           'LogisticCode'=>$LogisticCode
        ]);
        dump($requestData);exit();
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] =$this->encrypt($requestData, $this->AppKey);
        $result=$this->sendPost($this->ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    private function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

}