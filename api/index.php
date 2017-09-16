<?php
require 'BEncode.php';

$bcoder = new Bhutanio\BEncode\BEncode;
header("Access-Control-Allow-Origin: http://127.0.0.1:3000");

if(!isset($_FILES["file"])){
	die('please select a torrent file');
}

if ($_FILES["file"]["error"] > 0){
   die('error');
}else{
  if($_FILES["file"]["type"] == "application/x-bittorrent"){
  	if($_FILES["file"]["size"] > 1024*1024*2){
  	  die('只能上传 2M 以内的文件');
  	}
  	move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
  	$filePath = "./upload/" . $_FILES["file"]["name"];
  	$torrentInfo = getTorrentInfo($filePath);
  	header('Content-type: application/json');
  	echo json_encode($torrentInfo);
  }else{
  	die('please select a torrent file2');
  }
}

function getTorrentInfo($path){
	global $bcoder;
	$torrent = $bcoder->bdecode(file_get_contents($path));

    $result['infohash'] = sha1($bcoder->bencode($torrent["info"]));
    $result['file-name'] = substr($path, 9);
	$result['name'] = $torrent['info']['name'];
	$result['comment'] = $torrent['comment'];
	$result['publisher'] = $torrent['info']['publisher'];
	$result['created-by'] = $torrent['created by'];
	$result['created-at'] = $torrent['creation date'];
	$result['announce'] = $torrent['announce'];
	$result['announce-list'] = [];
	foreach ($torrent['announce-list'] as $value) {
		array_push($result['announce-list'], $value[0]);
	}

	$result['files'] = $bcoder->filelist( $torrent );
	return $result;
}
