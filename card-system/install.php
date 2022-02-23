<?php

function writeFileByUrl($url, $filepath) {
	$file = @file_get_contents($url);
	if (!$file) {
		showLog("{$url} 下载失败");
		return false;
	}
	if (!file_put_contents($filepath, $file)) {
		showLog("{$filepath} 写入失败");
		return false;	
	}
	showLog("{$filepath} 下载完毕");
	return true;
}

function showLog($text) {
	$date = date('Y-m-d H:i:s');
	echo "[$date]" . $text . PHP_EOL;
}


$gatewayDir = "app/Library/Gateway/Pay/MGate";



if (!is_dir($gatewayDir)) {
	mkdir($gatewayDir, 0755, true);
}

$baseUrl = "https://raw.githubusercontent.com/mgate/mgate-sdk/master/card-system/resources";

$files = [
	"Api.php"
];

showLog("SDK部署中...");

foreach ($files as $file) {
	if (!writeFileByUrl("{$baseUrl}/{$file}", "{$gatewayDir}/{$file}")) {
		$fail = true;
		break;
	}
}

if ($fail) {
	showLog("安装失败");
} else {
	showLog("安装成功");
}