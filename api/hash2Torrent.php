<?php
$infohash = isset($_GET['infohash']) ? $_GET['infohash'] : "";
if($infohash == ""){
    echo "no infohash";
    die();
}
$url = "http://itorrents.org/torrent/" . $infohash . ".torrent";
// $url = "http://oswz19065.bkt.clouddn.com/E629AAC0672E1E26332D85908EDB63E339293E94%20%282%29.torrent";
$content = vget($url);
if($content){
    file_put_contents("../torrent/{$infohash}.torrent", $content);
    echo json_encode(['download_url' => "/torrent/{$infohash}.torrent"]);
}else{
    echo json_encode(['error' => "no torrent"]);
}


function vget($url){ // 模拟获取内容函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_MAXREDIRS , 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        //echo 'Errno'.curl_error($curl);
        return false;
    }
    $mime = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close($curl); // 关闭CURL会话
    if($mime == "application/x-bittorrent" || $mime == "application/octet-stream"){
        return $tmpInfo; // 返回数据
    }else{
        return false;
    }
}
