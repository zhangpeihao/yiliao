<?php

// 公共助手函数

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name)
            return $name;
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }

}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $url = preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
            if (is_bool($domain)) {
                $public = \think\Config::get('view_replace_str.__PUBLIC__');
                $url = rtrim($public, '/') . $url;
                if (!preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
                    $url = request()->domain() . $url;
                }
            } else {
                $url = $domain . $url;
            }
        }
        return $url;
    }

}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }

}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst')) {

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }

}

if (!function_exists('var_export_short')) {

    /**
     * 返回打印数组结构
     * @param string $var 数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : var_export_short($key) . " => ")
                        . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

}

/**
 * 转换时间点，例如：11:40，转换成1140，03:10转换成0310
 * @author : yunhe <2846359640@qq.com>
 * @date: 2018/4/7 19:38
 */
function format_string_time($time){
    $arr=explode(':',$time);
    foreach ($arr as &$v){
        if (strlen($v)==1){
            $v='0'.$v;
        }
    }
    return (int)implode('',$arr);
}


/**
 * 将整型时间转成标准格式的时刻，例如：110，转换成01:10
 * @param $string
 * @author : yunhe <2846359640@qq.com>
 * @date: 2018/4/8 10:30
 */
function string_to_time($string){
    if (strlen($string)==3){
        $string='0'.$string;
    }
    $start_hour=substr($string,0,2);
    $start_min=substr($string,2,2);
    return $start_hour.':'.$start_min;
}


define('KC_FFMPEG_PATH', 'ffmpeg -i "%s" 2>&1');
function video_info($file) {
    ob_start();
    passthru(sprintf(KC_FFMPEG_PATH, $file));
    $info = ob_get_contents();
    ob_end_clean();
    // 通过使用输出缓冲，获取到ffmpeg所有输出的内容。
    $ret = array();
    // Duration: 01:24:12.73, start: 0.000000, bitrate: 456 kb/s
    if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
        $ret['duration'] = $match[1]; // 提取出播放时间
        $da = explode(':', $match[1]);
        $ret['seconds'] = $da[0] * 3600 + $da[1] * 60 + $da[2]; // 转换为秒
        $ret['start'] = $match[2]; // 开始时间
        $ret['bitrate'] = $match[3]; // bitrate 码率 单位 kb

    }
    // Stream #0.1: Video: rv40, yuv420p, 512x384, 355 kb/s, 12.05 fps, 12 tbr, 1k tbn, 12 tbc
    if (preg_match("/Video: (.*?), (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
        $ret['vcodec'] = $match[1]; // 编码格式
        $ret['vformat'] = $match[2]; // 视频格式
        $ret['resolution'] = $match[3]; // 分辨率
        //网上常见demo都是只有三个参数解析，不排除视频信息不一致，导致解析宽高出问题，有的是放在了第四个
        if (strstr($match[3],'x')){
            $a = explode('x', $match[3]);
        }else{
            $a = explode('x', $match[4]);
        }
        $ret['width'] = $a[0];
        $ret['height'] = $a[1];
    }
    // Stream #0.0: Audio: cook, 44100 Hz, stereo, s16, 96 kb/s
    if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
        $ret['acodec'] = $match[1]; // 音频编码
        $ret['asamplerate'] = $match[2]; // 音频采样频率

    }
    if (isset($ret['seconds']) && isset($ret['start'])) {
        $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间

    }
    $ret['size'] = filesize($file); // 文件大小
    return array($ret, $info);
}

/**
 * 创建视频封面
 */
function create_video_thumb($video_path,$sec=2){
//    $fmp=\FFMpeg\FFMpeg::create([
//        'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
//        'ffprobe.binaries' => '/usr/local/bin/ffprobe',
//        'timeout'          => 3600, // the timeout for the underlying process
//        'ffmpeg.threads'   => 12,   // the number of threads that FFMpeg should use
//    ]);
    $new_video_path='.'.$video_path;
    $temp_video_path=explode('.',$new_video_path);
    $ext=end($temp_video_path);
    $basename=basename($video_path);
    $save_dir='.'.strtr($video_path,[$basename=>'thumb/']);
    if (!file_exists($save_dir)){
        mkdir($save_dir,0777);
    }
    $thumb_path='.'.strtr($video_path,[$basename=>'thumb/'.$basename]);
    $thumb_path=strtr($thumb_path,[$ext=>'jpg']);
    $save_video_path=strtr($new_video_path,['.'.$ext=>'.mp4']);
//    生成缩略图
    $video_thumb_size=config('video_thumb_size');
//    if (!strstr($video_thumb_size,'x')){$video_thumb_size="300x240";}
//    $command2="ffmpeg -y -i %s -y -f image2 -ss 10 -t 0.001 -s %s %s  2>&1";
//    $command2="ffmpeg -y -i %s -y -f image2 -ss 00:00:05  %s  2>&1";
    $command2="ffmpeg -ss 00:00:02 -i %s %s -r 1 -an -f mjpeg 2>&1";
    $img_cmd=sprintf($command2,$new_video_path,$thumb_path);
    exec($img_cmd,$out_info);
//dump($img_cmd);
//dump($out_info);
//    $video_obj=$fmp->open($new_video_path);
//    $video_obj->filters()->resize(new \FFMpeg\Coordinate\Dimension(320,240))->synchronize();
    //视频封面地址
//    $video_obj->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($sec))->save($thumb_path);
//    $video_obj->save(new \FFMpeg\Format\Video\X264(),$save_video_path);
//    dump($img_cmd);
//dump($out_info);
    if ($thumb_path){
        return config('img_domain').ltrim($thumb_path,'.');
    }else{
        return false;
    }
}



/**
 * 友好的时间显示
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 *
 */
function friendly_date($sTime, $type = 'normal', $alt = 'false'){
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
//            return date("m月d日 H:i",$sTime);
            return date("m-d H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}


//格式化友好显示时间
function formatTime($time){
    if (empty($time)){
        return "";
    }
    $now=time();
    $day=date('Y-m-d',$time);
    $today=date('Y-m-d');

    $dayArr=explode('-',$day);
    $todayArr=explode('-',$today);

    //距离的天数，这种方法超过30天则不一定准确，但是30天内是准确的，因为一个月可能是30天也可能是31天
    $days=($todayArr[0]-$dayArr[0])*365+(($todayArr[1]-$dayArr[1])*30)+($todayArr[2]-$dayArr[2]);
    //距离的秒数
    $secs=$now-$time;

    if($todayArr[0]-$dayArr[0]>0 && $days>3){//跨年且超过3天
        return date('Y-m-d',$time);
    }else{

        if($days<1){//今天
            if($secs<60)return $secs.'秒前';
            elseif($secs<3600)return floor($secs/60)."分钟前";
            else return floor($secs/3600)."小时前";
        }else if($days<2){//昨天
            $hour=date('h',$time);
            return "昨天".$hour.'点';
        }elseif($days<3){//前天
            $hour=date('h',$time);
            return "前天".$hour.'点';
        }else{//三天前
            return date('m月d号',$time);
        }
    }
}


if (!function_exists('parse_attr')) {
    /**
     * 解析配置
     * @param string $value 配置值
     * @return array|string
     */
    function parse_attr($value = '') {
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if (strpos($value, ':')) {
            $value  = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k]   = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}
/**
 * 获取数字字符串
 * @param $num
 * @param int $length
 */
function getNumString($num,$length=5){
    $max=pow(10,$length-1);
    if ($num>=$max){
        return $num;
    }
    for($i=1;$i<$length;$i++){
        if ($num>=pow(10,$i-1) && $num<pow(10,$i)){
            return str_repeat('0',$length-$i).$num;
        }
    }
}
function getordersn($uid,$opertype=2){
    $data['uid']=$uid;
//    $map['ctime']=array(array('egt',strtotime(date('Y-m-d 00:00:00',time())),array('elt',strtotime(date('Y-m-d 23:59:59',time())))));
    $map['ctime']=[
        'between',[strtotime(date('Y-m-d 00:00:00',time())),strtotime(date('Y-m-d 23:59:59',time()))]
    ];
    $count=db('mall_product_order')->where($map)->count();
    $count=$count+1;
    $count=getNumString($count,5);
    $ordersn="";
    switch($opertype){
        case 1:$ordersn='CZ'.'_'.date('YmdHis',time()).$data['uid'].$count;break;//充值
        case 2:$ordersn='DD'.'_'.date('YmdHis',time()).$data['uid'].$count;break;//订单
        case 3:$ordersn='TK'.'_'.date('YmdHis',time()).$data['uid'].$count;break;//退款
        case 4:$ordersn='SK'."_".date('YmdHis',time()).$data['uid'].$count;break;//收款
        case 5:$ordersn='ZZ'."_".date('YmdHis',time()).$data['uid'].$count;break;//课桌
        case 6:$ordersn='GF'."_".date('YmdHis',time()).$data['uid'].$count;break;//gift礼物
        case 7:$ordersn='TX'."".date('YmdHis',time()).$data['uid'].$count;break;//用户提现
        case 8:$ordersn='HF'."_".date('YmdHis',time()).$data['uid'].$count;break;//余额用户充值话费
        case 9:$ordersn='AD'."_".date('YmdHis',time()).$data['uid'].$count;break;//统一支付订单号
    }
    return $ordersn;
}


function getPaysn($time=''){
    if (!$time){
        $time=time();
    }
    $map['ctime']=array(array('egt',strtotime(date('Y-m-d 00:00:00',$time)),array('elt',strtotime(date('Y-m-d 23:59:59',$time)))));
    $count=db('mall_pay')->where($map)->count();
    if(empty($count)){$count=0;}
    $count=$count+1;
    if($count>=0 && $count<10){
        $count='000'.$count;
    }if($count>=10&& $count<100){
        $count='00'.$count;
    }if($count>=100&& $count<1000){
        $count='0'.$count;
    }

    return  "PY_".date('YmdHis',$time).$count;
}


function getapi($url,$data=[],$post=0,$header=[]){
    if (!strstr($url,'http://') && !strstr($url,'https://')){
//        $url='http://'.$_SERVER['HTTP_HOST'].'/index.php/'.$url;
        $url=request()->domain().'/'.$url;
    }
    if ($post==0){
        if ($data){
            $url=$url.'?'.http_build_query($data);
        }
        $res=curl_data($url);
    }else{
        $res=curl_data($url,$data,'POST',$header);
    }
//    dump($url);exit();
    return json_decode($res,true);
}

function  curl_data($url,$data=array(),$method="GET",$header=[],$setcooke=false,$cookie_file=''){
    $ch = curl_init();     //1.初始化
    curl_setopt($ch, CURLOPT_URL, $url); //2.请求地址
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);//3.请求方式
    //4.参数如下    禁止服务器端的验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    //伪装请求来源，绕过防盗
    //curl_setopt($ch,CURLOPT_REFERER,"http://wthrcdn.etouch.cn/");
    //配置curl解压缩方式（默认的压缩方式）
    if (empty($header)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));
    }else{
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");

    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0'); //指明以哪种方式进行访问
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    if($method=="POST"){//5.post方式的时候添加数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if($setcooke==true){
        //如果设置要请求的cookie，那么把cookie值保存在指定的文件中
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    }else{
        //就从文件中读取cookie的信息
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($ch);

    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
}

/**
 * 字符截取 只支持UTF-8
 * @param $string
 * @param $length
 * @param $dot
 * @return string     字符串
 */
if (!function_exists('string_cut')) {
    function string_cut($string, $length, $dot = '...')
    {
        $len = mb_strlen($string);
        $string = htmlspecialchars_decode($string); // 将特殊的 HTML 实体转换回普通字符后再截取
        $string = mb_substr($string, 0, $length, 'utf-8');
        $string = htmlspecialchars($string);    // 把一些预定义的字符转换为 HTML 实体
        if ($len <= $length) {
            $dot = '';
        }
        return $string . $dot;
    }
}

function GetIpLookup($ip = ''){
    $res = @file_get_contents('https://api.map.baidu.com/location/ip?ip='.$ip.'&ak=ECb2d35333c8b8918490991c138778cb&coor=bd09ll');
    if(empty($res)){ return false; }
    return json_decode($res,true);
}
